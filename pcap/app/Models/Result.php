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

    // Dodaj właściwość $casts, aby zapewnić poprawne typy danych
    protected $casts = [
        'score_manager' => 'float',
        'above_expectations_manager' => 'boolean',
        // Możesz dodać inne atrybuty, jeśli to konieczne
    ];

    // Relacje
    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
