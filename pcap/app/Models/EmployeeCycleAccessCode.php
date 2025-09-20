<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCycleAccessCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'cycle_id', 'code_hash', 'raw_last4', 'expires_at'
    ];

    protected $dates = ['expires_at'];

    public function employee(){ return $this->belongsTo(Employee::class); }
    public function cycle(){ return $this->belongsTo(AssessmentCycle::class); }
}
