<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\HierarchyStructure;
use App\Models\User;
use App\Models\Employee;

class CleanAndMigrateLegacy extends Command
{
    protected $signature = 'hierarchy:clean-migrate {--clean : Usuń legacy struktury przed migracją}';
    protected $description = 'Czyści i ponownie migruje legacy dane';

    public function handle()
    {
        if ($this->option('clean')) {
            $this->info('🧹 Usuwam legacy struktury...');
            HierarchyStructure::where('team_name', 'Legacy Team')->delete();
            $this->info('✅ Legacy struktury usunięte');
        }

        $this->info('🔄 Rozpoczynam ulepszoną migrację...');

        // Pobierz wszystkie departamenty z unikalną listą managerów
        $departmentManagers = DB::table('employees')
            ->select('department')
            ->whereNotNull('manager_username')
            ->where('manager_username', '!=', '')
            ->groupBy('department')
            ->get();

        foreach ($departmentManagers as $dept) {
            $department = $dept->department;
            
            // Sprawdź czy już istnieje jakakolwiek struktura dla tego departamentu
            $existingStructure = HierarchyStructure::where('department', $department)->first();
            
            if ($existingStructure) {
                $this->line("⏭️  Departament {$department} już ma strukturę - pomijam");
                continue;
            }

                        // Znajdź głównego managera/heada dla departamentu
            $head = User::whereIn('role', ['head', 'supermanager'])->where('department', $department)->first();
            $manager = User::whereIn('role', ['manager', 'supermanager'])->where('department', $department)->first();

            if (!$head && !$manager) {
                $this->warn("❌ Brak head/manager/supermanager dla {$department} - pomijam");
                continue;
            }

            // Ustal kto będzie managerem i headem
            $managerUsername = null;
            $headUsername = null;

            if ($head) {
                $headUsername = $head->username;
                if ($manager && $manager->username !== $head->username) {
                    $managerUsername = $manager->username;
                } else {
                    // Head (lub supermanager) pełni także rolę managera
                    $managerUsername = $head->username;
                }
            } else if ($manager) {
                $managerUsername = $manager->username;
                // Szukaj heada w innych departamentach lub supermanagera
                $fallbackHead = User::whereIn('role', ['supermanager', 'head'])->first();
                $headUsername = $fallbackHead ? $fallbackHead->username : $manager->username;
            }

            try {
                HierarchyStructure::create([
                    'department' => $department,
                    'team_name' => 'Main Team',
                    'supervisor_username' => null,
                    'manager_username' => $managerUsername,
                    'head_username' => $headUsername,
                ]);

                $this->info("✅ Utworzono strukturę dla {$department}: Manager={$managerUsername}, Head={$headUsername}");
            } catch (\Exception $e) {
                $this->error("❌ Błąd dla {$department}: " . $e->getMessage());
            }
        }

        // Teraz zaktualizuj wszystkich pracowników
        $this->info('📝 Aktualizuję przypisania pracowników...');
        
        $employees = Employee::whereNotNull('manager_username')
            ->where('manager_username', '!=', '')
            ->get();

        $updated = 0;
        foreach ($employees as $employee) {
            $structure = HierarchyStructure::where('department', $employee->department)->first();
            
            if ($structure) {
                $employee->update([
                    'supervisor_username' => $structure->supervisor_username,
                    'manager_username' => $structure->manager_username,
                    'head_username' => $structure->head_username,
                ]);
                $updated++;
            }
        }

        $this->info("✅ Zaktualizowano {$updated} pracowników");
        
        return 0;
    }
}