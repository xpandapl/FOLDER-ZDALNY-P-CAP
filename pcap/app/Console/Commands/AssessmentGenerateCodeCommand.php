<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\AssessmentCycle;
use App\Models\EmployeeCycleAccessCode;
use Illuminate\Support\Str;

class AssessmentGenerateCodeCommand extends Command
{
    protected $signature = 'assessment:generate-code {employee : ID, UUID lub email pracownika} {--code=} {--length=12} {--force} {--ttl= : (minuty) ważność – opcjonalnie}';
    protected $description = 'Generuje (lub nadpisuje przy --force) kod dostępu dla aktywnego cyklu samooceny.';

    public function handle(): int
    {
        $identifier = $this->argument('employee');
        $customCode = $this->option('code');
        $length = (int)$this->option('length');
        $force = (bool)$this->option('force');
        $ttl = $this->option('ttl');

        if($length < 8) { $this->error('Minimalna długość to 8.'); return Command::FAILURE; }

        $cycle = AssessmentCycle::where('is_active', true)->first();
        if(!$cycle){
            $this->error('Brak aktywnego cyklu.');
            return Command::FAILURE;
        }

        // Znajdź pracownika po ID / uuid / email / name (kolejno)
        $employee = Employee::query()
            ->when(is_numeric($identifier), fn($q)=>$q->where('id',$identifier))
            ->when(!is_numeric($identifier), function($q) use ($identifier){
                $q->orWhere('uuid',$identifier)
                  ->orWhere('email',$identifier)
                  ->orWhere('name',$identifier);
            })
            ->first();

        if(!$employee){
            $this->error('Nie znaleziono pracownika dla identyfikatora: '.$identifier);
            return Command::FAILURE;
        }

        $existing = EmployeeCycleAccessCode::where('employee_id',$employee->id)->where('cycle_id',$cycle->id)->first();
        if($existing && !$force){
            $this->warn('Kod już istnieje. Użyj --force aby nadpisać. Ostatnie 4: '.$existing->raw_last4);
            return Command::SUCCESS;
        }

        $code = $customCode ?: $this->generateHumanCode($length);
        $normalized = preg_replace('/[^A-Za-z0-9]/','',$code);
        $last4 = substr($normalized, -4);

        $payload = [
            'code_hash' => password_hash($code, PASSWORD_BCRYPT),
            'raw_last4' => $last4,
            'expires_at' => $ttl ? now()->addMinutes((int)$ttl) : null,
        ];

        if($existing){
            $existing->update($payload);
        } else {
            EmployeeCycleAccessCode::create(array_merge($payload,[
                'employee_id' => $employee->id,
                'cycle_id' => $cycle->id,
            ]));
        }

        $this->info('Kod utworzony dla: '.$employee->name.' (cycle '.$cycle->label.')');
        $this->line('PEŁNY KOD: '.$code);
        $this->line('Ostatnie 4 (raw_last4): '.$last4);
        if($ttl) $this->line('Ważny do: '.now()->addMinutes((int)$ttl));
        return Command::SUCCESS;
    }

    private function generateHumanCode(int $length): string
    {
        // Wzorzec: grupy po 4 znaki oddzielone myślnikiem (A-Z2-9, bez O/0/I/1) - czytelne
        $alphabet = str_split('ABCDEFGHJKMNPQRSTUVWXYZ23456789');
        $raw = '';
        while(strlen($raw) < $length){
            $raw .= $alphabet[random_int(0, count($alphabet)-1)];
        }
        $raw = substr($raw,0,$length);
        return strtoupper(implode('-', str_split($raw, 4)));
    }
}
