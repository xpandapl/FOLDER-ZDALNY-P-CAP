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
        'department',
        'manager_username',
        'job_title',
        'uuid',
    ];

    public function results()
    {
        return $this->hasMany(Result::class);
    }


    public function team()
    {
        return $this->belongsTo(Team::class, 'department', 'name');
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

}