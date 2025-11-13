<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Serwera - P-CAP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        h1 { 
            color: #333; 
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .timestamp {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .refresh-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-left: auto;
        }
        .refresh-btn:hover { background: #45a049; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .stat-row:last-child { border-bottom: none; }
        .stat-label { 
            color: #666; 
            font-weight: 500;
        }
        .stat-value { 
            color: #333;
            font-weight: 600;
        }
        .status-good { color: #4CAF50; }
        .status-warning { color: #ff9800; }
        .status-error { color: #f44336; }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        .progress-fill {
            height: 100%;
            background: #4CAF50;
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .progress-fill.warning { background: #ff9800; }
        .progress-fill.error { background: #f44336; }
        .process-list {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 10px;
        }
        .process-item {
            background: #f9f9f9;
            padding: 8px;
            margin-bottom: 5px;
            border-radius: 4px;
            font-size: 12px;
        }
        .process-item strong { color: #333; }
        .process-item .time {
            color: #666;
            float: right;
        }
        .process-item .query {
            color: #888;
            margin-top: 4px;
            font-family: monospace;
            word-break: break-all;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>
                    <i class="fas fa-server"></i>
                    Status Serwera
                </h1>
                <div class="timestamp">
                    Ostatnia aktualizacja: {{ $timestamp }}
                </div>
            </div>
            <button class="refresh-btn" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Odśwież
            </button>
        </div>

        <div class="grid">
            <!-- MySQL Status -->
            <div class="card">
                <h2>
                    <i class="fas fa-database"></i>
                    MySQL
                </h2>
                @if($mysql['status'] === 'online')
                    <div class="stat-row">
                        <span class="stat-label">Status</span>
                        <span class="stat-value status-good">
                            <i class="fas fa-check-circle"></i> Online
                        </span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Wersja</span>
                        <span class="stat-value">{{ $mysql['version'] }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Połączenia</span>
                        <span class="stat-value">
                            {{ $mysql['connections']['current'] }} / {{ $mysql['connections']['max'] }}
                        </span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill {{ $mysql['connections']['usage_percent'] > 80 ? 'error' : ($mysql['connections']['usage_percent'] > 60 ? 'warning' : '') }}" 
                             style="width: {{ $mysql['connections']['usage_percent'] }}%">
                            {{ $mysql['connections']['usage_percent'] }}%
                        </div>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Aktywne procesy</span>
                        <span class="stat-value">{{ $mysql['processes']['total'] }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Wolne zapytania</span>
                        <span class="stat-value">
                            {{ $mysql['queries']['slow'] }} 
                            <small>({{ $mysql['queries']['slow_percent'] }}%)</small>
                        </span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Uptime</span>
                        <span class="stat-value">{{ $mysql['uptime']['formatted'] }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">InnoDB Buffer Pool</span>
                        <span class="stat-value">{{ $mysql['innodb']['buffer_pool_size'] }}</span>
                    </div>
                @else
                    <div class="stat-row">
                        <span class="stat-label">Status</span>
                        <span class="stat-value status-error">
                            <i class="fas fa-times-circle"></i> Error
                        </span>
                    </div>
                    <div style="color: #f44336; margin-top: 10px;">
                        {{ $mysql['error'] ?? 'Unknown error' }}
                    </div>
                @endif
            </div>

            <!-- Server Resources -->
            <div class="card">
                <h2>
                    <i class="fas fa-microchip"></i>
                    Zasoby Serwera
                </h2>
                <div class="stat-row">
                    <span class="stat-label">PHP Version</span>
                    <span class="stat-value">{{ $server['php']['version'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Memory Limit</span>
                    <span class="stat-value">{{ $server['php']['memory_limit'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Aktualne użycie pamięci</span>
                    <span class="stat-value">{{ $server['memory']['current'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Peak pamięci</span>
                    <span class="stat-value">{{ $server['memory']['peak'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Dysk - użyte</span>
                    <span class="stat-value">{{ $server['disk']['used'] ?? 'N/A' }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Dysk - wolne</span>
                    <span class="stat-value">{{ $server['disk']['free'] ?? 'N/A' }}</span>
                </div>
                @if(isset($server['disk']['usage_percent']))
                <div class="progress-bar">
                    <div class="progress-fill {{ $server['disk']['usage_percent'] > 90 ? 'error' : ($server['disk']['usage_percent'] > 75 ? 'warning' : '') }}" 
                         style="width: {{ $server['disk']['usage_percent'] }}%">
                        {{ $server['disk']['usage_percent'] }}%
                    </div>
                </div>
                @endif
                @if(isset($server['load_average']))
                <div class="stat-row">
                    <span class="stat-label">Load Average</span>
                    <span class="stat-value">
                        {{ number_format($server['load_average']['1min'], 2) }} / 
                        {{ number_format($server['load_average']['5min'], 2) }} / 
                        {{ number_format($server['load_average']['15min'], 2) }}
                    </span>
                </div>
                @endif
            </div>

            <!-- Laravel Config -->
            <div class="card">
                <h2>
                    <i class="fab fa-laravel"></i>
                    Laravel
                </h2>
                <div class="stat-row">
                    <span class="stat-label">Environment</span>
                    <span class="stat-value">
                        <span class="badge {{ $laravel['environment'] === 'production' ? 'badge-success' : 'badge-warning' }}">
                            {{ $laravel['environment'] }}
                        </span>
                    </span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Debug Mode</span>
                    <span class="stat-value">
                        <span class="badge {{ $laravel['debug'] === 'disabled' ? 'badge-success' : 'badge-danger' }}">
                            {{ $laravel['debug'] }}
                        </span>
                    </span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Cache Driver</span>
                    <span class="stat-value">{{ $laravel['cache_driver'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Queue Driver</span>
                    <span class="stat-value">{{ $laravel['queue_driver'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Session Driver</span>
                    <span class="stat-value">{{ $laravel['session_driver'] }}</span>
                </div>
            </div>

            <!-- Cache Status -->
            <div class="card">
                <h2>
                    <i class="fas fa-memory"></i>
                    Cache
                </h2>
                <div class="stat-row">
                    <span class="stat-label">Status</span>
                    <span class="stat-value {{ $cache['status'] === 'working' ? 'status-good' : 'status-error' }}">
                        <i class="fas fa-{{ $cache['status'] === 'working' ? 'check-circle' : 'times-circle' }}"></i>
                        {{ ucfirst($cache['status']) }}
                    </span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Driver</span>
                    <span class="stat-value">{{ $cache['driver'] }}</span>
                </div>
                @if(isset($cache['error']))
                <div style="color: #f44336; margin-top: 10px; font-size: 12px;">
                    {{ $cache['error'] }}
                </div>
                @endif
            </div>

            <!-- Sessions Status -->
            <div class="card">
                <h2>
                    <i class="fas fa-users"></i>
                    Sesje użytkowników
                </h2>
                <div class="stat-row">
                    <span class="stat-label">Driver</span>
                    <span class="stat-value">{{ $sessions['driver'] }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Lifetime</span>
                    <span class="stat-value">{{ $sessions['lifetime'] }}</span>
                </div>
                @if(isset($sessions['active']))
                <div class="stat-row">
                    <span class="stat-label">Aktywne sesje</span>
                    <span class="stat-value status-good">
                        <i class="fas fa-user-check"></i> {{ $sessions['active'] }}
                    </span>
                </div>
                @endif
                @if(isset($sessions['expired']))
                <div class="stat-row">
                    <span class="stat-label">Wygasłe sesje</span>
                    <span class="stat-value">{{ $sessions['expired'] }}</span>
                </div>
                @endif
                @if(isset($sessions['total_files']))
                <div class="stat-row">
                    <span class="stat-label">Pliki sesji</span>
                    <span class="stat-value">{{ $sessions['total_files'] }}</span>
                </div>
                @endif
                @if(isset($sessions['total']))
                <div class="stat-row">
                    <span class="stat-label">Razem w bazie</span>
                    <span class="stat-value">{{ $sessions['total'] }}</span>
                </div>
                @endif
                @if(isset($sessions['error']))
                <div style="color: #f44336; margin-top: 10px; font-size: 12px;">
                    {{ $sessions['error'] }}
                </div>
                @endif
                @if(isset($sessions['info']))
                <div style="color: #666; margin-top: 10px; font-size: 12px;">
                    <i class="fas fa-info-circle"></i> {{ $sessions['info'] }}
                </div>
                @endif
            </div>

            <!-- Errors Summary -->
            <div class="card">
                <h2>
                    <i class="fas fa-exclamation-triangle"></i>
                    Błędy (ostatnie 500 linii logu)
                </h2>
                @if($errors['status'] === 'ok')
                    <div class="stat-row">
                        <span class="stat-label">Plik logu</span>
                        <span class="stat-value" style="font-size: 11px;">{{ basename($errors['log_file']) }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Rozmiar logu</span>
                        <span class="stat-value">{{ $errors['log_size'] }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Ostatnia modyfikacja</span>
                        <span class="stat-value">{{ $errors['last_modified'] }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Błędy 500</span>
                        <span class="stat-value {{ $errors['counts']['error_500'] > 0 ? 'status-error' : 'status-good' }}">
                            <i class="fas fa-{{ $errors['counts']['error_500'] > 0 ? 'times-circle' : 'check-circle' }}"></i>
                            {{ $errors['counts']['error_500'] }}
                        </span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Wszystkie błędy</span>
                        <span class="stat-value {{ $errors['counts']['errors'] > 0 ? 'status-warning' : 'status-good' }}">
                            {{ $errors['counts']['errors'] }}
                        </span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Ostrzeżenia</span>
                        <span class="stat-value">{{ $errors['counts']['warnings'] }}</span>
                    </div>
                @else
                    <div class="stat-row">
                        <span class="stat-label">Status</span>
                        <span class="stat-value status-warning">
                            {{ $errors['message'] ?? $errors['error'] ?? 'Unknown error' }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Errors -->
        @if($errors['status'] === 'ok' && !empty($errors['recent_errors']))
        <div class="card">
            <h2>
                <i class="fas fa-bug"></i>
                Ostatnie błędy (top 10)
            </h2>
            <div class="process-list">
                @foreach($errors['recent_errors'] as $error)
                <div class="process-item" style="background: #fff3cd; border-left: 4px solid #ff9800;">
                    <strong style="color: #856404;">{{ $error['timestamp'] }}</strong>
                    <span class="badge badge-danger" style="float: right;">{{ $error['level'] }}</span>
                    <div style="margin-top: 8px; color: #333;">
                        {{ $error['message'] }}
                    </div>
                    @if(strlen($error['full']) > strlen($error['message']) + 100)
                    <details style="margin-top: 8px;">
                        <summary style="cursor: pointer; color: #666; font-size: 11px;">
                            <i class="fas fa-code"></i> Pokaż stack trace
                        </summary>
                        <pre style="background: #f5f5f5; padding: 8px; border-radius: 4px; font-size: 10px; overflow-x: auto; margin-top: 8px;">{{ $error['full'] }}</pre>
                    </details>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- MySQL Processes -->
        @if($mysql['status'] === 'online' && !empty($mysql['processes']['list']))
        <div class="card">
            <h2>
                <i class="fas fa-tasks"></i>
                Aktywne Procesy MySQL (Top 20)
            </h2>
            <div class="process-list">
                @foreach($mysql['processes']['list'] as $process)
                <div class="process-item">
                    <strong>ID: {{ $process['id'] }}</strong>
                    <span class="time">{{ $process['time'] }}s</span>
                    <br>
                    <strong>User:</strong> {{ $process['user'] }} | 
                    <strong>DB:</strong> {{ $process['db'] ?? 'NULL' }} | 
                    <strong>Command:</strong> {{ $process['command'] }} | 
                    <strong>State:</strong> {{ $process['state'] ?? 'NULL' }}
                    @if($process['info'])
                    <div class="query">{{ $process['info'] }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <script>
        // Auto-refresh co 30 sekund
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
