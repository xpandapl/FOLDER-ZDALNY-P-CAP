<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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




    protected $appends = ['level', 'percentage'];

    public function getLevelAttribute()
    {
        return $this->attributes['level'] ?? 'Brak danych';
    }

    public function getPercentageAttribute()
    {
        return $this->attributes['percentage'] ?? 0;
    }

}