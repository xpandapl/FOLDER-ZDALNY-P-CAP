@foreach($competencies as $competency)
    <div class="question">
        <div class="question-header">
            <div class="badge-container">
                <span class="badge competency">{{ $competency->competency_type }}</span> <!-- Kolumna B -->
                <span class="badge level">Poziom {{ $competency->level }}</span> <!-- Kolumna A -->
            </div>
        </div>
        <label>{{ $competency->competency_name }}:</label> <!-- Kolumna C -->
        <div class="slider-container">

            <input type="range" min="0" max="1" step="0.25" value="0" class="slider" name="q{{ $competency->id }}" onchange="updateSliderValue(this)">
        </div>
        <div class="description">{{ $competency->description_075_to_1 }}</div> <!-- Opis z kolumny D -->
    </div>
@endforeach
