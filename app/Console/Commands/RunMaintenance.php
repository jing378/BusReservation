<?php

namespace App\Console\Commands;

use App\Services\MaintenanceService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('bus:maintenance {--force : Run the daily archive/cleanup step even if it already ran in the last 24 hours}')]
#[Description('Update trip/booking statuses and archive old completed trips and cancelled bookings')]
class RunMaintenance extends Command
{
    public function handle(MaintenanceService $maintenance): int
    {
        $status = $maintenance->updateTripStatuses();
        $result = $maintenance->runScheduled((bool) $this->option('force'));

        $this->info($status['message']);
        $this->info($result['message'] ?? 'Maintenance complete.');

        return $status['success'] ? self::SUCCESS : self::FAILURE;
    }
}
