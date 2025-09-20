<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'year', 'period', 'label', 'is_active', 'locked_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function results()
    {
        return $this->hasMany(Result::class, 'cycle_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function getDisplayNameAttribute()
    {
        return $this->label ?? ($this->year . ($this->period ? ' H' . $this->period : ''));
    }
}
