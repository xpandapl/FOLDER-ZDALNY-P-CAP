<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Sprawdź czy tabele istnieją
        if (!Schema::hasTable('employees') || !Schema::hasTable('hierarchy_structure')) {
            echo "Brak wymaganych tabel - pomijam migrację\n";
            return;
        }

        echo "Rozpoczynam migrację starych danych managerów...\n";

        // Pobierz wszystkich unikalnych managerów z tabeli employees
        $oldManagers = DB::table('employees')
            ->select('manager_username', 'department')
            ->whereNotNull('manager_username')
            ->where('manager_username', '!=', '')
            ->groupBy('manager_username', 'department')
            ->get();

        echo "Znaleziono " . $oldManagers->count() . " unikalnych przypisań manager-departament\n";

        foreach ($oldManagers as $manager) {
            $managerUsername = $manager->manager_username;
            $department = $manager->department;

            // Sprawdź czy manager istnieje w tabeli users (szukaj po name, nie username)
            $managerUser = DB::table('users')->where('name', $managerUsername)->first();
            
            if (!$managerUser) {
                echo "UWAGA: Manager {$managerUsername} nie istnieje w tabeli users - pomijam\n";
                continue;
            }

            // Sprawdź czy już istnieje struktura hierarchiczna dla tego managera i departamentu
            $existingStructure = DB::table('hierarchy_structure')
                ->where('department', $department)
                ->where('manager_username', $managerUser->username) // używamy username z users
                ->first();

            if ($existingStructure) {
                echo "Struktura dla {$managerUser->username} ({$managerUsername}) w {$department} już istnieje - pomijam\n";
                continue;
            }

            // Sprawdź czy manager ma rolę managera lub wyższą
            $managerRoles = ['manager', 'head', 'supermanager'];
            if (!in_array($managerUser->role, $managerRoles)) {
                echo "UWAGA: {$managerUsername} nie ma roli managera (ma: {$managerUser->role}) - pomijam\n";
                continue;
            }

            // Znajdź potencjalnego heada dla tego departamentu
            $potentialHead = DB::table('users')
                ->where('role', 'head')
                ->where('department', $department)
                ->first();

            if (!$potentialHead) {
                // Sprawdź czy są supermanagerowie
                $potentialHead = DB::table('users')
                    ->where('role', 'supermanager')
                    ->first();
            }

            // Utwórz strukturę hierarchiczną
            $hierarchyData = [
                'department' => $department,
                'team_name' => 'Legacy Team', // Domyślna nazwa dla starych danych
                'supervisor_username' => null, // Stare dane nie miały supervisorów
                'manager_username' => $managerUser->username, // używamy username z users
                'head_username' => $potentialHead ? $potentialHead->username : null,
                'created_at' => now(),
                'updated_at' => now()
            ];

            try {
                DB::table('hierarchy_structure')->insert($hierarchyData);
                echo "✓ Utworzono strukturę dla {$managerUser->username} ({$managerUsername}) w {$department}\n";
            } catch (Exception $e) {
                echo "BŁĄD przy tworzeniu struktury dla {$managerUsername}: " . $e->getMessage() . "\n";
            }
        }

        echo "\nMigracja struktur zakończona. Teraz aktualizuję przypisania pracowników...\n";

        // Teraz zaktualizuj przypisania w tabeli employees na podstawie utworzonych struktur
        $employeeAssignments = DB::table('employees')
            ->select('id', 'manager_username', 'department')
            ->whereNotNull('manager_username')
            ->where('manager_username', '!=', '')
            ->get();

        foreach ($employeeAssignments as $assignment) {
            // Znajdź username managera na podstawie jego nazwy
            $managerUser = DB::table('users')->where('name', $assignment->manager_username)->first();
            if (!$managerUser) {
                echo "UWAGA: Manager {$assignment->manager_username} nie istnieje - pomijam pracownika ID {$assignment->id}\n";
                continue;
            }

            // Znajdź strukturę hierarchiczną
            $hierarchy = DB::table('hierarchy_structure')
                ->where('department', $assignment->department)
                ->where('manager_username', $managerUser->username)
                ->first();

            if ($hierarchy) {
                // Zaktualizuj pracownika
                DB::table('employees')
                    ->where('id', $assignment->id)
                    ->update([
                        'supervisor_username' => $hierarchy->supervisor_username,
                        'manager_username' => $managerUser->username, // zmień na username
                        'head_username' => $hierarchy->head_username,
                        'updated_at' => now()
                    ]);

                echo "✓ Zaktualizowano pracownika ID {$assignment->id} (manager: {$managerUser->username})\n";
            }
        }

        echo "\nMigracja zakończona pomyślnie!\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Można usunąć struktury oznaczone jako 'Legacy Team' jeśli potrzeba
        DB::table('hierarchy_structure')
            ->where('team_name', 'Legacy Team')
            ->delete();
            
        echo "Usunięto legacy struktury hierarchiczne\n";
    }
};