<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // Backwards compatibility - will be deprecated
        'first_name',
        'last_name',
        'email',
        'active',
        'department',
        'manager_username',
        'supervisor_username',  // Bezpośredni przełożony
        'head_username',        // Head w hierarchii
        'job_title',
        'uuid',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function results()
    {
        return $this->hasMany(Result::class);
    }


    public function team()
    {
        return $this->belongsTo(Team::class, 'department', 'name');
    }

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

    public function overriddenCompetencyValues()
{
    return $this->hasMany(EmployeeCompetencyValue::class);
}


    public function competencyValues()
    {
        return $this->hasMany(EmployeeCompetencyValue::class);
    }
    
    public function getCompetencyValue($competencyId)
    {
        // Check for overridden competency value
        $overriddenValue = $this->overriddenCompetencyValues->firstWhere('competency_id', $competencyId);

        if ($overriddenValue) {
            return $overriddenValue->value;
        }

        // Get team competency value
        if ($this->team) {
            $competencyTeamValue = $this->team->competencyTeamValues->firstWhere('competency_id', $competencyId);
            if ($competencyTeamValue) {
                return $competencyTeamValue->value;
            }
        }

        return 0; // Default value if none found
    }




    protected $appends = ['level', 'percentage', 'full_name'];

    public function getLevelAttribute()
    {
        return $this->attributes['level'] ?? 'Brak danych';
    }

    public function getPercentageAttribute()
    {
        return $this->attributes['percentage'] ?? 0;
    }

    // Full name accessor - handles both new and old format
    public function getFullNameAttribute()
    {
        if (!empty($this->first_name) && !empty($this->last_name)) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        
        // Fallback to old 'name' field for backwards compatibility
        return $this->name ?? '';
    }

    // Name accessor for backwards compatibility
    public function getNameAttribute()
    {
        // If we have the old 'name' field, return it
        if (!empty($this->attributes['name'])) {
            return $this->attributes['name'];
        }
        
        // Otherwise, construct from first_name and last_name
        return $this->getFullNameAttribute();
    }

    // Zwraca pełną hierarchię jako string
    public function getHierarchyAttribute()
    {
        $parts = [];
        if ($this->supervisor) $parts[] = "Supervisor: {$this->supervisor->name}";
        if ($this->manager) $parts[] = "Manager: {$this->manager->name}";
        if ($this->head) $parts[] = "Head: {$this->head->name}";
        
        return implode(' → ', $parts);
    }

    // Zwraca relację pracownika do managera
    public function getRelationshipToManager($manager)
    {
        // Bezpośredni podwładni na różnych poziomach
        if ($this->supervisor_username == $manager->username) {
            return ['type' => 'direct', 'role' => 'supervisor'];
        }
        
        if ($this->manager_username == $manager->username) {
            return ['type' => 'direct', 'role' => 'manager'];
        }
        
        if ($this->head_username == $manager->username) {
            return ['type' => 'direct', 'role' => 'head'];
        }
        
        // Pośrednie relacje
        if ($manager->role == 'manager') {
            // Manager widzi pracowników swoich supervisorów
            if ($this->supervisor_username && $this->supervisor_username != $manager->username) {
                // Sprawdź czy supervisor jest pod tym managerem
                $supervisor = User::where('username', $this->supervisor_username)->first();
                if ($supervisor) {
                    $supervisorEmployee = Employee::where('manager_username', $manager->username)
                                                 ->where(function($q) use ($supervisor) {
                                                     $q->where('name', $supervisor->name)
                                                       ->orWhere('supervisor_username', $supervisor->username);
                                                 })
                                                 ->first();
                    if ($supervisorEmployee || 
                        \App\Models\HierarchyStructure::where('supervisor_username', $this->supervisor_username)
                                                      ->where('manager_username', $manager->username)
                                                      ->exists()) {
                        return ['type' => 'indirect', 'through' => 'supervisor'];
                    }
                }
            }
        }
        
        if ($manager->role == 'head') {
            // Head widzi pracowników przez managerów i supervisorów w swojej hierarchii
            if ($this->manager_username && $this->manager_username != $manager->username) {
                // Sprawdź czy manager jest pod tym headem
                if (\App\Models\HierarchyStructure::where('manager_username', $this->manager_username)
                                                  ->where('head_username', $manager->username)
                                                  ->exists()) {
                    return ['type' => 'indirect', 'through' => 'manager'];
                }
            }
            if ($this->supervisor_username && $this->supervisor_username != $manager->username) {
                // Sprawdź czy supervisor jest pod tym headem
                if (\App\Models\HierarchyStructure::where('supervisor_username', $this->supervisor_username)
                                                  ->where('head_username', $manager->username)
                                                  ->exists()) {
                    return ['type' => 'indirect', 'through' => 'supervisor'];
                }
            }
        }
        
        return ['type' => 'none'];
    }

    // Sprawdza czy pracownik ma przypisaną pełną hierarchię
    public function hasCompleteHierarchy()
    {
        return $this->supervisor_username && $this->manager_username && $this->head_username;
    }

    // Zwraca najwyższy poziom przypisanego przełożonego
    public function getHighestAssignedLevel()
    {
        if ($this->head_username) return 'head';
        if ($this->manager_username) return 'manager';
        if ($this->supervisor_username) return 'supervisor';
        return null;
    }

}