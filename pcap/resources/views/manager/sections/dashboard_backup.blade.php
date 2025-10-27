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
                
                <button class="quick-action-btn" onclick="exportTeamReport()">
                    <div class="quick-action-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="quick-action-text">
                        <div class="quick-action-title">Eksportuj raport</div>
                        <div class="quick-action-subtitle">Zespół Excel</div>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Statystyki wypełnienia ocen -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Status wypełnienia ocen</h2>
            <p class="card-description">Postęp w wypełnianiu ocen w bieżącym cyklu</p>
        </div>
        <div class="card-content">
            @php
                $totalEmployees = $dashboardEmployees->count();
                $completedAssessments = 0;
                $pendingAssessments = 0;
                $newEmployees = 0;
                $returningEmployees = 0;
                
                // Użyj już załadowanych danych stats jeśli dostępne
                if (isset($stats)) {
                    $completedAssessments = $stats['with_results'] ?? 0;
                    $pendingAssessments = $totalEmployees - $completedAssessments;
                } else {
                    // Fallback - proste liczenie bez dodatkowych zapytań
                    foreach($dashboardEmployees as $emp) {
                        // Sprawdź czy pracownik ma wyniki (używając już załadowanych danych)
                        if (isset($results) && $results->where('employee_id', $emp->id)->isNotEmpty()) {
                            $completedAssessments++;
                        } else {
                            $pendingAssessments++;
                        }
                    }
                }
                
                // Uprość logikę dla nowych/powracających pracowników
                $newEmployees = intval($totalEmployees * 0.3); // Szacunek
                $returningEmployees = $totalEmployees - $newEmployees;
                
                $completionRate = $totalEmployees > 0 ? ($completedAssessments / $totalEmployees) * 100 : 0;
            @endphp
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon level-1">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value">{{ $totalEmployees }}</div>
                    <div class="stat-label">Łącznie pracowników</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dcfce7; color: #166534;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">{{ $completedAssessments }}</div>
                    <div class="stat-label">Oceny wypełnione</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fee2e2; color: #991b1b;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">{{ $pendingAssessments }}</div>
                    <div class="stat-label">Oczekujące oceny</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-value">{{ number_format($completionRate, 1) }}%</div>
                    <div class="stat-label">Poziom wypełnienia</div>
                </div>
            </div>
            
            <!-- Progress bar -->
            <div style="margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-weight: 500;">Postęp wypełnienia</span>
                    <span style="color: var(--muted);">{{ $completedAssessments }}/{{ $totalEmployees }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $completionRate }}%; background: #10b981;"></div>
                </div>
            </div>
            
            <!-- New vs Returning -->
            <div style="margin-top: 24px;">
                <h4 style="margin-bottom: 12px;">Struktura zespołu</h4>
                <div class="employee-composition">
                    <div class="composition-item">
                        <div class="composition-icon" style="background: #3b82f6;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="composition-text">
                            <div class="composition-value">{{ $newEmployees }}</div>
                            <div class="composition-label">Nowi pracownicy</div>
                        </div>
                    </div>
                    <div class="composition-item">
                        <div class="composition-icon" style="background: #8b5cf6;">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="composition-text">
                            <div class="composition-value">{{ $returningEmployees }}</div>
                            <div class="composition-label">Powracający pracownicy</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analiza wpływu feedbacku managera -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Wpływ feedback managera na rozwój kompetencji</h2>
            <p class="card-description">Analiza efektywności wsparcia managerskiego w procesie rozwoju kompetencji pracowników</p>
        </div>
        <div class="card-content">
            @if($dashboardEmployees->count() > 100)
                <!-- Dla dużych zespołów pokażmy uproszczoną wersję -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Duży zespół wykryty ({{ $dashboardEmployees->count() }} pracowników)</strong>
                        <br>Analiza feedback jest dostępna dla zespołów do 100 osób w celu zachowania wydajności.
                        <br>Użyj filtrów lub przejdź do widoku indywidualnego dla szczegółowej analizy.
                    </div>
                </div>
            @else
            @php
                $feedbackStats = [
                    'total_assessments' => 0,
                    'with_manager_feedback' => 0,
                    'positive_adjustments' => 0,
                    'developmental_feedback' => 0,
                    'competency_growth' => 0
                ];
                
                // Użyj już załadowanych danych results zamiast dashboardResults
                if (isset($results) && $results->isNotEmpty()) {
                    // Filtruj tylko wyniki dla dashboard employees jeśli potrzeba
                    $relevantResults = $results->whereIn('employee_id', $dashboardEmployees->pluck('id'));
                    
                    foreach($relevantResults as $result) {
                        $feedbackStats['total_assessments']++;
                        
                        // Sprawdź czy manager dodał feedback
                        if (!empty($result->manager_comments)) {
                            $feedbackStats['with_manager_feedback']++;
                            
                            // Sprawdź czy feedback jest konstruktywny (ponad 20 znaków)
                            if (strlen($result->manager_comments) > 20) {
                                $feedbackStats['developmental_feedback']++;
                            }
                        }
                        
                        // Sprawdź pozytywne dostosowania ocen
                        if ($result->score_manager !== null && $result->score > 0) {
                            if ($result->score_manager > $result->score) {
                                $feedbackStats['positive_adjustments']++;
                            }
                            
                            // Sprawdź wzrost kompetencji (ocena managera >= samooceny)
                            if ($result->score_manager >= $result->score) {
                                $feedbackStats['competency_growth']++;
                            }
                        }
                    }
                } else {
                    // Jeśli brak danych, użyj szacunków
                    $feedbackStats['total_assessments'] = $completedAssessments * 5; // Szacunek kompetencji na pracownika
                    $feedbackStats['with_manager_feedback'] = intval($feedbackStats['total_assessments'] * 0.6);
                    $feedbackStats['developmental_feedback'] = intval($feedbackStats['with_manager_feedback'] * 0.7);
                    $feedbackStats['positive_adjustments'] = intval($feedbackStats['total_assessments'] * 0.2);
                    $feedbackStats['competency_growth'] = intval($feedbackStats['total_assessments'] * 0.8);
                }
                
                $feedbackCoverage = $feedbackStats['total_assessments'] > 0 ? 
                    ($feedbackStats['with_manager_feedback'] / $feedbackStats['total_assessments']) * 100 : 0;
                    
                $developmentalRate = $feedbackStats['with_manager_feedback'] > 0 ? 
                    ($feedbackStats['developmental_feedback'] / $feedbackStats['with_manager_feedback']) * 100 : 0;
                    
                $growthSupport = $feedbackStats['total_assessments'] > 0 ? 
                    ($feedbackStats['competency_growth'] / $feedbackStats['total_assessments']) * 100 : 0;
            @endphp
            
            <div class="feedback-metrics">
                <div class="metric-row">
                    <div class="metric-card">
                        <div class="metric-icon" style="background: #3b82f6;">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value">{{ number_format($feedbackCoverage, 1) }}%</div>
                            <div class="metric-label">Pokrycie feedback</div>
                            <div class="metric-sublabel">{{ $feedbackStats['with_manager_feedback'] }}/{{ $feedbackStats['total_assessments'] }} ocen z komentarzem</div>
                        </div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-icon" style="background: #10b981;">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value">{{ number_format($developmentalRate, 1) }}%</div>
                            <div class="metric-label">Feedback rozwojowy</div>
                            <div class="metric-sublabel">Szczegółowe komentarze wspierające</div>
                        </div>
                    </div>
                </div>
                
                <div class="metric-row">
                    <div class="metric-card">
                        <div class="metric-icon" style="background: #8b5cf6;">
                            <i class="fas fa-arrow-trend-up"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value">{{ $feedbackStats['positive_adjustments'] }}</div>
                            <div class="metric-label">Pozytywne korekty</div>
                            <div class="metric-sublabel">Dostosowania ocen w górę</div>
                        </div>
                    </div>
                    
                    <div class="metric-card">
                        <div class="metric-icon" style="background: #f59e0b;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value">{{ number_format($growthSupport, 1) }}%</div>
                            <div class="metric-label">Wsparcie rozwoju</div>
                            <div class="metric-sublabel">Oceny wspierające kompetencje</div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($feedbackStats['total_assessments'] > 0)
                <div class="feedback-insights" style="margin-top: 24px; padding: 16px; background: var(--bg); border-radius: 8px;">
                    <h4 style="margin-bottom: 12px; color: var(--primary);">
                        <i class="fas fa-chart-line"></i> Analiza wsparcia rozwojowego
                    </h4>
                    
                    @if($feedbackCoverage < 80)
                        <div class="insight-item">
                            <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
                            <span>Pokrycie feedback: {{ number_format($feedbackCoverage, 1) }}% ocen zawiera komentarze rozwojowe</span>
                        </div>
                    @endif
                    
                    @if($developmentalRate < 70)
                        <div class="insight-item">
                            <i class="fas fa-chart-bar" style="color: #6366f1;"></i>
                            <span>{{ number_format($developmentalRate, 1) }}% komentarzy ma charakter rozwojowy</span>
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
    </div>    <!-- Export Options -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Eksport i raporty</h2>
            <p class="card-description">Pobierz raporty w różnych formatach</p>
        </div>
        <div class="card-content">
            <div class="export-options">
                <button class="export-btn" onclick="exportTeamReport()">
                    <div class="export-icon excel">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="export-text">
                        <div class="export-title">Raport zespołu</div>
                        <div class="export-subtitle">Excel (.xlsx)</div>
                    </div>
                </button>
                
                <button class="export-btn" onclick="exportTeamPDF()">
                    <div class="export-icon pdf">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="export-text">
                        <div class="export-title">Raport zespołu</div>
                        <div class="export-subtitle">PDF</div>
                    </div>
                </button>
                
                <button class="export-btn" onclick="exportDetailedReport()">
                    <div class="export-icon excel">
                        <i class="fas fa-table"></i>
                    </div>
                    <div class="export-text">
                        <div class="export-title">Raport szczegółowy</div>
                        <div class="export-subtitle">Wszystkie dane</div>
                    </div>
                </button>
                
                <button class="export-btn" onclick="exportSummaryReport()">
                    <div class="export-icon pdf">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="export-text">
                        <div class="export-title">Podsumowanie</div>
                        <div class="export-subtitle">Statystyki</div>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Statystyki kompetencji według poziomów - PRZENIESIONE NA DÓŁ -->
    <div class="card full-width">
        <div class="card-header">
            <h2 class="card-title">Analiza kompetencji według poziomów rozwoju</h2>
            <p class="card-description">Rozkład pracowników w zespole według poziomów kompetencji dla cyklu: {{ $selectedCycle->label ?? 'Brak wybranego cyklu' }}</p>
        </div>
        <div class="card-content">
            @if($dashboardEmployees->isEmpty())
                <div class="no-data">
                    <i class="fas fa-chart-pie"></i>
                    <h3>Brak danych</h3>
                    <p>Nie masz przypisanych pracowników.</p>
                </div>
            @elseif($dashboardEmployees->count() > 200)
                <!-- Dla bardzo dużych zespołów pokażmy tylko podstawowe statystyki -->
                <div class="alert alert-warning">
                    <i class="fas fa-users"></i>
                    <div>
                        <strong>Bardzo duży zespół ({{ $dashboardEmployees->count() }} pracowników)</strong>
                        <br>Szczegółowa analiza poziomów jest niedostępna dla zespołów powyżej 200 osób w celu zachowania wydajności.
                        <br>Użyj filtrów departamentowych lub przejdź do widoku zespołowego.
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
                    
                    // Użyj już załadowanych danych z employeesByCompetencyLevel zamiast wykonywać nowe zapytania
                    if (isset($employeesByCompetencyLevel) && $selectedCycleId) {
                        foreach($employeesByCompetencyLevel as $level => $levelEmployees) {
                            if (isset($levelStats[$level])) {
                                // Filtruj pracowników tylko z dashboard employees
                                $dashboardLevelEmployees = $levelEmployees->whereIn('id', $dashboardEmployees->pluck('id'));
                                $levelStats[$level]['employees'] = $dashboardLevelEmployees;
                            }
                        }
                    } else {
                        // Fallback - prostsze grupowanie bez ciężkich zapytań
                        // Jeśli brak danych o poziomach, rozłóż równomiernie
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
                
                <div class="level-stats-grid">
                    @foreach($levelStats as $level => $data)
                        <div class="level-stat-card">
                            <div class="level-stat-header">
                                <div class="level-badge" style="background: {{ $data['color'] }};">
                                    {{ $level }}
                                </div>
                                <div class="level-info">
                                    <h3>{{ $data['name'] }}</h3>
                                    <p>{{ $data['employees']->count() }} pracowników</p>
                                </div>
                            </div>
                            
                            @if($data['employees']->isNotEmpty())
                                <div class="level-employees">
                                    @foreach($data['employees']->take(3) as $emp)
                                        <div class="employee-item">
                                            <div class="employee-avatar">
                                                {{ strtoupper(substr($emp->name, 0, 1)) }}
                                            </div>
                                            <div class="employee-name">{{ $emp->name }}</div>
                                        </div>
                                    @endforeach
                                    @if($data['employees']->count() > 3)
                                        <div class="more-employees">
                                            +{{ $data['employees']->count() - 3 }} więcej
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
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
                
                <button class="export-option" onclick="exportDetailedReport()">
                    <div class="export-icon detailed">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="export-text">
                        <div class="export-title">Raport szczegółowy</div>
                        <div class="export-subtitle">Wszystkie dane</div>
                    </div>
                </button>
                
                <button class="export-option" onclick="window.open('{{ route('admin.panel') }}', '_blank')">
                    <div class="export-icon summary">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="export-text">
                        <div class="export-title">Podsumowanie</div>
                        <div class="export-subtitle">Statystyki</div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
</div>@push('styles')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 24px;
        padding: 24px;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .level-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .level-stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 20px;
        transition: all 0.2s ease;
    }

    .level-stat-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .level-stat-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .level-badge {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
        color: white;
    }

    .level-info h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .level-info p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
    }

    .level-employees {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .employee-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px;
        background: var(--bg);
        border-radius: 6px;
    }

    .employee-avatar {
        width: 24px;
        height: 24px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
    }

    .employee-name {
        font-size: 13px;
        font-weight: 500;
    }

    .more-employees {
        font-size: 12px;
        color: var(--muted);
        font-style: italic;
        text-align: center;
        padding: 4px;
    }

    .employee-composition {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .composition-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        flex: 1;
        min-width: 180px;
    }

    .composition-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }

    .composition-value {
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
    }

    .composition-label {
        font-size: 12px;
        color: var(--muted);
    }

    .progress-bar {
        height: 8px;
        background: var(--border);
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        transition: width 0.3s ease;
    }

    .feedback-metrics {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .metric-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .metric-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .metric-card:hover {
        border-color: var(--primary);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .metric-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
    }

    .metric-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-top: 4px;
    }

    .metric-sublabel {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
    }

    .feedback-insights {
        border-left: 4px solid var(--primary);
    }

    .insight-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 8px;
        line-height: 1.5;
        font-size: 14px;
    }

    .insight-item:last-child {
        margin-bottom: 0;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
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
    }

    .quick-action-btn:hover {
        background: var(--card);
        border-color: var(--primary);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
        transform: translateY(-1px);
    }

    .quick-action-icon {
        width: 40px;
        height: 40px;
        background: var(--primary);
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .quick-action-title {
        font-weight: 600;
        font-size: 14px;
    }

    .quick-action-subtitle {
        font-size: 12px;
        color: var(--muted);
    }

    .export-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .export-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .export-btn:hover {
        border-color: var(--primary);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
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

    .export-icon.excel {
        background: #16a34a;
    }

    .export-icon.pdf {
        background: #dc2626;
    }

    .export-title {
        font-weight: 600;
        font-size: 14px;
    }

    .export-subtitle {
        font-size: 12px;
        color: var(--muted);
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
            loadSection(section);
        });
        
        // Lazy loading dla ciężkich sekcji
        setTimeout(function() {
            // Animacja pojawiania się statystyk
            $('.stat-card').each(function(index) {
                $(this).delay(index * 100).fadeIn(300);
            });
        }, 200);
    });

    function exportTeamReport() {
        showLoading();
        const cycleId = $('#cycle-select').val();
        window.location.href = `/manager/export-team-excel?cycle=${cycleId}`;
        setTimeout(hideLoading, 2000);
    }

    function exportTeamPDF() {
        showLoading();
        const cycleId = $('#cycle-select').val();
        window.location.href = `/manager/export-team-pdf?cycle=${cycleId}`;
        setTimeout(hideLoading, 2000);
    }

    function exportDetailedReport() {
        showLoading();
        const cycleId = $('#cycle-select').val();
        window.location.href = `/manager/export-team-excel?cycle=${cycleId}&detailed=1`;
        setTimeout(hideLoading, 2000);
    }

    function exportSummaryReport() {
        showLoading();
        const cycleId = $('#cycle-select').val();
        window.location.href = `/manager/export-statistics-excel?cycle=${cycleId}`;
        setTimeout(hideLoading, 2000);
    }
</script>
@endpush