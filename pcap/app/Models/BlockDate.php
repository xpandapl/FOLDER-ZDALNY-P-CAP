<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockDate extends Model
{
    protected $table = 'block_dates';
    public $timestamps = false;
    
    protected $fillable = [
        'block_date',
        'block_new_submissions_date',
        'block_edits_date',
    ];
    
    protected $casts = [
        'block_date' => 'datetime',
        'block_new_submissions_date' => 'datetime',
        'block_edits_date' => 'datetime',
    ];
}