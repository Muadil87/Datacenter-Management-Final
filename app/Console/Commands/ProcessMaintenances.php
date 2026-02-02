<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Maintenance;
use App\Models\Resource;
use Carbon\Carbon;

class ProcessMaintenances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled maintenances - activate at start_at and deactivate at end_at';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Find maintenances that should START now or have started
        $startingMaintenances = Maintenance::where('status', 'scheduled')
            ->where('start_at', '<=', $now)
            ->where('end_at', '>', $now)
            ->get();

        foreach ($startingMaintenances as $maintenance) {
            $resource = Resource::find($maintenance->resource_id);
            if ($resource) {
                $resource->update(['state' => 'maintenance']);
                $maintenance->update(['status' => 'in_progress']);
                $this->info("✅ Maintenance started for resource: {$resource->name}");
            }
        }

        // Find maintenances that should END now
        $endingMaintenances = Maintenance::whereIn('status', ['scheduled', 'in_progress'])
            ->where('end_at', '<=', $now)
            ->get();

        foreach ($endingMaintenances as $maintenance) {
            $resource = Resource::find($maintenance->resource_id);
            if ($resource) {
                $resource->update(['state' => 'available']);
                $maintenance->update(['status' => 'completed']);
                $this->info("✅ Maintenance completed for resource: {$resource->name}");
            }
        }

        return 0;
    }
}
