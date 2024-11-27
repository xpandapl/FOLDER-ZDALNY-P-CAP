<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyTeamValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'competency_id',
        'team_id',
        'value',
    ];

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    
}
