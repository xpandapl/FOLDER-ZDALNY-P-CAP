<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
        });

        // Convert existing name data to first_name and last_name
        $this->convertExistingData();
    }

    /**
     * Convert existing name data to first_name and last_name
     */
    private function convertExistingData(): void
    {
        $employees = Employee::whereNotNull('name')->get();
        
        foreach ($employees as $employee) {
            $nameParts = $this->splitName($employee->name);
            
            $employee->update([
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name']
            ]);
        }
    }

    /**
     * Split full name into first and last name
     */
    private function splitName(string $fullName): array
    {
        $fullName = trim($fullName);
        
        // Handle special cases
        $specialCases = [
            'Tester' => ['first_name' => 'Tester', 'last_name' => ''],
            'Admin' => ['first_name' => 'Admin', 'last_name' => ''],
            'Test' => ['first_name' => 'Test', 'last_name' => ''],
        ];
        
        if (array_key_exists($fullName, $specialCases)) {
            return $specialCases[$fullName];
        }
        
        // Split by space
        $parts = explode(' ', $fullName);
        
        if (count($parts) === 1) {
            // Only one word - assume it's first name
            return [
                'first_name' => $parts[0],
                'last_name' => ''
            ];
        } elseif (count($parts) === 2) {
            // Two words - first is first name, second is last name
            return [
                'first_name' => $parts[0],
                'last_name' => $parts[1]
            ];
        } else {
            // More than two words - first is first name, rest is last name
            return [
                'first_name' => $parts[0],
                'last_name' => implode(' ', array_slice($parts, 1))
            ];
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};