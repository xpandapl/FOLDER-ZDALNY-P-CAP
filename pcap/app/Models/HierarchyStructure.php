<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HierarchyStructure extends Model
{
    use HasFactory;

    protected $table = 'hierarchy_structure';
    
    protected $fillable = [
        'department',
        'team_name',
        'supervisor_username',
        'manager_username', 
        'head_username'
    ];

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_username', 'username');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_username', 'username');
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_username', 'username');
    }

    // Zwraca wszystkich supervisorów dla danego działu
    public static function getSupervisorsForDepartment($department)
    {
        return self::where('department', $department)
                   ->with(['supervisor:username,name'])
                   ->get()
                   ->pluck('supervisor.name', 'supervisor_username')
                   ->unique();
    }

    // Automatyczne przypisanie hierarchii dla pracownika na podstawie wybranego supervisora/managera
    public static function assignHierarchyByManager($employee, $selectedManager)
    {
        $department = $employee->department;
        
        // Znajdź hierarchię gdzie dany użytkownik jest supervisorem
        $hierarchy = self::where('department', $department)
                         ->where('supervisor_username', $selectedManager)
                         ->first();
        
        // Jeśli nie znaleziono, sprawdź czy to manager w strukturze bez supervisora
        if (!$hierarchy) {
            $hierarchy = self::where('department', $department)
                           ->where('manager_username', $selectedManager)
                           ->whereNull('supervisor_username')
                           ->first();
        }
        
        // Jeśli nie znaleziono, sprawdź czy to head (bezpośrednie podległość)
        if (!$hierarchy) {
            $hierarchy = self::where('department', $department)
                           ->where('head_username', $selectedManager)
                           ->first();
        }
        
        if ($hierarchy) {
            $employee->supervisor_username = $hierarchy->supervisor_username;
            $employee->manager_username = $hierarchy->manager_username;
            $employee->head_username = $hierarchy->head_username;
            $employee->save();
            
            return true;
        }
        
        return false;
    }

    // Automatyczne przypisanie hierarchii dla pracownika
    public static function assignHierarchyToEmployee($employee, $hierarchyId)
    {
        $hierarchy = self::find($hierarchyId);
        
        if ($hierarchy) {
            $employee->supervisor_username = $hierarchy->supervisor_username;
            $employee->manager_username = $hierarchy->manager_username;
            $employee->head_username = $hierarchy->head_username;
            $employee->save();
            
            return true;
        }
        
        return false;
    }

    // Przypisanie częściowej hierarchii (np. bezpośrednio pod managerem/headem)
    public static function assignPartialHierarchy($employee, $selectedManager, $managerRole)
    {
        switch ($managerRole) {
            case 'supervisor':
                $employee->supervisor_username = $selectedManager;
                break;
            case 'manager':
                $employee->manager_username = $selectedManager;
                break;
            case 'head':
                $employee->head_username = $selectedManager;
                break;
        }
        
        $employee->save();
        return true;
    }

    // Zwraca pełną hierarchię dla danego supervisora lub managera (gdy brak supervisora)
    public static function getHierarchyBySupervisor($department, $supervisorUsername)
    {
        // Sprawdź najpierw czy to supervisor
        $hierarchy = self::where('department', $department)
                         ->where('supervisor_username', $supervisorUsername)
                         ->with(['supervisor:username,name', 'manager:username,name', 'head:username,name'])
                         ->first();
        
        // Jeśli nie znaleziono, sprawdź czy to manager z struktury bez supervisora
        if (!$hierarchy) {
            $hierarchy = self::where('department', $department)
                           ->where('manager_username', $supervisorUsername)
                           ->whereNull('supervisor_username') // Struktura bez supervisora
                           ->with(['supervisor:username,name', 'manager:username,name', 'head:username,name'])
                           ->first();
        }
        
        return $hierarchy;
    }

    // Sprawdza czy supervisor już istnieje w danym dziale
    public static function supervisorExistsInDepartment($department, $supervisorUsername, $excludeId = null)
    {
        $query = self::where('department', $department)
                    ->where('supervisor_username', $supervisorUsername);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    // Zwraca liczbę przypisanych pracowników
    public function getEmployeesCountAttribute()
    {
        return Employee::where('supervisor_username', $this->supervisor_username)->count();
    }

    // Zwraca wszystkich pracowników przypisanych do tego supervisora
    public function employees()
    {
        return Employee::where('supervisor_username', $this->supervisor_username)->get();
    }
}