<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name',
        'department',
        'competency_id',
        'score',
    ];

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }
}

