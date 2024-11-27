<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = [
        'competency_name',
        'level',
        'competency_type',
        'description_075_to_1',
        'description_0_to_05',
        'description_above_expectations',
        'value',
    ];

    public function competencyTeamValues()
    {
        return $this->hasMany(CompetencyTeamValue::class);
    }

    public function getValueForTeam($teamId)
    {
        $ctv = $this->competencyTeamValues()->where('team_id', $teamId)->first();
        return $ctv ? $ctv->value : 0;
    }

}
