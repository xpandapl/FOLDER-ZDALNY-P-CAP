<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;

class SplitEmployeeNames extends Command
{
    protected $signature = 'employees:split-names {--dry-run : Show what would be changed without making changes}';
    protected $description = 'Split employee names into first_name and last_name fields';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $employees = Employee::whereNotNull('name')
            ->where(function($query) {
                $query->whereNull('first_name')
                      ->orWhereNull('last_name')
                      ->orWhere('first_name', '')
                      ->orWhere('last_name', '');
            })
            ->get();

        if ($employees->isEmpty()) {
            $this->info('No employees found that need name splitting.');
            return;
        }

        $this->info("Found {$employees->count()} employees to process:");

        $specialCases = [
            'Tester' => ['first_name' => 'Tester', 'last_name' => ''],
            'Admin' => ['first_name' => 'Admin', 'last_name' => ''],
            'Test' => ['first_name' => 'Test', 'last_name' => ''],
            'TestUser' => ['first_name' => 'Test', 'last_name' => 'User'],
        ];

        foreach ($employees as $employee) {
            $originalName = $employee->name;
            $nameParts = $this->splitName($originalName, $specialCases);
            
            $this->line("\nEmployee ID: {$employee->id}");
            $this->line("Original name: '{$originalName}'");
            $this->line("Split to: First='{$nameParts['first_name']}', Last='{$nameParts['last_name']}'");
            
            if (!$dryRun) {
                $employee->update([
                    'first_name' => $nameParts['first_name'],
                    'last_name' => $nameParts['last_name']
                ]);
                $this->info('✓ Updated');
            } else {
                $this->comment('○ Would update (dry-run mode)');
            }
        }

        if ($dryRun) {
            $this->info("\nDry run completed. Use without --dry-run to apply changes.");
        } else {
            $this->info("\nName splitting completed for {$employees->count()} employees.");
        }
    }

    private function splitName(string $fullName, array $specialCases): array
    {
        $fullName = trim($fullName);
        
        // Handle special cases
        if (array_key_exists($fullName, $specialCases)) {
            return $specialCases[$fullName];
        }

        // Check for similar special cases (case-insensitive)
        foreach ($specialCases as $special => $parts) {
            if (strcasecmp($fullName, $special) === 0) {
                return $parts;
            }
        }
        
        // Split by space
        $parts = preg_split('/\s+/', $fullName);
        $parts = array_filter($parts); // Remove empty parts
        
        if (count($parts) === 0) {
            return ['first_name' => '', 'last_name' => ''];
        } elseif (count($parts) === 1) {
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
}