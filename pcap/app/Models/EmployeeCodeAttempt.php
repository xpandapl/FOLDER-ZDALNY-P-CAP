<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCodeAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id','cycle_id','ip_hash','attempts','locked_until'
    ];

    protected $dates = ['locked_until'];

    public function employee(){ return $this->belongsTo(Employee::class); }
    public function cycle(){ return $this->belongsTo(AssessmentCycle::class); }
}
