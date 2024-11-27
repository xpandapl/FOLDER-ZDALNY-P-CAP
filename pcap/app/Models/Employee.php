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