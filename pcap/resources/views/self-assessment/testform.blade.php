<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz samooceny P-CAP</title>

    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    width: 100%;
}

.container-wrapper {
    display: flex;
    justify-content: center;
    width: 100%;
}

.container {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 20px;
}

.level-header {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.question {
    background-color: white;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    box-sizing: border-box;
    display: block;
}

.question-header {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #333;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.badge-container {
    display: flex;
    gap: 10px;
}

.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 12px;
    color: white;
    font-size: 14px;
}

.badge.competency {
    background-color: #2196F3; /* Widoczny kolor dla competency */
}

.badge.level {
    /*display: none; *//* Ukrycie badge dla poziomu */
    background-color: lightgrey;
    color:black;
}

.slider-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    position: relative;
}

.slider {
    -webkit-appearance: none;
    width: 100%;
    height: 8px;
    border-radius: 5px;
    background: #d3d3d3;
    outline: none;
    transition: opacity .2s;
}

.slider:hover {
    opacity: 1;
}

.slider-labels {
    display: flex;
    justify-content: space-between;
    width: 100%;
    color: #666;
    font-size: 14px;
    margin-top: 10px;
}

.toggle-checkbox {
    display: flex;
    align-items: center;
    gap: 5px;
    text-align: center;
}

.custom-toggle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #d3d3d3;
    position: relative;
    cursor: pointer;
    display: inline-block;
    transition: background-color 0.2s;
}

.toggle-checkbox input[type="checkbox"] {
    display: none;
}

.toggle-checkbox input[type="checkbox"]:checked + .custom-toggle {
    background-color: #2196F3;
}

.label-text {
    font-size: 14px;
    display: inline-block;
    line-height: 1.2;
}

.textarea-description {
    display: none;
    margin-top: 10px;
}

.textarea-description textarea {
    width: 100%;
    height: 80px;
    padding: 10px;
    font-size: 14px;
    border-radius: 4px;
    border: 1px solid #ccc;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    resize: none;
}

.checkbox-container {
    display: flex;
    flex-direction: row;
    gap: 10px;
    align-items: center;
    margin-top: 10px;
}

.checkbox-container input[type="checkbox"] {
    margin-right: 5px;
}

.checkbox-container label {
    font-size: 14px;
    color: #333;
}

.above-expectations-container {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-top: 10px;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000; /* Dodaj tę linię */
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 25px;
    border-radius: 8px;
    width: 400px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.modal-content p {
    font-size: 16px;
    font-weight: 500;
    color: #444;
    margin-bottom: 20px;
}

.modal-content button {
    background-color: #2196F3;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    border: none;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin: 0 10px;
}

.modal-content button:hover {
    background-color: #1976d2;
}

button {
    font-size: 16px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}
.badge.osobiste {
    background-color: #2196F3; /* niebieski */
}

.badge.spoleczne {
    background-color: #4CAF50; /* zielony */
}

.badge.liderskie {
    background-color: #9C27B0; /* fioletowy */
}

.badge.zawodowe-logistics {
    background-color: #FF9800; /* pomarańczowy */
}

.badge.zawodowe-growth {
    background-color: #009688; /* turkusowy */
}

.badge.zawodowe-inne {
    background-color: #F44336; /* czerwony */
}
.skip-button {
    background-color: #f44336;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    align-self: flex-start;
}

.skip-button:hover {
    background-color: #d32f2f;
}
.back-button {
    background-color: #ccc;
    color: #333;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    align-self: flex-end;
}

.back-button:hover {
    background-color: #bbb;
}
.save-button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 20px;
    align-self: flex-end;
}

.save-button:hover {
    background-color: #45a049;
}


</style>

<script>
    let completedCount = 0;
    let totalQuestions = {{ $competencies->count() }};
    let osobisteCompleted = 0;
    let spoleczneCompleted = 0;
    let liderskieCompleted = 0;
    let zawodoweCompleted = 0;

    function updateProgress() {
        completedCount = 0;
        osobisteCompleted = 0;
        spoleczneCompleted = 0;
        liderskieCompleted = 0;
        zawodoweCompleted = 0;

        document.querySelectorAll('.slider').forEach(slider => {
            if (slider.value > 0) {
                completedCount++;
                const competencyType = slider.closest('.question').dataset.competencyType;
                if (competencyType.includes('1. Osobiste')) osobisteCompleted++;
                if (competencyType.includes('2. Społeczne')) spoleczneCompleted++;
                if (competencyType.includes('4. Liderskie')) liderskieCompleted++;
                if (competencyType.includes('3.')) zawodoweCompleted++;
            }
        });

        document.getElementById('completed').innerText = completedCount;
        document.getElementById('osobiste-completed').innerText = osobisteCompleted;
        document.getElementById('spoleczne-completed').innerText = spoleczneCompleted;
        document.getElementById('liderskie-completed').innerText = liderskieCompleted;
        document.getElementById('zawodowe-completed').innerText = zawodoweCompleted;
    }

    function toggleAboveExpectations(checkbox) {
    const question = checkbox.closest('.question');
    const slider = question.querySelector('.slider');
    const descriptionCheckbox = question.querySelector('.add-description-container input[type="checkbox"]');
    const commentContainer = question.querySelector('.textarea-description');
    const descriptionDiv = question.querySelector('.slider-description');

    if (checkbox.checked) {
        // Ustawienie suwaka na 1
        slider.value = 1;
        slider.disabled = true; // Zablokuj suwak

        // Wyświetlenie opisu "Powyżej oczekiwań"
        descriptionDiv.textContent = question.dataset.descriptionAboveExpectations;
        descriptionDiv.style.display = 'block';

        // Zaznacz i zablokuj checkbox "Dodaj opis/argumentację"
        descriptionCheckbox.checked = true;
        descriptionCheckbox.disabled = true;
        commentContainer.style.display = 'block';
        commentContainer.querySelector('textarea').required = true;
    } else {
        // Odblokuj suwak
        slider.disabled = false;

        // Odblokuj i odznacz checkbox "Dodaj opis/argumentację"
        descriptionCheckbox.disabled = false;
        descriptionCheckbox.checked = false;
        commentContainer.style.display = 'none';
        commentContainer.querySelector('textarea').required = false;

        // Aktualizacja opisu na podstawie wartości suwaka
        updateSliderValue(slider);
    }
}



    function updateSliderValue(slider) {
    const value = parseFloat(slider.value);
    const question = slider.closest('.question');
    const descriptionDiv = question.querySelector('.slider-description');

    if (value >= 0.75) {
        descriptionDiv.textContent = question.dataset.description075to1;
        descriptionDiv.style.display = 'block';
    } else if (value <= 0.5 && value >= 0) {
        descriptionDiv.textContent = question.dataset.description0to05;
        descriptionDiv.style.display = 'block';
    } else {
        descriptionDiv.style.display = 'none';
    }
}


// Funkcja, która pokazuje pole tekstowe przy zaznaczeniu checkboxa "Dodaj opis/argumentację"
function toggleDescriptionInput(checkbox) {
    const question = checkbox.closest('.question');
    const commentContainer = question.querySelector('.textarea-description');  // Zmieniono na textarea-description

    if (checkbox.checked) {
        commentContainer.style.display = 'block';
        commentContainer.querySelector('textarea').required = true;
    } else {
        commentContainer.style.display = 'none';
        commentContainer.querySelector('textarea').required = false;
    }
}
    document.addEventListener("DOMContentLoaded", function() {
        updateProgress();

        // Inicjalizacja suwaków i opisów
        document.querySelectorAll('.slider').forEach(slider => {
            updateSliderValue(slider);
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
    var skipButton = document.getElementById("skipButton");
    var skipModal = document.getElementById("skipModal");
    var confirmSkip = document.getElementById("confirmSkip");
    var cancelSkip = document.getElementById("cancelSkip");

    // Wyświetlanie okna modalnego po kliknięciu "Pomiń resztę samooceny"
    skipButton.addEventListener("click", function() {
        skipModal.style.display = "flex";
    });

    // Potwierdzenie pominięcia - przekierowanie do zakończenia samooceny
    confirmSkip.addEventListener("click", function() {
        // Po potwierdzeniu zakończamy samoocenę i przechodzimy do ekranu zakończenia
        window.location.href = "{{ route('self.assessment.complete') }}";
    });

    // Anulowanie pominięcia - ukrywanie modala
    cancelSkip.addEventListener("click", function() {
        skipModal.style.display = "none";
    });
});

</script>

</head>
<body>
    <div class="container-wrapper">
        <div class="container">
            <!-- Wyświetlenie nagłówka poziomu -->
            <div class="level-header">
                Poziom: {{ $currentLevelName }}
            </div>
            @if ($currentLevel > 1)
                <button type="button" id="skipButton" class="skip-button">Pomiń resztę samooceny</button>
            @endif

            <!-- Modal -->
            <div id="skipModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <p>Pominięcie dalszej oceny oznacza, że nie chcesz oceniać się na tym oraz wyższych poziomach i to jest w porządku. Potwierdź tylko, czy to jest to, co miałeś na myśli.</p>
                    <button id="confirmSkip" class="confirm-button">Tak, pomiń pozostałe pytania</button>
                    <button id="cancelSkip" class="cancel-button">Wróć</button>
                </div>
            </div>


            <!-- Główny formularz -->
            <form action="{{ route('save_results') }}" method="POST" id="assessmentForm">
                @csrf

                <!-- Ukryte pola z danymi użytkownika -->
                <input type="hidden" name="name" value="{{ session('name') }}">
                <input type="hidden" name="email" value="{{ session('email') }}">
                <input type="hidden" name="department" value="{{ session('department') }}">
                <input type="hidden" name="current_level" value="{{ $currentLevel }}">

                @php
                    function getCompetencyClass($competencyType) {
                        if (strpos($competencyType, '1. Osobiste') !== false) {
                            return 'osobiste';
                        } elseif (strpos($competencyType, '2. Społeczne') !== false) {
                            return 'spoleczne';
                        } elseif (strpos($competencyType, '3.L.') !== false) {
                            return 'zawodowe-logistics';
                        } elseif (strpos($competencyType, '3.G.') !== false) {
                            return 'zawodowe-growth';
                        } elseif (strpos($competencyType, '3.') !== false) {
                            return 'zawodowe-inne';
                        } elseif (strpos($competencyType, '4. Liderskie') !== false) {
                            return 'liderskie';
                        } else {
                            return '';
                        }
                    }
                @endphp


                <!-- Pętla wyświetlająca pytania -->
                @foreach($competencies as $competency)
                <div class="question" data-description0to05="{{ $competency->description_0_to_05 }}"
                     data-description075to1="{{ $competency->description_075_to_1 }}"
                     data-description-above-expectations="{{ $competency->description_above_expectations }}"
                     data-competency-type="{{ $competency->competency_type }}">

                        <div class="question-header">
                            <div class="badge-container">
                            <span class="badge competency {{ getCompetencyClass($competency->competency_type) }}">{{ $competency->competency_type }}</span>
                                <!-- Badge level jest ukryty przez CSS -->
                                <span class="badge level">Poziom {{ $competency->level }}</span>
                            </div>
                        </div>
                        <label>{{ $competency->competency_name }}:</label>
                        <div class="slider-container">
                            <div class="slider-labels">
                                <div class="slider-label">0</div>
                                <div class="slider-label">0.25</div>
                                <div class="slider-label">0.5</div>
                                <div class="slider-label">0.75</div>
                                <div class="slider-label">1</div>
                            </div>
                            <input type="hidden" name="competency_id[]" value="{{ $competency->id }}">
                            <input type="range" min="0" max="1" step="0.25" value="{{ $savedAnswers['score'][$competency->id] ?? 0 }}" class="slider" name="score[{{ $competency->id }}]" onchange="updateSliderValue(this)">

                        </div>

                        <!-- Opis suwaka -->
                        <div class="slider-description" style="display:none; margin-top:15px;"></div>

                        <!-- Checkbox "Powyżej oczekiwań" -->
                        <div class="above-expectations-container">
                            <label class="toggle-checkbox">
                                <input type="checkbox" name="above_expectations[{{ $competency->id }}]" onclick="toggleAboveExpectations(this)">
                                <span class="custom-toggle"></span>
                                <span class="label-text">Powyżej oczekiwań</span>
                            </label>
                        </div>

                        <!-- Checkbox "Dodaj opis/argumentację" -->
                        <div class="add-description-container">
                        <input type="checkbox" name="add_description[{{ $competency->id }}]" onclick="toggleDescriptionInput(this)">
                            <label>Dodaj opis/argumentację</label>
                        </div>

                        <!-- Pole tekstowe na opis -->
                        <div class="textarea-description" style="display:none;">
                            <textarea name="comments[{{ $competency->id }}]" placeholder="Wpisz opis/argumentację..."></textarea>

                        </div>
                    </div>
                @endforeach

                <!-- Przycisk Submit -->
                <div style="display: flex; justify-content: space-between;">
                    <button type="submit" name="back" class="back-button">Wróć</button>
                    @if ($currentLevel == 6)
                        <button type="submit" name="submit" class="save-button">Wyślij</button>
                    @else
                        <button type="submit" name="next" class="save-button">Przejdź dalej</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</body>



</html>
