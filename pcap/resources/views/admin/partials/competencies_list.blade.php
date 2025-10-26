@if(isset($competencies) && $competencies->count() > 0)
    @foreach($competencies->groupBy('level') as $level => $levelCompetencies)
    <div class="level-group">
        <h3 class="level-title">{{ $level }}</h3>
        <div class="competencies-grid">
            @foreach($levelCompetencies as $competency)
            <div class="competency-card">
                <div class="competency-header">
                    <div class="competency-name">{{ $competency->competency_name }}</div>
                    <div class="competency-type">{{ $competency->competency_type }}</div>
                </div>
                <div class="competency-descriptions">
                    @if($competency->description_025)
                        <div class="description-item">
                            <label>Wymaga poprawy (0.25):</label>
                            <p>{{ Str::limit($competency->description_025, 100) }}</p>
                        </div>
                    @endif
                    @if($competency->description_0_to_05)
                        <div class="description-item">
                            <label>Zgodnie z oczekiwaniami (0.5):</label>
                            <p>{{ Str::limit($competency->description_0_to_05, 100) }}</p>
                        </div>
                    @endif
                    @if($competency->description_075_to_1)
                        <div class="description-item">
                            <label>Powyżej oczekiwań (0.75-1):</label>
                            <p>{{ Str::limit($competency->description_075_to_1, 100) }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    
    <div class="load-more">
        @if($search)
            <p>Znaleziono: {{ $competencies->count() }} kompetencji dla "{{ $search }}"</p>
        @else
            <p>Wyświetlane: {{ $competencies->count() }} z {{ $totalCompetencies }} kompetencji</p>
        @endif
        @if($competencies->count() >= 20 && !$search)
            <p class="text-muted">Użyj wyszukiwarki aby znaleźć konkretne kompetencje</p>
        @elseif($competencies->count() >= 40 && $search)
            <p class="text-muted">Wyniki ograniczone do 40. Użyj bardziej precyzyjnego wyszukiwania</p>
        @endif
    </div>
@else
    <div class="empty-state">
        @if($search)
            <h3>Brak wyników</h3>
            <p>Nie znaleziono kompetencji dla wyszukiwania "{{ $search }}"</p>
        @else
            <h3>Brak kompetencji</h3>
            <p>Nie dodano jeszcze żadnych kompetencji do systemu.</p>
            <a href="{{ route('upload.excel') }}" class="btn btn-primary">Importuj pierwsze kompetencje</a>
        @endif
    </div>
@endif