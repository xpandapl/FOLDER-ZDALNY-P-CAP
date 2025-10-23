@extends('layouts.self-assessment')

@section('content')
<style>
/* Scoped styles for complete view - consistent with other assessment pages */
.sa-complete { max-width: 800px; margin: 0 auto; }
.sa-complete .success-icon { color: #059669; font-size: 28px; margin-right: 8px; }
.sa-complete .lead { color:#4b5563; margin-top:6px; }
.sa-complete .actions { display:flex; gap:12px; align-items:center; justify-content:center; margin: 20px 0; flex-wrap:wrap; }
.sa-complete .card { border:1px solid #e5e7eb; border-radius:14px; padding:20px; background:#fff; box-shadow:0 4px 14px rgba(15,23,42,.06); margin: 16px 0; }
.sa-complete .edit-section { text-align:center; }
.sa-complete .edit-section p { color:#6b7280; margin-bottom:12px; }
.sa-complete .link-row { display:flex; gap:8px; align-items:center; justify-content:center; margin-bottom:16px; }
.sa-complete .link-input { flex:1; max-width:480px; border:1px solid #d1d5db; border-radius:10px; padding:10px 12px; font-size:14px; background:#f9fafb; }
.sa-complete .home-section { text-align:center; margin-top:20px; padding-top:20px; border-top:1px solid #e5e7eb; }
/* Button System - Consistent Hierarchy */
.sa-complete .btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 44px;
    justify-content: center;
}

/* Primary Button - Main actions (Submit, Save, Confirm) */
.sa-complete .btn-primary {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}
.sa-complete .btn-primary:hover {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
    text-decoration: none;
}

/* Secondary Button - Supporting actions (Copy, Cancel) */
.sa-complete .btn-secondary {
    background: white;
    color: #374151;
    border-color: #d1d5db;
}
.sa-complete .btn-secondary:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #374151;
    text-decoration: none;
}

/* Text Button - Low priority actions (Back, Links) */
.sa-complete .btn-text {
    background: transparent;
    color: #6b7280;
    border-color: transparent;
    padding: 8px 12px;
}
.sa-complete .btn-text:hover {
    background: #f3f4f6;
    color: #374151;
    text-decoration: none;
}

/* Assessment Summary Styles */
.sa-complete .assessment-summary-section { margin: 24px 0; }
.sa-complete .assessment-results-card { border: 1px solid #e0e7ff; background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%); }
.sa-complete .card-header { text-align: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #e0e7ff; }
.sa-complete .card-header h2 { color: #3730a3; font-size: 1.5em; margin: 0; font-weight: 600; }
.sa-complete .card-header i { margin-right: 8px; color: #6366f1; }

/* Overall Results */
.sa-complete .overall-results-container { display: flex; justify-content: center; margin-bottom: 32px; }
.sa-complete .result-item { display: flex; align-items: center; padding: 24px 32px; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.12); border: 2px solid #f1f5f9; }
.sa-complete .result-item.centered { max-width: 400px; width: 100%; }
.sa-complete .result-icon { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; font-size: 24px; }
.sa-complete .achieved-level-item .result-icon { background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
.sa-complete .result-content { flex: 1; }
.sa-complete .result-label { display: block; font-size: 0.9em; color: #64748b; font-weight: 500; margin-bottom: 6px; }
.sa-complete .level-badge { display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 20px; font-weight: 600; font-size: 1.1em; }
.sa-complete .percentage-badge { display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border-radius: 20px; font-weight: 600; font-size: 1.1em; }

/* Level Breakdown */
.sa-complete .level-breakdown-section { margin-top: 32px; }
.sa-complete .breakdown-title { text-align: center; color: #374151; font-size: 1.2em; margin-bottom: 20px; font-weight: 600; }
.sa-complete .breakdown-title i { margin-right: 8px; color: #6366f1; }
.sa-complete .levels-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }

/* Level Cards */
.sa-complete .level-card { background: #ffffff; border-radius: 12px; padding: 20px; transition: all 0.3s ease; position: relative; overflow: hidden; }
.sa-complete .level-card.achieved { border: 2px solid #10b981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); }
.sa-complete .level-card.not-achieved { border: 2px solid #ef4444; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15); }
.sa-complete .level-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
.sa-complete .level-card.achieved::before { background: linear-gradient(90deg, #10b981, #059669); }
.sa-complete .level-card.not-achieved::before { background: linear-gradient(90deg, #ef4444, #dc2626); }

.sa-complete .level-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.sa-complete .level-name { margin: 0; color: #374151; font-size: 1.1em; font-weight: 600; }
.sa-complete .achievement-badge { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.sa-complete .achievement-badge { background: #10b981; color: white; }
.sa-complete .achievement-badge.not-achieved { background: #ef4444; color: white; }

.sa-complete .level-metrics { text-align: center; }
.sa-complete .percentage-display { margin-bottom: 12px; }
.sa-complete .percentage-value { font-size: 2em; font-weight: 700; }
.sa-complete .level-card.achieved .percentage-value { color: #059669; }
.sa-complete .level-card.not-achieved .percentage-value { color: #dc2626; }

.sa-complete .points-display { margin-bottom: 16px; color: #6b7280; font-size: 1em; }
.sa-complete .points-earned { font-weight: 600; color: #374151; }
.sa-complete .points-separator { margin: 0 4px; }
.sa-complete .points-total { font-weight: 500; }
.sa-complete .points-label { margin-left: 4px; font-size: 0.9em; }

.sa-complete .achievement-status { padding: 8px 12px; border-radius: 20px; font-size: 0.9em; font-weight: 500; }
.sa-complete .achievement-status.achieved { background: #d1fae5; color: #065f46; }
.sa-complete .achievement-status.not-achieved { background: #fee2e2; color: #991b1b; }
.sa-complete .achievement-status i { margin-right: 6px; }

@media (max-width:760px){ 
  .sa-complete { padding: 0 6px; } 
  .sa-complete .actions { flex-direction:column; gap:8px; }
  .sa-complete .link-row { flex-direction:column; gap:8px; }
  .sa-complete .link-input { max-width:100%; }
  .sa-complete .result-item.centered { max-width: 100%; }
  .sa-complete .levels-grid { grid-template-columns: 1fr; }
  .sa-complete .result-item { padding: 16px; }
  .sa-complete .percentage-value { font-size: 1.6em; }
}
</style>

    <div class="sa-complete">
        <input type="hidden" name="uuid" value="{{ $uuid }}">
        
        <div class="center" style="margin-bottom: 12px;">
            <h1><i class="fas fa-check-circle success-icon"></i>Ocena zakończona</h1>
            <p class="lead">Dziękujemy za wypełnienie formularza samooceny.</p>
        </div>

        @if(isset($assessmentSummary))
        <div class="assessment-summary-section">
            <div class="card assessment-results-card">
                <div class="card-header">
                    <h2><i class="fas fa-chart-line"></i> Podsumowanie Twojej Oceny</h2>
                </div>
                
                <div class="card-body">
                    <!-- Overall Results -->
                    <div class="overall-results-container">
                        <div class="result-item achieved-level-item centered">
                            <div class="result-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="result-content">
                                <span class="result-label">Twój osiągnięty poziom</span>
                                <span class="level-badge achieved">{{ $assessmentSummary['achievedLevel'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Level Breakdown -->
                    <div class="level-breakdown-section">
                        <h3 class="breakdown-title">
                            <i class="fas fa-tasks"></i> Szczegółowe wyniki według poziomów
                        </h3>
                        
                        @php
                            // Define thresholds for each level
                            $thresholds = [
                                1 => 80, // Junior ≥80%
                                2 => 85, // Specjalista ≥85%
                                3 => 85, // Senior ≥85%
                                4 => 80, // Supervisor ≥80%
                                5 => 80, // Manager ≥80%
                            ];
                        @endphp
                        
                        <div class="levels-grid">
                            @foreach($assessmentSummary['levelSummaries'] as $levelNumber => $summary)
                            @php
                                // Extract numeric level from levelNumber
                                preg_match('/^(\d+)/', $levelNumber, $matches);
                                $numericLevel = isset($matches[1]) ? (int)$matches[1] : 1;
                                $requiredThreshold = $thresholds[$numericLevel] ?? 50;
                            @endphp
                            <div class="level-card {{ $summary['percentage'] >= $requiredThreshold ? 'achieved' : 'not-achieved' }}">
                                <div class="level-header">
                                    <h4 class="level-name">{{ $summary['levelName'] }}</h4>
                                    @if($summary['percentage'] >= $requiredThreshold)
                                        <div class="achievement-badge">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    @else
                                        <div class="achievement-badge not-achieved">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="level-metrics">
                                    <div class="percentage-display">
                                        <span class="percentage-value">{{ $summary['percentage'] }}%</span>
                                    </div>
                                    
                                    <div class="points-display">
                                        <span class="points-earned">{{ $summary['earnedPoints'] }}</span>
                                        <span class="points-separator">/</span>
                                        <span class="points-total">{{ $summary['maxPoints'] }}</span>
                                        <span class="points-label">pkt</span>
                                    </div>
                                    
                                    @if($summary['percentage'] >= $requiredThreshold)
                                        <div class="achievement-status achieved">
                                            <i class="fas fa-trophy"></i> Poziom osiągnięty
                                        </div>
                                    @else
                                        <div class="achievement-status not-achieved">
                                            <i class="fas fa-target"></i> Do osiągnięcia: {{ $requiredThreshold - $summary['percentage'] }}%
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="actions">
            <a href="{{ route('self.assessment.generate_pdf', ['uuid' => $uuid]) }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Pobierz PDF
            </a>
            <a href="{{ route('self.assessment.generate_xls', ['uuid' => $uuid]) }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-file-excel"></i> Pobierz XLS
            </a>
        </div>

        <div class="card">
            <div class="edit-section">
                <p>Zapisz ten link. Umożliwi edycję odpowiedzi do czasu zamknięcia okna samooceny.</p>
                <div class="link-row">
                    <input type="text" id="editLink" value="{{ route('form.edit', ['uuid' => $uuid]) }}" readonly class="link-input">
                    <button onclick="copyToClipboard()" class="btn btn-secondary">
                        <i class="fas fa-copy"></i> Kopiuj link
                    </button>
                </div>
            </div>
            
            <div class="home-section">
                <a href="{{ url('/') }}" class="btn btn-text">
                    <i class="fas fa-home"></i> Powrót do strony głównej
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function copyToClipboard(){
    var el = document.getElementById('editLink');
    el.select();
    el.setSelectionRange(0, 99999);
    
    // Modern clipboard API with fallback
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(el.value).then(function() {
            alert('Link został skopiowany do schowka.');
        });
    } else {
        // Fallback for older browsers
        document.execCommand('copy');
        alert('Link został skopiowany do schowka.');
    }
}
</script>
@endsection
