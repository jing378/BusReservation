<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

abstract class Controller
{
    /**
     * Run a read query and return plain associative arrays, which is the row shape the views expect.
     *
     * Numbers are returned as strings, like the original app's mysqli queries. The views' JavaScript
     * relies on that, e.g. `trip.id === select.value` when choosing a trip in the booking form.
     *
     * @param  array<int|string, mixed>  $bindings
     * @return list<array<string, string|null>>
     */
    protected function rows(string $sql, array $bindings = []): array
    {
        return array_map(
            fn (object $row): array => array_map(
                fn (mixed $value): ?string => $value === null ? null : (string) $value,
                (array) $row,
            ),
            DB::select($sql, $bindings),
        );
    }

    /**
     * Read a single scalar value (e.g. a COUNT or SUM).
     *
     * @param  array<int|string, mixed>  $bindings
     */
    protected function value(string $sql, array $bindings = []): mixed
    {
        return DB::scalar($sql, $bindings);
    }
}
