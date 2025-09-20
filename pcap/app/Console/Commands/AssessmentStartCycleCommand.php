<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssessmentCycle;
use App\Models\Competency;
use App\Models\Employee;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class AssessmentStartCycleCommand extends Command
{
    protected $signature = 'assessment:start-cycle {year} {--period=} {--label=} {--activate} {--clone-from=}';
    protected $description = 'Start a new assessment cycle (optionally cloning parent_result links from a previous cycle).';

    public function handle(): int
    {
        $year = (int)$this->argument('year');
        $period = $this->option('period') !== null ? (int)$this->option('period') : null;
        $label = $this->option('label') ?: ($year . ($period ? ' H'.$period : ''));
        $activate = $this->option('activate');
        $cloneFromYear = $this->option('clone-from');

        DB::beginTransaction();
        try {
            if ($activate) {
                AssessmentCycle::where('is_active', true)->update(['is_active' => false, 'locked_at' => now()]);
            }

            $cycle = AssessmentCycle::firstOrCreate([
                'year' => $year,
                'period' => $period,
            ], [
                'label' => $label,
                'is_active' => (bool)$activate,
            ]);

            $this->info("Cycle {$cycle->display_name} (ID {$cycle->id}) created.");

            // Optionally clone structure for employees
            if ($cloneFromYear) {
                $prev = AssessmentCycle::where('year', $cloneFromYear)->orderByDesc('period')->first();
                if (!$prev) {
                    $this->warn('Previous cycle (year='.$cloneFromYear.') not found. Skipping cloning.');
                } else {
                    $this->cloneResults($prev, $cycle);
                }
            }

            DB::commit();
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function cloneResults(AssessmentCycle $prev, AssessmentCycle $current): void
    {
        $this->info('Cloning results skeleton from cycle '.$prev->display_name.' to '.$current->display_name.' ...');

        $employees = Employee::select('id')->get();
        $competencies = Competency::select('id')->get();

        // Preload previous results grouped by employee+competency for fast lookup
        $prevResults = Result::where('cycle_id', $prev->id)
            ->select('id','employee_id','competency_id')
            ->get()
            ->groupBy('employee_id');

        $insertBatch = [];
        $now = now();
        foreach ($employees as $emp) {
            $empPrev = $prevResults->get($emp->id, collect())->keyBy('competency_id');
            foreach ($competencies as $comp) {
                $parent = $empPrev->get($comp->id);
                $insertBatch[] = [
                    'employee_id' => $emp->id,
                    'competency_id' => $comp->id,
                    'cycle_id' => $current->id,
                    'parent_result_id' => $parent?->id,
                    'score' => null,
                    'above_expectations' => 0,
                    'comments' => null,
                    'score_manager' => null,
                    'above_expectations_manager' => 0,
                    'feedback_manager' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($insertBatch) >= 1000) {
                    Result::insert($insertBatch);
                    $insertBatch = [];
                }
            }
        }
        if ($insertBatch) {
            Result::insert($insertBatch);
        }
        $this->info('Cloning completed.');
    }
}
