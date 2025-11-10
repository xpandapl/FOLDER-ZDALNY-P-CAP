@php
    // Determine which employees to show based on manager role for statistics
    $dashboardEmployees = collect();
    if ($manager->role == 'supermanager') {
        $dashboardEmployees = $allEmployees ?? collect();
    } elseif ($manager->role == 'head') {
        $dashboardEmployees = $departmentEmployees ?? collect();
    } else {
        $dashboardEmployees = $employees ?? collect();
    }
@endphp

<div class="dashboard-container">
    <!-- Top Row: Quick Actions + Key Stats -->
    <div class="dashboard-row">
        <!-- Quick Actions Card -->
        <div class="dashboard-card quick-actions-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-bolt"></i> Szybkie akcje
                </h2>
            </div>
            <div class="card-content">
                <div class="quick-actions-grid">
                    <a href="#" class="quick-action-btn" data-section="individual">
                        <div class="quick-action-icon individual">
                            <i class="fas fa-user"></i>
                        </div>
                        <span>Oceń pracownika</span>
                    </a>
                    
                    <a href="#" class="quick-action-btn" data-section="team">
                        <div class="quick-action-icon team">
                            <i class="fas fa-users"></i>
                        </div>
                        <span>Przegląd zespołu</span>
                    </a>
                    
                    <a href="#" class="quick-action-btn" data-section="codes">
                        <div class="quick-action-icon codes">
                            <i class="fas fa-key"></i>
                        </div>
                        <span>Zarządzaj kodami</span>
                    </a>
                    
                    <a href="#" class="quick-action-btn export-btn" onclick="exportTeamReport()">
                        <div class="quick-action-icon export">
                            <i class="fas fa-download"></i>
                        </div>
                        <span>Eksportuj raport</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Key Stats Card -->
        <div class="dashboard-card stats-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-bar"></i> Status wypełnienia ocen
                </h2>
            </div>
            <div class="card-content">
                <div class="stats-tiles">
                    @php
                        $completedCount = 0;
                        $pendingCount = 0;
                        $totalCount = $dashboardEmployees->count();
                        
                        if ($selectedCycleId) {
                            foreach ($dashboardEmployees as $emp) {
                                $hasResults = \App\Models\Result::where('employee_id', $emp->id)
                                    ->where('cycle_id', $selectedCycleId)
                                    ->exists();
                                if ($hasResults) {
                                    $completedCount++;
                                } else {
                                    $pendingCount++;
                                }
                            }
                        }
                    @endphp
                    
                    <div class="stat-tile total">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $totalCount }}</div>
                            <div class="stat-label">Łącznie pracowników</div>
                        </div>
                    </div>
                    
                    <div class="stat-tile completed">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $completedCount }}</div>
                            <div class="stat-label">Oceny wypełnione</div>
                        </div>
                    </div>
                    
                    <div class="stat-tile pending">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $pendingCount }}</div>
                            <div class="stat-label">Oczekujące oceny</div>
                        </div>
                    </div>
                    
                    @if($totalCount > 0)
                        <div class="stat-tile progress">
                            <div class="stat-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format(($completedCount / $totalCount) * 100, 1) }}%</div>
                                <div class="stat-label">Postęp wypełnienia</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row: Feedback Analysis + Team Levels -->
    <div class="dashboard-row">
        <!-- Feedback Analysis Card -->
        <div class="dashboard-card feedback-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-line"></i> Analiza wsparcia rozwojowego
                </h2>
            </div>
            <div class="card-content">
                @if($dashboardEmployees->count() > 100)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Duży zespół ({{ $dashboardEmployees->count() }} pracowników)</strong>
                            <br>Analiza dostępna dla zespołów do 100 osób.
                        </div>
                    </div>
                @else
                    @php
                        $feedbackStats = [
                            'total_assessments' => 0,
                            'with_manager_feedback' => 0,
                            'positive_adjustments' => 0,
                            'developmental_feedback' => 0
                        ];
                        
                        if (isset($results) && $results->isNotEmpty()) {
                            $relevantResults = $results->whereIn('employee_id', $dashboardEmployees->pluck('id'));
                            
                            foreach($relevantResults as $result) {
                                $feedbackStats['total_assessments']++;
                                
                                if (!empty($result->manager_comments)) {
                                    $feedbackStats['with_manager_feedback']++;
                                    
                                    $comment = strtolower($result->manager_comments);
                                    if (str_contains($comment, 'rozwój') || str_contains($comment, 'poprawa') || str_contains($comment, 'uczenie')) {
                                        $feedbackStats['developmental_feedback']++;
                                    }
                                }
                                
                                if ($result->score_manager > $result->score) {
                                    $feedbackStats['positive_adjustments']++;
                                }
                            }
                        }
                        
                        $feedbackCoverage = $feedbackStats['total_assessments'] > 0 
                            ? ($feedbackStats['with_manager_feedback'] / $feedbackStats['total_assessments']) * 100 
                            : 0;
                        $developmentalRate = $feedbackStats['with_manager_feedback'] > 0 
                            ? ($feedbackStats['developmental_feedback'] / $feedbackStats['with_manager_feedback']) * 100 
                            : 0;
                        $growthSupport = $feedbackStats['total_assessments'] > 0 
                            ? ($feedbackStats['positive_adjustments'] / $feedbackStats['total_assessments']) * 100 
                            : 0;
                    @endphp
                    
                    <div class="feedback-metrics">
                        <div class="metric-card">
                            <div class="metric-icon" style="background: #dbeafe; color: #1d4ed8;">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value">{{ number_format($feedbackCoverage, 1) }}%</div>
                                <div class="metric-label">Pokrycie feedback</div>
                                <div class="metric-detail">{{ $feedbackStats['with_manager_feedback'] }}/{{ $feedbackStats['total_assessments'] }} ocen z komentarzem</div>
                            </div>
                        </div>
                        
                        <div class="metric-card">
                            <div class="metric-icon" style="background: #dcfce7; color: #166534;">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value">{{ number_format($developmentalRate, 1) }}%</div>
                                <div class="metric-label">Feedback rozwojowy</div>
                                <div class="metric-detail">Szczegółowe komentarze wspierające</div>
                            </div>
                        </div>
                        
                        <div class="metric-card">
                            <div class="metric-icon" style="background: #fef3c7; color: #d97706;">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value">{{ $feedbackStats['positive_adjustments'] }}</div>
                                <div class="metric-label">Pozytywne korekty</div>
                                <div class="metric-detail">Dostrzeżenie potencjału zespołu</div>
                            </div>
                        </div>
                        
                        <div class="metric-card">
                            <div class="metric-icon" style="background: #f3e8ff; color: #7c3aed;">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value">{{ number_format($growthSupport, 1) }}%</div>
                                <div class="metric-label">Wsparcie rozwoju</div>
                                <div class="metric-detail">Oceny wspierające kompetencje</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($feedbackStats['total_assessments'] > 0)
                        <div class="feedback-insights" style="margin-top: 20px;">
                            @if($feedbackCoverage < 80)
                                <div class="insight-item">
                                    <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
                                    <span>Pokrycie feedback: {{ number_format($feedbackCoverage, 1) }}% ocen zawiera komentarze rozwojowe</span>
                                </div>
                            @endif
                            
                            @if($growthSupport > 70)
                                <div class="insight-item">
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                    <span>Wysokie wsparcie rozwoju kompetencji - oceny managera pozytywnie wpływają na samoocenę zespołu</span>
                                </div>
                            @endif
                            
                            @if($feedbackStats['positive_adjustments'] > 0)
                                <div class="insight-item">
                                    <i class="fas fa-arrow-up" style="color: #8b5cf6;"></i>
                                    <span>{{ $feedbackStats['positive_adjustments'] }} pozytywnych korekt ocen - aktywne rozpoznawanie potencjału zespołu</span>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Team Levels Card -->
        <div class="dashboard-card levels-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-layer-group"></i> Analiza kompetencji według poziomów
                </h2>
            </div>
            <div class="card-content">
                @if($dashboardEmployees->isEmpty())
                    <div class="no-data">
                        <i class="fas fa-chart-pie"></i>
                        <h3>Brak danych</h3>
                        <p>Nie masz przypisanych pracowników.</p>
                    </div>
                @elseif($dashboardEmployees->count() > 200)
                    <div class="alert alert-warning">
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>Bardzo duży zespół ({{ $dashboardEmployees->count() }} pracowników)</strong>
                            <br>Szczegółowa analiza poziomów niedostępna dla zespołów powyżej 200 osób.
                        </div>
                    </div>
                @else
                    @php
                        $levelStats = [
                            1 => ['name' => 'Junior', 'employees' => collect(), 'color' => '#10b981'],
                            2 => ['name' => 'Specjalista', 'employees' => collect(), 'color' => '#3b82f6'], 
                            3 => ['name' => 'Senior', 'employees' => collect(), 'color' => '#8b5cf6'],
                            4 => ['name' => 'Supervisor', 'employees' => collect(), 'color' => '#f59e0b'],
                            5 => ['name' => 'Manager', 'employees' => collect(), 'color' => '#ef4444']
                        ];
                        
                        if (isset($employeesByCompetencyLevel) && $selectedCycleId) {
                            foreach($employeesByCompetencyLevel as $level => $levelEmployees) {
                                if (isset($levelStats[$level])) {
                                    $dashboardLevelEmployees = $levelEmployees->whereIn('id', $dashboardEmployees->pluck('id'));
                                    $levelStats[$level]['employees'] = $dashboardLevelEmployees;
                                }
                            }
                        } else {
                            $totalCount = $dashboardEmployees->count();
                            if ($totalCount > 0) {
                                $chunks = $dashboardEmployees->chunk(max(1, ceil($totalCount / 5)));
                                $levelIndex = 1;
                                foreach($chunks as $chunk) {
                                    if ($levelIndex <= 5) {
                                        $levelStats[$levelIndex]['employees'] = $chunk;
                                        $levelIndex++;
                                    }
                                }
                            }
                        }
                    @endphp
                    
                    <div class="level-stats-compact">
                        @foreach($levelStats as $level => $data)
                            <div class="level-stat-item">
                                <div class="level-badge" style="background: {{ $data['color'] }};">
                                    {{ $level }}
                                </div>
                                <div class="level-info">
                                    <div class="level-name">{{ $data['name'] }}</div>
                                    <div class="level-count">{{ $data['employees']->count() }} os.</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Third Row: Export Options -->
    <div class="dashboard-row">
        <div class="dashboard-card export-card full-width">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-download"></i> Eksport i raporty
                </h2>
            </div>
            <div class="card-content">
                <div class="export-options-grid">
                    <button class="export-option" onclick="exportTeamReport()">
                        <div class="export-icon excel">
                            <i class="fas fa-file-excel"></i>
                        </div>
                        <div class="export-text">
                            <div class="export-title">Raport zespołu</div>
                            <div class="export-subtitle">Excel (.xlsx)</div>
                        </div>
                    </button>
                    
                    <button class="export-option" onclick="exportTeamPdf()">
                        <div class="export-icon pdf">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="export-text">
                            <div class="export-title">Raport zespołu</div>
                            <div class="export-subtitle">PDF</div>
                        </div>
                    </button>
                    
                    @if(isset($manager) && $manager->role == 'supermanager')
                        <button class="export-option" onclick="window.open('{{ route('admin.panel') }}', '_blank')">
                            <div class="export-icon summary">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div class="export-text">
                                <div class="export-title">Panel admin</div>
                                <div class="export-subtitle">Statystyki</div>
                            </div>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding: 0;
    }

    .dashboard-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .dashboard-row .full-width {
        grid-column: 1 / -1;
    }

    .dashboard-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .dashboard-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .dashboard-card .card-header {
        padding: 20px;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
    }

    .dashboard-card .card-content {
        padding: 20px;
    }

    /* Quick Actions Grid */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        text-decoration: none;
        color: var(--text);
        transition: all 0.2s ease;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }

    .quick-action-btn:hover {
        background: var(--hover);
        border-color: var(--primary);
        transform: translateY(-1px);
    }

    .quick-action-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }

    .quick-action-icon.individual { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .quick-action-icon.team { background: linear-gradient(135deg, #10b981, #059669); }
    .quick-action-icon.codes { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .quick-action-icon.export { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    /* Stats Tiles */
    .stats-tiles {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .stat-tile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--bg);
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .stat-tile .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-tile.total .stat-icon { background: #dbeafe; color: #1d4ed8; }
    .stat-tile.completed .stat-icon { background: #dcfce7; color: #166534; }
    .stat-tile.pending .stat-icon { background: #fee2e2; color: #dc2626; }
    .stat-tile.progress .stat-icon { background: #fef3c7; color: #d97706; }

    .stat-content .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
    }

    .stat-content .stat-label {
        font-size: 14px;
        color: var(--muted);
        margin-top: 4px;
    }

    /* Feedback Metrics */
    .feedback-metrics {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .metric-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--bg);
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .metric-content .metric-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
    }

    .metric-content .metric-label {
        font-size: 14px;
        color: var(--text);
        margin-top: 2px;
        font-weight: 500;
    }

    .metric-content .metric-detail {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
    }

    /* Level Stats */
    .level-stats-compact {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
    }

    .level-stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 16px;
        background: var(--bg);
        border-radius: 8px;
        border: 1px solid var(--border);
        text-align: center;
    }

    .level-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 14px;
    }

    .level-info .level-name {
        font-size: 12px;
        font-weight: 500;
        color: var(--text);
    }

    .level-info .level-count {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
    }

    /* Export Options */
    .export-options-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .export-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .export-option:hover {
        background: var(--hover);
        border-color: var(--primary);
        transform: translateY(-1px);
    }

    .export-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }

    .export-icon.excel { background: linear-gradient(135deg, #10b981, #059669); }
    .export-icon.pdf { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .export-icon.detailed { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .export-icon.summary { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .export-text .export-title {
        font-size: 14px;
        font-weight: 500;
        color: var(--text);
    }

    .export-text .export-subtitle {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
    }

    /* Insights */
    .insight-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 14px;
        color: var(--text);
    }

    .insight-item:last-child {
        margin-bottom: 0;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .dashboard-row {
            grid-template-columns: 1fr;
        }
        
        .quick-actions-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        
        .export-options-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-tiles {
            grid-template-columns: 1fr;
        }
        
        .feedback-metrics {
            grid-template-columns: 1fr;
        }
        
        .level-stats-compact {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .quick-actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .export-options-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Alerts */
    .alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 16px;
    }

    .alert-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
    }

    .alert-warning {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        color: #92400e;
    }

    .no-data {
        text-align: center;
        padding: 40px 20px;
        color: var(--muted);
    }

    .no-data i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .no-data h3 {
        margin: 0 0 8px 0;
        font-size: 18px;
    }

    .no-data p {
        margin: 0;
        font-size: 14px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Quick action navigation
        $('.quick-action-btn[data-section]').on('click', function(e) {
            e.preventDefault();
            const section = $(this).data('section');
            if (typeof showSection === 'function') {
                showSection(section);
            }
        });
    });

    function exportTeamReport() {
        showLoading();
        const cycleId = $('#cycle-select').val();
        window.location.href = `/manager/export-team-excel?cycle=${cycleId}`;
        setTimeout(hideLoading, 2000);
    }

    function exportTeamPdf() {
        showLoading();
        const cycleId = $('#cycle-select').val();
        window.location.href = `/manager/export-team-pdf?cycle=${cycleId}`;
        setTimeout(hideLoading, 2000);
    }
</script>
@endpush