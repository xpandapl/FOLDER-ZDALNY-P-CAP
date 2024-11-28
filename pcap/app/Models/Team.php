<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function competencies()
    {
        return $this->belongsToMany(Competency::class, 'competency_team_values')->withPivot('value');
    }
    public function employees()
    {
        return $this->hasMany(Employee::class, 'department', 'name');
    }
    public function competencyTeamValues()
    {
        return $this->hasMany(CompetencyTeamValue::class);
    }


}
