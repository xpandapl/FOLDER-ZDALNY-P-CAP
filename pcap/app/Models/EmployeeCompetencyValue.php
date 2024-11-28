<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCompetencyValue extends Model
{
    protected $fillable = ['employee_id', 'competency_id', 'value'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }
}
