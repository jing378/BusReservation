<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('bus:import-legacy
    {--database=busreservation : Name of the old (plain PHP) MySQL database on the same server}
    {--force : Replace existing data without asking}')]
#[Description('Copy users, fleet, routes, trips, bookings and payments from the old BusReservationSystem database')]
class ImportLegacyData extends Command
{
    /** @var list<string> Import order respects foreign keys. */
    private const TABLES = ['users', 'buses', 'routes', 'trips', 'bookings', 'payments', 'bookings_archive'];

    public function handle(): int
    {
        config(['database.connections.legacy' => array_merge(
            config('database.connections.'.config('database.default')),
            ['database' => $this->option('database')],
        )]);

        $legacy = DB::connection('legacy');

        try {
            $legacy->getPdo();
        } catch (\Throwable $exception) {
            $this->error('Cannot connect to the legacy database: '.$exception->getMessage());

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('This replaces all users, buses, routes, trips, bookings and payments in '
            .config('database.connections.'.config('database.default').'.database').'. Continue?')) {
            return self::FAILURE;
        }

        Schema::disableForeignKeyConstraints();

        foreach (array_reverse(self::TABLES) as $table) {
            DB::table($table)->truncate();
        }

        foreach (self::TABLES as $table) {
            if (! $legacy->getSchemaBuilder()->hasTable($table)) {
                $this->warn("Skipping {$table}: not found in legacy database.");

                continue;
            }

            $count = 0;

            $legacy->table($table)->orderBy('id')->chunk(500, function ($rows) use ($table, &$count) {
                $records = $rows->map(fn (object $row): array => $this->transform($table, (array) $row))->all();
                DB::table($table)->insert($records);
                $count += count($records);
            });

            $this->info(str_pad($table, 18).$count.' rows');
        }

        // Trips were inserted as "scheduled" so the booking trigger accepted their bookings; restore real statuses.
        $legacy->table('trips')->get(['id', 'status'])->groupBy('status')->each(
            fn ($trips, $status) => DB::table('trips')->whereIn('id', $trips->pluck('id'))->update(['status' => $status])
        );

        Schema::enableForeignKeyConstraints();

        $this->info('Import complete. Existing passwords keep working (same bcrypt hashes).');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function transform(string $table, array $row): array
    {
        return match ($table) {
            'users' => [
                'id' => $row['id'],
                'full_name' => $row['full_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'username' => $row['username'],
                'password' => $row['password_hash'],
                'reset_token' => $row['reset_token'],
                'reset_token_expiry' => $row['reset_token_expiry'],
                'role' => $row['role'],
                'user_type' => $row['user_type'],
                'newsletter_subscription' => $row['newsletter_subscription'],
                'is_active' => $row['is_active'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ],
            'trips' => ['status' => 'scheduled'] + $row,
            'bookings' => array_diff_key($row, ['active_seat_check' => true]),
            default => $row,
        };
    }
}
