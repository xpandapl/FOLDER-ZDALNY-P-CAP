<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\HierarchyStructure;
use App\Models\User;
use App\Models\Employee;

class MigrateOldManagerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hierarchy:migrate-old-data {--dry-run : Tylko pokaż co zostanie zrobione}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migruje stare dane managerów do nowego systemu hierarchii';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 TRYB PODGLĄDU - żadne zmiany nie zostaną zapisane');
        }

        $this->info('Analizuję stare dane managerów...');

        // Pobierz wszystkich unikalnych managerów z tabeli employees
        $oldManagers = DB::table('employees')
            ->select('manager_username', 'department')
            ->whereNotNull('manager_username')
            ->where('manager_username', '!=', '')
            ->groupBy('manager_username', 'department')
            ->get();

        $this->info("Znaleziono {$oldManagers->count()} unikalnych przypisań manager-departament");

        $createdStructures = 0;
        $skippedStructures = 0;
        $updatedEmployees = 0;

        // Tworzenie struktur hierarchicznych
        foreach ($oldManagers as $manager) {
            $managerUsername = $manager->manager_username;
            $department = $manager->department;

            // Sprawdź czy manager istnieje
            $managerUser = User::where('name', $managerUsername)->first();
            if (!$managerUser) {
                $this->warn("❌ Manager {$managerUsername} nie istnieje w tabeli users");
                $skippedStructures++;
                continue;
            }

            // Sprawdź czy już istnieje struktura
            $existingStructure = HierarchyStructure::where('department', $department)
                ->where('manager_username', $managerUser->username) // używamy username z users
                ->first();

            if ($existingStructure) {
                $this->line("⏭️  Struktura dla {$managerUser->username} ({$managerUsername}) w {$department} już istnieje");
                $skippedStructures++;
                continue;
            }

            // Znajdź heada
            $head = User::where('role', 'head')
                ->where('department', $department)
                ->first();
            
            if (!$head) {
                $head = User::where('role', 'supermanager')->first();
            }

            // Jeśli manager to ten sam co head, nie ustawiaj head_username
            $headUsername = null;
            if ($head && $head->username !== $managerUser->username) {
                $headUsername = $head->username;
            }

            if ($dryRun) {
                $this->line("🔄 Utworzyłbym strukturę: {$department} -> Manager: {$managerUser->username} ({$managerUsername}), Head: " . ($headUsername ? $headUsername : 'brak (manager=head)'));
                $createdStructures++;
            } else {
                // Utwórz strukturę
                try {
                    HierarchyStructure::create([
                        'department' => $department,
                        'team_name' => 'Legacy Team',
                        'supervisor_username' => null,
                        'manager_username' => $managerUser->username, // używamy username
                        'head_username' => $headUsername,
                    ]);
                    
                    $this->info("✅ Utworzono strukturę dla {$managerUser->username} ({$managerUsername}) w {$department}");
                    $createdStructures++;
                } catch (\Exception $e) {
                    $this->error("❌ Błąd przy tworzeniu struktury dla {$managerUsername}: " . $e->getMessage());
                    $skippedStructures++;
                }
            }
        }

        // Aktualizacja pracowników
        $this->info('Aktualizuję przypisania pracowników...');

        $employeeAssignments = DB::table('employees')
            ->select('id', 'manager_username', 'department')
            ->whereNotNull('manager_username')
            ->where('manager_username', '!=', '')
            ->get();

        foreach ($employeeAssignments as $assignment) {
            // Znajdź username managera na podstawie jego nazwy
            $managerUser = User::where('name', $assignment->manager_username)->first();
            if (!$managerUser) {
                continue;
            }

            $hierarchy = HierarchyStructure::where('department', $assignment->department)
                ->where('manager_username', $managerUser->username)
                ->first();

            if ($hierarchy) {
                if ($dryRun) {
                    $this->line("🔄 Zaktualizowałbym pracownika ID {$assignment->id} (manager: {$managerUser->username})");
                    $updatedEmployees++;
                } else {
                    Employee::where('id', $assignment->id)
                        ->update([
                            'supervisor_username' => $hierarchy->supervisor_username,
                            'manager_username' => $managerUser->username, // zmień na username
                            'head_username' => $hierarchy->head_username,
                        ]);
                    $updatedEmployees++;
                }
            }
        }

        // Podsumowanie
        $this->info('📊 PODSUMOWANIE:');
        if ($dryRun) {
            $this->info("Zostałyby utworzone struktury: {$createdStructures}");
            $this->info("Zostałyby zaktualizowani pracownicy: {$updatedEmployees}");
            $this->info("Pominięte/błędy: {$skippedStructures}");
            $this->info('');
            $this->info('Aby wykonać migrację, uruchom: php artisan hierarchy:migrate-old-data');
        } else {
            $this->info("Utworzone struktury: {$createdStructures}");
            $this->info("Zaktualizowani pracownicy: {$updatedEmployees}");
            $this->info("Pominięte/błędy: {$skippedStructures}");
        }

        return 0;
    }
}