<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;

class FixManagerData extends Command
{
    protected $signature = 'hierarchy:fix-data';
    protected $description = 'Ręczna korekta problemowych przypadków w danych managerów';

    public function handle()
    {
        $this->info('🔧 Korekta problemowych przypadków...');

        // 1. Usuń niepoprawne przypisania gdzie manager nie istnieje
        $invalidEmployees = Employee::whereNotNull('manager_username')
            ->whereNotIn('manager_username', function($query) {
                $query->select('username')->from('users');
            })
            ->get();

        $this->info("Znaleziono {$invalidEmployees->count()} pracowników z nieprawidłowymi managerami:");

        foreach ($invalidEmployees as $employee) {
            $this->line("- Pracownik ID {$employee->id}: {$employee->first_name} {$employee->last_name} -> manager: {$employee->manager_username}");
            
            if ($this->confirm("Czy usunąć przypisanie managera dla tego pracownika?")) {
                // Znajdź jakąś strukturę hierarchiczną dla tego departamentu
                $fallbackStructure = \App\Models\HierarchyStructure::where('department', $employee->department)->first();
                
                if ($fallbackStructure) {
                    $employee->manager_username = $fallbackStructure->manager_username;
                    $employee->supervisor_username = $fallbackStructure->supervisor_username;
                    $employee->head_username = $fallbackStructure->head_username;
                    $employee->save();
                    $this->info("✅ Przypisano do struktury: Manager={$fallbackStructure->manager_username}");
                } else {
                    $this->warn("⚠️  Brak struktury dla departamentu {$employee->department} - pomijam");
                }
            }
        }

        // 2. Sprawdź duplikaty managerów w różnych departamentach
        $duplicateManagers = Employee::select('manager_username', 'department')
            ->whereNotNull('manager_username')
            ->groupBy('manager_username', 'department')
            ->havingRaw('COUNT(*) > 0')
            ->get()
            ->groupBy('manager_username')
            ->filter(function($departments) {
                return $departments->count() > 1;
            });

        if ($duplicateManagers->count() > 0) {
            $this->info("\n🔍 Managerowie w wielu departamentach:");
            foreach ($duplicateManagers as $managerUsername => $departments) {
                $this->line("Manager {$managerUsername} w departamentach: " . $departments->pluck('department')->implode(', '));
            }
        }

        // 3. Pokaż listę wszystkich managerów do weryfikacji
        $this->info("\n📋 Lista wszystkich managerów w systemie:");
        $managers = User::whereIn('role', ['manager', 'head', 'supervisor', 'supermanager'])->get();
        
        foreach ($managers as $manager) {
            $employeeCount = Employee::where('manager_username', $manager->username)->count();
            $this->line("- {$manager->username} ({$manager->name}) - {$manager->role} w {$manager->department} - {$employeeCount} pracowników");
        }

        $this->info("\n✅ Korekta zakończona");
        return 0;
    }

    private function getRoleDescription($role)
    {
        return match($role) {
            'supervisor' => 'supervisor (widzi podzespół)',
            'manager' => 'manager (widzi zespół i podzespoły)',
            'head' => 'head (widzi dział, zespoły i podzespoły)',
            'supermanager' => 'supermanager (HR - widzi całą firmę, może być liderem)',
            default => $role
        };
    }
}