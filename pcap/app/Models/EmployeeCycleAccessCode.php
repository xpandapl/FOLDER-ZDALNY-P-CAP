<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EmployeeCycleAccessCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'cycle_id', 'code_hash', 'raw_last4', 'encrypted_full_code', 'expires_at'
    ];

    protected $dates = ['expires_at'];

    public function employee(){ return $this->belongsTo(Employee::class); }
    public function cycle(){ return $this->belongsTo(AssessmentCycle::class); }
    
    /**
     * Zapisz pełny kod w formie zaszyfrowanej
     */
    public function setFullCode($code)
    {
        $this->encrypted_full_code = Crypt::encrypt($code);
        $this->raw_last4 = substr($code, -4);
    }
    
    /**
     * Pobierz odszyfrowany pełny kod
     */
    public function getFullCode()
    {
        if (!$this->encrypted_full_code) {
            return null;
        }
        
        try {
            return Crypt::decrypt($this->encrypted_full_code);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Sprawdź czy kod ma zapisany pełny kod
     */
    public function hasFullCode()
    {
        return !empty($this->encrypted_full_code);
    }
}
