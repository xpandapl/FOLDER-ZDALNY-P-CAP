<style>
    .server-status-container { max-width: 100%; }
    .server-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    .server-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }
    .server-card h3 {
        font-size: 16px;
        margin-bottom: 15px;
        color: #333;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .server-stat-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
    }
    .server-stat-row:last-child { border-bottom: none; }
    .server-stat-label { color: #666; font-weight: 500; }
    .server-stat-value { color: #333; font-weight: 600; }
    .server-status-good { color: #4CAF50; }
    .server-status-warning { color: #ff9800; }
    .server-status-error { color: #f44336; }
    .server-progress-bar {
        width: 100%;
        height: 18px;
        background: #e0e0e0;
        border-radius: 9px;
        overflow: hidden;
        margin-top: 5px;
    }
    .server-progress-fill {
        height: 100%;
        background: #4CAF50;
        transition: width 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 11px;
        font-weight: bold;
    }
    .server-progress-fill.warning { background: #ff9800; }
    .server-progress-fill.error { background: #f44336; }
    .server-process-list {
        max-height: 350px;
        overflow-y: auto;
        margin-top: 10px;
    }
    .server-process-item {
        background: #f9f9f9;
        padding: 8px;
        margin-bottom: 5px;
        border-radius: 4px;
        font-size: 12px;
    }
    .server-process-item strong { color: #333; }
    .server-process-item .time { color: #666; float: right; }
    .server-process-item .query {
        color: #888;
        margin-top: 4px;
        font-family: monospace;
        word-break: break-all;
    }
    .server-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: bold;
    }
    .server-badge-success { background: #d4edda; color: #155724; }
    .server-badge-warning { background: #fff3cd; color: #856404; }
    .server-badge-danger { background: #f8d7da; color: #721c24; }
    .server-error-item {
        background: #fff3cd;
        border-left: 4px solid #ff9800;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 4px;
        font-size: 12px;
    }
    .server-error-item strong { color: #856404; }
    .server-error-item details { margin-top: 8px; }
    .server-error-item summary {
        cursor: pointer;
        color: #666;
        font-size: 11px;
    }
    .server-error-item pre {
        background: #f5f5f5;
        padding: 8px;
        border-radius: 4px;
        font-size: 10px;
        overflow-x: auto;
        margin-top: 8px;
        max-height: 300px;
    }
    .server-refresh-info {
        text-align: center;
        color: #666;
        font-size: 13px;
        margin-top: 20px;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 4px;
    }
    .btn-clear-cache {
        background: #4CAF50;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.2);
    }
    .btn-clear-cache:hover {
        background: #45a049;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
    }
    .btn-clear-cache:active {
        transform: translateY(0);
    }
    .btn-clear-cache:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    .btn-clear-sessions {
        background: #ff9800;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        width: 100%;
        justify-content: center;
    }
    .btn-clear-sessions:hover {
        background: #f57c00;
    }
    .btn-clear-sessions:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
</style>

<div class="server-status-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <small style="color: #666;">Ostatnia aktualizacja: {{ $timestamp }}</small>
        <button onclick="clearAllCache()" class="btn-clear-cache" id="clearCacheBtn">
            <i class="fas fa-broom"></i> Wyczyść cache
        </button>
    </div>

    <div class="server-grid">
        <!-- MySQL Status -->
        <div class="server-card">
            <h3>
                <i class="fas fa-database"></i>
                MySQL
            </h3>
            @if($mysql['status'] === 'online')
                <div class="server-stat-row">
                    <span class="server-stat-label">Status</span>
                    <span class="server-stat-value server-status-good">
                        <i class="fas fa-check-circle"></i> Online
                    </span>
                </div>
                <div class="server-stat-row">
                    <span class="server-stat-label">Wersja</span>
                    <span class="server-stat-value">{{ $mysql['version'] }}</span>
                </div>
                <div class="server-stat-row">
                    <span class="server-stat-label">Połączenia</span>
                    <span class="server-stat-value">
                        {{ $mysql['connections']['current'] }} / {{ $mysql['connections']['max'] }}
                    </span>
                </div>
                <div class="server-progress-bar">
                    <div class="server-progress-fill {{ $mysql['connections']['usage_percent'] > 80 ? 'error' : ($mysql['connections']['usage_percent'] > 60 ? 'warning' : '') }}" 
                         style="width: {{ $mysql['connections']['usage_percent'] }}%">
                        {{ $mysql['connections']['usage_percent'] }}%
                    </div>
                </div>
                <div class="server-stat-row">
                    <span class="server-stat-label">Aktywne procesy</span>
                    <span class="server-stat-value">{{ $mysql['processes']['total'] }}</span>
                </div>
                @if(isset($mysql['processes']['by_command']))
                <div style="margin-top: 8px; padding: 8px; background: #f9fafb; border-radius: 4px; font-size: 11px;">
                    <strong>Procesy wg typu:</strong><br>
                    @foreach($mysql['processes']['by_command'] as $cmd => $count)
                        <span style="display: inline-block; margin-right: 10px;">{{ $cmd }}: <strong>{{ $count }}</strong></span>
                    @endforeach
                </div>
                @endif
                <div class="server-stat-row">
                    <span class="server-stat-label">Wolne zapytania</span>
                    <span class="server-stat-value">
                        {{ $mysql['queries']['slow'] }} 
                        <small>({{ $mysql['queries']['slow_percent'] }}%)</small>
                    </span>
                </div>
                <div class="server-stat-row">
                    <span class="server-stat-label">Uptime</span>
                    <span class="server-stat-value">{{ $mysql['uptime']['formatted'] }}</span>
                </div>
            @else
                <div class="server-stat-row">
                    <span class="server-stat-label">Status</span>
                    <span class="server-stat-value server-status-error">
                        <i class="fas fa-times-circle"></i> Error
                    </span>
                </div>
                <div style="color: #f44336; margin-top: 10px; font-size: 12px;">
                    {{ $mysql['error'] ?? 'Unknown error' }}
                </div>
            @endif
        </div>

        <!-- Server Resources -->
        <div class="server-card">
            <h3>
                <i class="fas fa-microchip"></i>
                Zasoby Serwera
            </h3>
            <div class="server-stat-row">
                <span class="server-stat-label">PHP Version</span>
                <span class="server-stat-value">{{ $server['php']['version'] }}</span>
            </div>
            <div class="server-stat-row">
                <span class="server-stat-label">Memory Limit</span>
                <span class="server-stat-value">{{ $server['php']['memory_limit'] }}</span>
            </div>
            <div class="server-stat-row">
                <span class="server-stat-label">Użycie pamięci</span>
                <span class="server-stat-value">{{ $server['memory']['current'] }}</span>
            </div>
            <div class="server-stat-row">
                <span class="server-stat-label">Peak pamięci</span>
                <span class="server-stat-value">{{ $server['memory']['peak'] }}</span>
            </div>
            @if(isset($server['disk']['used']))
            <div class="server-stat-row">
                <span class="server-stat-label">Dysk - użyte</span>
                <span class="server-stat-value">{{ $server['disk']['used'] }}</span>
            </div>
            <div class="server-stat-row">
                <span class="server-stat-label">Dysk - wolne</span>
                <span class="server-stat-value">{{ $server['disk']['free'] }}</span>
            </div>
            <div class="server-progress-bar">
                <div class="server-progress-fill {{ $server['disk']['usage_percent'] > 90 ? 'error' : ($server['disk']['usage_percent'] > 75 ? 'warning' : '') }}" 
                     style="width: {{ $server['disk']['usage_percent'] }}%">
                    {{ $server['disk']['usage_percent'] }}%
                </div>
            </div>
            @endif
        </div>

        <!-- Laravel Config -->
        <div class="server-card">
            <h3>
                <i class="fab fa-laravel"></i>
                Laravel
            </h3>
            <div class="server-stat-row">
                <span class="server-stat-label">Environment</span>
                <span class="server-stat-value">
                    <span class="server-badge {{ $laravel['environment'] === 'production' ? 'server-badge-success' : 'server-badge-warning' }}">
                        {{ $laravel['environment'] }}
                    </span>
                </span>
            </div>
            <div class="server-stat-row">
                <span class="server-stat-label">Debug Mode</span>
                <span class="server-stat-value">
                    <span class="server-badge {{ $laravel['debug'] === 'disabled' ? 'server-badge-success' : 'server-badge-danger' }}">
                        {{ $laravel['debug'] }}
                    </span>
                </span>
            </div>
            <div class="server-stat-row">
                <span class="server-stat-label">Cache Driver</span>
                <span class="server-stat-value">{{ $laravel['cache_driver'] }}</span>
            </div>
            <div class="server-stat-row">
                <span class="server-stat-label">Session Driver</span>
                <span class="server-stat-value">{{ $laravel['session_driver'] }}</span>
            </div>
        </div>

        <!-- Sessions -->
        <div class="server-card">
            <h3>
                <i class="fas fa-users"></i>
                Sesje użytkowników
            </h3>
            <div class="server-stat-row">
                <span class="server-stat-label">Driver</span>
                <span class="server-stat-value">{{ $sessions['driver'] }}</span>
            </div>
            <div class="server-stat-row">
                <span class="server-stat-label">Lifetime</span>
                <span class="server-stat-value">{{ $sessions['lifetime'] }}</span>
            </div>
            @if(isset($sessions['recent']))
            <div class="server-stat-row">
                <span class="server-stat-label">Aktywni teraz (ostatnie 15 min)</span>
                <span class="server-stat-value server-status-good">
                    <i class="fas fa-user-check"></i> {{ $sessions['recent'] }}
                </span>
            </div>
            @endif
            @if(isset($sessions['active']))
            <div class="server-stat-row">
                <span class="server-stat-label">Sesje niewygasłe</span>
                <span class="server-stat-value">{{ $sessions['active'] }}</span>
            </div>
            @endif
            @if(isset($sessions['expired']) && $sessions['expired'] > 0)
            <div class="server-stat-row">
                <span class="server-stat-label">Wygasłe (do usunięcia)</span>
                <span class="server-stat-value server-status-warning">
                    {{ $sessions['expired'] }}
                </span>
            </div>
            <div style="margin-top: 10px;">
                <button onclick="clearExpiredSessions()" class="btn-clear-sessions" id="clearSessionsBtn">
                    <i class="fas fa-trash"></i> Wyczyść wygasłe ({{ $sessions['expired'] }})
                </button>
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
        <div class="server-card">
            <h3>
                <i class="fas fa-exclamation-triangle"></i>
                Błędy
            </h3>
            @if($errors['status'] === 'ok')
                <div class="server-stat-row">
                    <span class="server-stat-label">Rozmiar logu</span>
                    <span class="server-stat-value">{{ $errors['log_size'] }}</span>
                </div>
                <div class="server-stat-row">
                    <span class="server-stat-label">Błędy 500</span>
                    <span class="server-stat-value {{ $errors['counts']['error_500'] > 0 ? 'server-status-error' : 'server-status-good' }}">
                        <i class="fas fa-{{ $errors['counts']['error_500'] > 0 ? 'times-circle' : 'check-circle' }}"></i>
                        {{ $errors['counts']['error_500'] }}
                    </span>
                </div>
                <div class="server-stat-row">
                    <span class="server-stat-label">Wszystkie błędy</span>
                    <span class="server-stat-value {{ $errors['counts']['errors'] > 0 ? 'server-status-warning' : 'server-status-good' }}">
                        {{ $errors['counts']['errors'] }}
                    </span>
                </div>
                <div class="server-stat-row">
                    <span class="server-stat-label">Ostrzeżenia</span>
                    <span class="server-stat-value">{{ $errors['counts']['warnings'] }}</span>
                </div>
            @else
                <div class="server-stat-row">
                    <span class="server-stat-label">Status</span>
                    <span class="server-stat-value server-status-warning">
                        {{ $errors['message'] ?? $errors['error'] ?? 'Unknown error' }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Cache Status -->
        <div class="server-card">
            <h3>
                <i class="fas fa-memory"></i>
                Cache
            </h3>
            <div class="server-stat-row">
                <span class="server-stat-label">Status</span>
                <span class="server-stat-value {{ $cache['status'] === 'working' ? 'server-status-good' : 'server-status-error' }}">
                    <i class="fas fa-{{ $cache['status'] === 'working' ? 'check-circle' : 'times-circle' }}"></i>
                    {{ ucfirst($cache['status']) }}
                </span>
            </div>
            <div class="server-stat-row">
                <span class="server-stat-label">Driver</span>
                <span class="server-stat-value">{{ $cache['driver'] }}</span>
            </div>
        </div>
    </div>

    <!-- MySQL Processes -->
    @if($mysql['status'] === 'online' && !empty($mysql['processes']['list']))
    <div class="server-card">
        <h3>
            <i class="fas fa-tasks"></i>
            Aktywne Procesy MySQL (Top 30)
        </h3>
        
        @if(isset($mysql['processes']['by_state']))
        <div style="margin-bottom: 15px; padding: 10px; background: #f0f9ff; border-left: 3px solid #3b82f6; border-radius: 4px;">
            <strong style="color: #1e40af;">Stan procesów:</strong><br>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 5px;">
                @foreach($mysql['processes']['by_state'] as $state => $count)
                    <span style="background: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                        <strong>{{ $state ?: 'NULL' }}:</strong> {{ $count }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
        
        <div class="server-process-list">
            @foreach($mysql['processes']['list'] as $process)
            <div class="server-process-item">
                <strong>ID: {{ $process['id'] }}</strong>
                <span class="time">{{ $process['time'] }}s</span>
                <br>
                <strong>User:</strong> {{ $process['user'] }} | 
                <strong>Host:</strong> {{ $process['host'] }} | 
                <strong>DB:</strong> {{ $process['db'] ?? 'NULL' }}<br>
                <strong>Command:</strong> <span style="background: #fef3c7; padding: 2px 6px; border-radius: 3px;">{{ $process['command'] }}</span>
                @if($process['state'])
                | <strong>State:</strong> <span style="color: #059669;">{{ $process['state'] }}</span>
                @endif
                @if($process['info'])
                <div class="query">{{ $process['info'] }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Errors -->
    @if($errors['status'] === 'ok' && !empty($errors['recent_errors']))
    <div class="server-card">
        <h3>
            <i class="fas fa-bug"></i>
            Ostatnie błędy (top 10)
        </h3>
        <div class="server-process-list">
            @foreach($errors['recent_errors'] as $error)
            <div class="server-error-item">
                <strong>{{ $error['timestamp'] }}</strong>
                <span class="server-badge server-badge-danger" style="float: right;">{{ $error['level'] }}</span>
                <div style="margin-top: 8px; color: #333;">
                    {{ $error['message'] }}
                </div>
                @if(strlen($error['full']) > strlen($error['message']) + 100)
                <details>
                    <summary>
                        <i class="fas fa-code"></i> Pokaż stack trace
                    </summary>
                    <pre>{{ $error['full'] }}</pre>
                </details>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="server-refresh-info">
        <i class="fas fa-info-circle"></i> Sekcja odświeża się automatycznie przy każdym przełączeniu zakładki
    </div>
</div>

<script>
function clearAllCache() {
    const btn = document.getElementById('clearCacheBtn');
    const originalText = btn.innerHTML;
    
    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Czyszczenie...';
    
    fetch('/clear-all-cache', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.text())
    .then(data => {
        // Show success
        btn.innerHTML = '<i class="fas fa-check"></i> Wyczyszczono!';
        btn.style.background = '#10b981';
        
        // Reset button after 2 seconds
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            btn.style.background = '#4CAF50';
            
            // Reload section to show updated stats
            if (typeof loadSection === 'function') {
                loadSection('server', false);
            }
        }, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
        btn.innerHTML = '<i class="fas fa-times"></i> Błąd';
        btn.style.background = '#ef4444';
        
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            btn.style.background = '#4CAF50';
        }, 2000);
    });
}

function clearExpiredSessions() {
    const btn = document.getElementById('clearSessionsBtn');
    const originalText = btn.innerHTML;
    
    if (!confirm('Czy na pewno chcesz usunąć wszystkie wygasłe sesje?')) {
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Usuwanie...';
    
    fetch('/clear-expired-sessions', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = `<i class="fas fa-check"></i> Usunięto ${data.deleted}`;
        btn.style.background = '#10b981';
        
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            btn.style.background = '#ff9800';
            
            if (typeof loadSection === 'function') {
                loadSection('server', false);
            }
        }, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
        btn.innerHTML = '<i class="fas fa-times"></i> Błąd';
        btn.style.background = '#ef4444';
        
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            btn.style.background = '#ff9800';
        }, 2000);
    });
}
</script>
