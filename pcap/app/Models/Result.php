<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'competency_id',
        'score',
        'above_expectations',
        'comments',
        'score_manager',
        'above_expectations_manager',
        'feedback_manager',
    ];

    // Relationships
    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
}
