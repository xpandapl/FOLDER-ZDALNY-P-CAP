<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssessmentCycle;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class AssessmentInitCommand extends Command
{
    protected $signature = 'assessment:init {--year=2024} {--label=} {--lock}';
    protected $description = 'Initialize historical cycle (e.g. 2024) and backfill existing results with cycle_id.';

    public function handle(): int
    {
        $year = (int)$this->option('year');
        $label = $this->option('label') ?: (string)$year;
        $lock = $this->option('lock');

        DB::beginTransaction();
        try {
            $cycle = AssessmentCycle::firstOrCreate([
                'year' => $year,
                'period' => null,
            ], [
                'label' => $label,
                'is_active' => false,
                'locked_at' => $lock ? now() : null,
            ]);

            $this->info("Cycle {$cycle->display_name} (ID {$cycle->id}) ready.");

            // Backfill results without cycle_id
            $affected = Result::whereNull('cycle_id')->update(['cycle_id' => $cycle->id]);
            $this->info("Backfilled {$affected} result rows with cycle_id={$cycle->id}.");

            DB::commit();
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
