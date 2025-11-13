<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ServerStatusController extends Controller
{
    public function __construct()
    {
        // Tylko dla supermanagerów
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role === 'supermanager') {
                return $next($request);
            }
            abort(403, 'Brak dostępu');
        });
    }

    public function index()
    {
        $data = [
            'mysql' => $this->getMySQLStatus(),
            'server' => $this->getServerStatus(),
            'laravel' => $this->getLaravelStatus(),
            'cache' => $this->getCacheStatus(),
            'sessions' => $this->getSessionStatus(),
            'errors' => $this->getRecentErrors(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];

        return view('server-status', $data);
    }

    private function getMySQLStatus()
    {
        try {
            // Podstawowe statystyki MySQL
            $variables = DB::select('SHOW VARIABLES');
            $status = DB::select('SHOW STATUS');
            $processes = DB::select('SHOW PROCESSLIST');

            $varsArray = [];
            foreach ($variables as $var) {
                $varsArray[$var->Variable_name] = $var->Value;
            }

            $statusArray = [];
            foreach ($status as $stat) {
                $statusArray[$stat->Variable_name] = $stat->Value;
            }

            // Obliczenia
            $maxConnections = (int)($varsArray['max_connections'] ?? 0);
            $threadsConnected = (int)($statusArray['Threads_connected'] ?? 0);
            $connectionUsage = $maxConnections > 0 ? round(($threadsConnected / $maxConnections) * 100, 2) : 0;

            // Query cache (jeśli włączony)
            $queryCacheSize = (int)($varsArray['query_cache_size'] ?? 0);
            $queryCacheType = $varsArray['query_cache_type'] ?? 'OFF';

            // Buffer pool (InnoDB)
            $innodbBufferPoolSize = $this->formatBytes($varsArray['innodb_buffer_pool_size'] ?? 0);

            // Slow queries
            $slowQueries = (int)($statusArray['Slow_queries'] ?? 0);
            $totalQueries = (int)($statusArray['Questions'] ?? 1);
            $slowQueryPercentage = round(($slowQueries / $totalQueries) * 100, 4);

            // Uptime
            $uptime = (int)($statusArray['Uptime'] ?? 0);
            $uptimeFormatted = $this->formatUptime($uptime);

            return [
                'status' => 'online',
                'version' => $varsArray['version'] ?? 'unknown',
                'connections' => [
                    'current' => $threadsConnected,
                    'max' => $maxConnections,
                    'usage_percent' => $connectionUsage,
                ],
                'processes' => [
                    'total' => count($processes),
                    'by_command' => collect($processes)->groupBy('Command')->map->count()->toArray(),
                    'by_state' => collect($processes)->groupBy('State')->map->count()->toArray(),
                    'list' => collect($processes)->map(function($p) {
                        return [
                            'id' => $p->Id,
                            'user' => $p->User,
                            'host' => $p->Host,
                            'db' => $p->db,
                            'command' => $p->Command,
                            'time' => $p->Time,
                            'state' => $p->State,
                            'info' => $p->Info ? (strlen($p->Info) > 200 ? substr($p->Info, 0, 200) . '...' : $p->Info) : null,
                        ];
                    })->sortByDesc('time')->values()->take(30)->toArray(),
                ],
                'queries' => [
                    'total' => $totalQueries,
                    'slow' => $slowQueries,
                    'slow_percent' => $slowQueryPercentage,
                ],
                'cache' => [
                    'query_cache_type' => $queryCacheType,
                    'query_cache_size' => $queryCacheSize > 0 ? $this->formatBytes($queryCacheSize) : 'Disabled',
                ],
                'innodb' => [
                    'buffer_pool_size' => $innodbBufferPoolSize,
                ],
                'uptime' => [
                    'seconds' => $uptime,
                    'formatted' => $uptimeFormatted,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getServerStatus()
    {
        $data = [];

        // PHP Info
        $data['php'] = [
            'version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ];

        // Memory usage
        $data['memory'] = [
            'current' => $this->formatBytes(memory_get_usage(true)),
            'peak' => $this->formatBytes(memory_get_peak_usage(true)),
        ];

        // Disk space
        try {
            $diskTotal = disk_total_space('/');
            $diskFree = disk_free_space('/');
            $diskUsed = $diskTotal - $diskFree;
            
            $data['disk'] = [
                'total' => $this->formatBytes($diskTotal),
                'used' => $this->formatBytes($diskUsed),
                'free' => $this->formatBytes($diskFree),
                'usage_percent' => $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 2) : 0,
            ];
        } catch (\Exception $e) {
            $data['disk'] = ['error' => 'Unable to read disk space'];
        }

        // Load average (Linux only)
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $data['load_average'] = [
                '1min' => $load[0],
                '5min' => $load[1],
                '15min' => $load[2],
            ];
        }

        return $data;
    }

    private function getLaravelStatus()
    {
        return [
            'environment' => config('app.env'),
            'debug' => config('app.debug') ? 'enabled' : 'disabled',
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'session_driver' => config('session.driver'),
        ];
    }

    private function getCacheStatus()
    {
        try {
            // Test cache
            $testKey = 'server_status_test_' . time();
            Cache::put($testKey, 'test', 10);
            $cacheWorks = Cache::get($testKey) === 'test';
            Cache::forget($testKey);

            return [
                'status' => $cacheWorks ? 'working' : 'not working',
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function formatBytes($bytes)
    {
        $bytes = (int)$bytes;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 2) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }

    private function formatUptime($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        $parts = [];
        if ($days > 0) $parts[] = $days . 'd';
        if ($hours > 0) $parts[] = $hours . 'h';
        if ($minutes > 0) $parts[] = $minutes . 'm';
        
        return implode(' ', $parts) ?: '0m';
    }

    private function getSessionStatus()
    {
        try {
            $driver = config('session.driver');
            $sessionData = [
                'driver' => $driver,
                'lifetime' => config('session.lifetime') . ' min',
            ];

            if ($driver === 'file') {
                $sessionPath = storage_path('framework/sessions');
                if (file_exists($sessionPath)) {
                    $files = glob($sessionPath . '/*');
                    $activeSessions = 0;
                    $expiredSessions = 0;
                    $recentSessions = 0; // ostatnie 15 minut
                    $now = time();
                    $lifetime = config('session.lifetime') * 60;

                    foreach ($files as $file) {
                        if (is_file($file)) {
                            $lastModified = filemtime($file);
                            $age = $now - $lastModified;
                            
                            if ($age < $lifetime) {
                                $activeSessions++;
                                if ($age < 900) { // 15 minut
                                    $recentSessions++;
                                }
                            } else {
                                $expiredSessions++;
                            }
                        }
                    }

                    $sessionData['active'] = $activeSessions;
                    $sessionData['recent'] = $recentSessions; // aktywne w ostatnich 15 min
                    $sessionData['expired'] = $expiredSessions;
                    $sessionData['total_files'] = count($files);
                } else {
                    $sessionData['error'] = 'Session directory not found';
                }
            } elseif ($driver === 'database') {
                try {
                    $table = config('session.table', 'sessions');
                    $lifetime = config('session.lifetime') * 60;
                    $now = time();
                    
                    $total = DB::table($table)->count();
                    $active = DB::table($table)->where('last_activity', '>', $now - $lifetime)->count();
                    $recent = DB::table($table)->where('last_activity', '>', $now - 900)->count(); // 15 min
                    
                    $sessionData['active'] = $active;
                    $sessionData['recent'] = $recent;
                    $sessionData['expired'] = $total - $active;
                    $sessionData['total'] = $total;
                } catch (\Exception $e) {
                    $sessionData['error'] = 'Cannot read session table: ' . $e->getMessage();
                }
            } else {
                $sessionData['info'] = 'Session count not available for this driver';
            }

            return $sessionData;
        } catch (\Exception $e) {
            return [
                'driver' => config('session.driver'),
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getRecentErrors()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            
            if (!file_exists($logPath)) {
                return [
                    'status' => 'no_log_file',
                    'message' => 'Log file not found',
                ];
            }

            // Odczytaj ostatnie 500 linii (dla wydajności)
            $lines = $this->readLastLines($logPath, 500);
            
            $errors = [];
            $error500Count = 0;
            $errorCount = 0;
            $warningCount = 0;
            
            $currentError = null;
            
            foreach ($lines as $line) {
                // Sprawdź czy to początek nowego wpisu logowania
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+):/', $line, $matches)) {
                    // Zapisz poprzedni błąd jeśli istnieje
                    if ($currentError !== null) {
                        $errors[] = $currentError;
                        $currentError = null;
                    }
                    
                    $timestamp = $matches[1];
                    $level = strtoupper($matches[2]);
                    
                    // Policz błędy
                    if ($level === 'ERROR') {
                        $errorCount++;
                        
                        // Sprawdź czy to błąd 500
                        if (strpos($line, '500') !== false || strpos($line, 'Internal Server Error') !== false) {
                            $error500Count++;
                        }
                        
                        // Zachowaj ostatnie 10 błędów
                        if (count($errors) < 10) {
                            $currentError = [
                                'timestamp' => $timestamp,
                                'level' => $level,
                                'message' => trim(substr($line, strpos($line, ':') + 1)),
                                'full' => $line,
                            ];
                        }
                    } elseif ($level === 'WARNING') {
                        $warningCount++;
                    }
                } elseif ($currentError !== null) {
                    // Dodaj kontynuację do obecnego błędu (stack trace)
                    $currentError['full'] .= "\n" . $line;
                }
            }
            
            // Dodaj ostatni błąd jeśli istnieje
            if ($currentError !== null) {
                $errors[] = $currentError;
            }

            // Odwróć kolejność - najnowsze na górze
            $errors = array_reverse($errors);

            return [
                'status' => 'ok',
                'log_file' => $logPath,
                'log_size' => $this->formatBytes(filesize($logPath)),
                'last_modified' => date('Y-m-d H:i:s', filemtime($logPath)),
                'counts' => [
                    'errors' => $errorCount,
                    'error_500' => $error500Count,
                    'warnings' => $warningCount,
                ],
                'recent_errors' => $errors,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function readLastLines($file, $lines = 100)
    {
        $handle = fopen($file, 'r');
        if (!$handle) {
            return [];
        }

        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = [];

        while ($linecounter > 0) {
            $t = ' ';
            while ($t != "\n") {
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }
            $linecounter--;
            if ($beginning) {
                rewind($handle);
            }
            $text[$lines - $linecounter - 1] = fgets($handle);
            if ($beginning) break;
        }

        fclose($handle);
        return array_reverse($text);
    }
}
