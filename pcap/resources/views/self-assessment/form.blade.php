<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz samooceny P-CAP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

.badge.level { background-color: #e0e0e0; color:#000; }

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
    width: 500px;
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

.user-info {
    position: fixed;
    top: 10px;
    right: 10px;
    color: grey;
    font-size: 12px;
    text-align: right;
}

.edit-link-wrapper {
    display: flex;
    align-items: center;
    margin-top: 5px;
}

.edit-link-wrapper input {
    width: 300px;
    font-size: 10px;
}

.copy-button {
    background-color: #4CAF50;
    border: none;
    font-size: 10px;
    margin-left: 5px;
    scale: 0.7;
}

.copy-button:hover {
    background-color: #45a049;
}

.cancel-button {
    background-color: #f44336;
    color: white;
    padding: 8px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.cancel-button:hover {
    background-color: #d32f2f;
}

.user-card {
    position: fixed;
    top: 10px;
    right: 10px;
    background-color: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    width: 200px;
    z-index: 1000;
}

.user-icon {
    text-align: center;
    font-size: 40px;
    color: #2196F3;
}

.user-details {
    text-align: center;
    margin-top: 10px;
}

.user-details p {
    margin: 5px 0;
    color: #333;
    font-size: 14px;
}

.save-and-exit-button {
    background-color: #2196F3;
    color: white;
    padding: 8px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
    width: 100%;
}

.save-and-exit-button:hover {
    background-color: #1976d2;
}

/* Rating dots UI */
.rating-dots .dots-wrap{display:flex;gap:14px;align-items:center;margin:10px 0}
.rating-dots .dot{width:28px;height:28px;border-radius:50%;background:#ddd;border:2px solid #bdbdbd;cursor:pointer;position:relative}
.rating-dots .dot.selected{background:#1976d2;border-color:#1565c0}
.rating-dots .dot.prev::after{display:none}
.show-prev .rating-dots .dot.prev::after{display:none}
.rating-dots .dot.star{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;line-height:1;border:2px solid #bdbdbd;background:#ddd;color:#9e9e9e}
.rating-dots .dot.star.selected{background:#ffd54f;border-color:#f9a825;color:#7a5900}
.rating-dots .dot.star.prev{outline:2px dashed #f57f17}
.rating-dots .dots-legend{display:flex;gap:28px;flex-wrap:wrap;color:#666;font-size:13px;margin-bottom:8px;margin-top:0}
.rating-dots .dots-legend .legend-item.active{color:#1976d2;font-weight:600}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}

.definition-bubble{background:#e7f3ff;border-left:4px solid #1e88e5;border-radius:6px;padding:12px;margin-top:10px}
.definition-bubble .def-content{font-size:14px;color:#0d47a1}

/* Previous-year definition bubble */
.prev-definition-bubble{background:#fff8e1;border-left:4px solid #ffb300;border-radius:6px;padding:12px;margin-top:10px}
.prev-definition-bubble .prev-def-content{font-size:14px;color:#5d4037}

/* Show star on last-year selected dot (instead of star button) */
.rating-dots .dot.prev.prev-star::before{content:'\2605'; /* ★ */ position:absolute; right:-8px; top:-10px; font-size:14px; color:#f9a825; display:none}
.show-prev .rating-dots .dot.prev.prev-star::before{display:block}

/* Mobile-specific styles */
@media only screen and (max-width: 768px) {
    .user-card {
        position: fixed;
        bottom: 10px;
        right: 50px;
        background-color: #fff;
        padding: 10px;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 50px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .user-card.expanded {
        width: 200px;
        height: 140px;
        border-radius: 8px;
        padding: 15px;
        display: block;
    }

    .user-card .user-icon {
        font-size: 24px;
    }

    .user-card .user-details {
        display: none;
    }

    .user-card.expanded .user-details {
        display: block;
        text-align: center;
        margin-top: 10px;
    }

    .user-card.expanded .save-and-exit-button {
        display: block;
    }

    .user-card .save-and-exit-button {
        display: none;
    }
}




</style>

<script>
    /*
    let completedCount = 0;
    let totalQuestions = {{ $competencies->count() }};
    let osobisteCompleted = 0;
    let spoleczneCompleted = 0;
    let liderskieCompleted = 0;
    let zawodoweCompleted = 0;
*/
    /*function updateProgress() {
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
    }*/

    function toggleAboveExpectations(checkbox) {
    const question = checkbox.closest('.question');
    const slider = question.querySelector('.slider');
    const descriptionCheckbox = question.querySelector('.add-description-container input[type="checkbox"]');
    const commentContainer = question.querySelector('.textarea-description');
    const descriptionDiv = question.querySelector('.slider-description');

    if (checkbox.checked) {
        // Ustawienie suwaka na 1
        slider.value = 1;
        // Uniemożliwienie interakcji użytkownika
        slider.style.pointerEvents = 'none';
        slider.style.backgroundColor = '#e0e0e0'; // Opcjonalnie: zmień kolor, aby wskazać, że suwak jest nieaktywny

        // Wyświetlenie opisu "Powyżej oczekiwań"
        descriptionDiv.textContent = question.dataset.descriptionAboveExpectations;
        descriptionDiv.style.display = 'block';

        // Zaznacz i zablokuj checkbox "Dodaj opis/argumentację"
        descriptionCheckbox.checked = true;
        descriptionCheckbox.disabled = true;
        commentContainer.style.display = 'block';
        commentContainer.querySelector('textarea').required = true;
    } else {
        // Przywróć interakcję użytkownika
        slider.style.pointerEvents = 'auto';
        slider.style.backgroundColor = ''; // Przywróć domyślny kolor

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
    const touched = slider.dataset.touched === "true";

    if (value === 0) {
        if (touched) {
            descriptionDiv.textContent = "Nie dotyczy mnie ta kompetencja";
            descriptionDiv.style.display = 'block';
        } else {
            descriptionDiv.style.display = 'none';
            descriptionDiv.textContent = '';
        }
    } else if (value === 0.25) {
        descriptionDiv.textContent = question.dataset.description025;
        descriptionDiv.style.display = 'block';
    } else if (value === 0.5) {
        descriptionDiv.textContent = question.dataset.description0to05;
        descriptionDiv.style.display = 'block';
    } else if (value === 0.75 || value === 1) {
        descriptionDiv.textContent = question.dataset.description075to1;
        descriptionDiv.style.display = 'block';
    } else {
        descriptionDiv.style.display = 'none';
        descriptionDiv.textContent = '';
    }
}


// Funkcja, która pokazuje pole tekstowe przy zaznaczeniu checkboxa "Dodaj opis/argumentację"
    function toggleDescriptionInput(checkbox) {
        const question = checkbox.closest('.question');
        const commentContainer = question.querySelector('.textarea-description');

        if (checkbox.checked) {
            commentContainer.style.display = 'block';
            commentContainer.querySelector('textarea').required = true;
        } else {
            commentContainer.style.display = 'none';
            commentContainer.querySelector('textarea').required = false;
        }
    }

    function copyLink() {
            var copyText = document.getElementById("editLinkModal");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            document.execCommand("copy");

            alert("Link został skopiowany do schowka.");
        }

    document.addEventListener("DOMContentLoaded", function() {
            // Inicjalizacja suwaków i opisów
            document.querySelectorAll('.slider').forEach(slider => {
                // Nie wywołuj updateSliderValue na starcie!
            });

            document.querySelectorAll('input[name^="above_expectations"]').forEach(checkbox => {
                if (checkbox.checked) {
                    toggleAboveExpectations(checkbox);
                }
            });

            // Inicjalizacja checkboxów "Dodaj opis/argumentację"
            document.querySelectorAll('input[name^="add_description"]').forEach(checkbox => {
                toggleDescriptionInput(checkbox);
            });

            var skipButton = document.getElementById("skipButton");
            if (skipButton) {
                var skipModal = document.getElementById("skipModal");
                var confirmSkip = document.getElementById("confirmSkip");
                var cancelSkip = document.getElementById("cancelSkip");

                // Wyświetlanie okna modalnego po kliknięciu "Pomiń resztę samooceny"
                skipButton.addEventListener("click", function() {
                    skipModal.style.display = "flex";
                });

                // Potwierdzenie pominięcia - przekierowanie do zakończenia samooceny
                var uuid = "{{ $uuid }}";

                confirmSkip.addEventListener("click", function() {
                    window.location.href = "/self-assessment/complete/" + uuid;
                });

                // Anulowanie pominięcia - ukrywanie modala
                cancelSkip.addEventListener("click", function() {
                    skipModal.style.display = "none";
                });
            }

            // Modal close button
            var closeModalButton = document.getElementById("closeModal");
            if (closeModalButton) {
                closeModalButton.addEventListener("click", function() {
                    document.getElementById("saveModal").style.display = "none";
                });
            }
        });
        document.addEventListener("DOMContentLoaded", function () {
            const userCard = document.querySelector(".user-card");
            const userIcon = userCard.querySelector(".user-icon");

            // Toggle expanded state on click
            userIcon.addEventListener("click", function () {
                userCard.classList.toggle("expanded");
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.slider').forEach(slider => {
        if (parseFloat(slider.value) !== 0) {
            updateSliderValue(slider);
        }
    });

    document.querySelectorAll('input[name^="above_expectations"]').forEach(checkbox => {
        if (checkbox.checked) {
            toggleAboveExpectations(checkbox);
        }
    });

    // Inicjalizacja checkboxów "Dodaj opis/argumentację"
    document.querySelectorAll('input[name^="add_description"]').forEach(checkbox => {
        toggleDescriptionInput(checkbox);
    });

    var skipButton = document.getElementById("skipButton");
    if (skipButton) {
        var skipModal = document.getElementById("skipModal");
        var confirmSkip = document.getElementById("confirmSkip");
        var cancelSkip = document.getElementById("cancelSkip");

        // Wyświetlanie okna modalnego po kliknięciu "Pomiń resztę samooceny"
        skipButton.addEventListener("click", function() {
            skipModal.style.display = "flex";
        });

        // Potwierdzenie pominięcia - przekierowanie do zakończenia samooceny
        var uuid = "{{ $uuid }}";

        confirmSkip.addEventListener("click", function() {
            window.location.href = "/self-assessment/complete/" + uuid;
        });

        // Anulowanie pominięcia - ukrywanie modala
        cancelSkip.addEventListener("click", function() {
            skipModal.style.display = "none";
        });
    }

    // Modal close button
    var closeModalButton = document.getElementById("closeModal");
    if (closeModalButton) {
        closeModalButton.addEventListener("click", function() {
            document.getElementById("saveModal").style.display = "none";
        });
    }
});


function markSliderTouched(slider) {
    slider.dataset.touched = "true";
}

let autosaveInterval = setInterval(function() {
    let form = document.getElementById('assessmentForm');
    if (!form) return;

    let formData = new FormData(form);

    fetch('{{ route('self_assessment.autosave') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Możesz dodać powiadomienie o autozapisie, np. console.log('Autozapis OK');
    })
    .catch(error => {
        // Możesz dodać obsługę błędów, np. console.error('Autozapis error', error);
    });
}, 60000); // co 60 sekund
</script>

</head>
<body>
    <div class="container-wrapper">
        <div class="user-card">
            <div class="user-icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-details">
                <p>{{ $employee->name }}</p>
                <p>{{ $employee->department }}</p>
            </div>
        <button type="submit" form="assessmentForm" name="save_and_exit" class="save-and-exit-button">Zapisz i dokończ później</button>
        </div>


        <div class="container">
            <!-- Wyświetlenie nagłówka poziomu -->
            <div class="level-header">
                Poziom: {{ $currentLevelName }}
            </div>

            <!-- Toggle poprzedniego cyklu -->
            @if(!empty($prevAnswers))
            <div style="text-align:right;margin-bottom:10px;">
                <label style="font-size:14px;color:#333;">
                    <input type="checkbox" id="togglePrev" onchange="document.body.classList.toggle('show-prev', this.checked)"> Pokaż wyniki z poprzedniego cyklu
                </label>
                <style>
                    .prev-badge{display:none;color:#555;font-size:12px;margin-left:8px}
                    .show-prev .prev-badge{display:inline-block}
                    .prev-value{display:none;color:#777;font-size:12px;margin-top:6px}
                    .show-prev .prev-value{display:block}
                </style>
            </div>
            @endif
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

            @if(session('show_modal'))
            <!-- Modal -->
            <div id="saveModal" class="modal" style="display:flex;">
                <div class="modal-content">
                    <p>Twoja dotychczasowa samoocena została zapisana. Aby wrócić później do edycji tego formularza, użyj tego linku:</p>
                    <div class="edit-link-wrapper">
                        <input type="text" id="editLinkModal" value="{{ route('form.edit', ['uuid' => $uuid]) }}" readonly>
                        <button onclick="copyLink()" class="button copy-button">
                            <i class="fas fa-copy"></i> Kopiuj link
                        </button>
                    </div>
                    <button id="closeModal" class="cancel-button">Zamknij</button>
                </div>
            </div>
            @endif



            <!-- Główny formularz -->
            <form action="{{ route('save_results') }}" method="POST" id="assessmentForm">
                @csrf

                <!-- Ukryte pola z danymi użytkownika -->
                <input type="hidden" name="name" value="{{ session('name') }}">
                <input type="hidden" name="email" value="{{ session('email') }}">
                <input type="hidden" name="department" value="{{ session('department') }}">
                <input type="hidden" name="current_level" value="{{ $currentLevel }}">
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <input type="hidden" name="uuid" value="{{ $uuid }}">



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
                @php
                    // Zbuduj dymek z opisem z zeszłego roku zgodnie z mapowaniem wartości -> tekst opisu
                    $prevValGlobal = $prevAnswers['score'][$competency->id] ?? null;
                    $prevTextGlobal = null;
                    if ($prevValGlobal !== null) {
                        if ((float)$prevValGlobal == 0) { $prevTextGlobal = 'Nie dotyczy / brak oceny początkowo'; }
                        elseif ((float)$prevValGlobal == 0.25) { $prevTextGlobal = $competency->description_025; }
                        elseif ((float)$prevValGlobal == 0.5) { $prevTextGlobal = $competency->description_0_to_05; }
                        elseif ((float)$prevValGlobal >= 0.75) { $prevTextGlobal = $competency->description_075_to_1; }
                    }
                    if (!empty($prevAnswers['above_expectations'][$competency->id])) {
                        $prevTextGlobal = $competency->description_above_expectations ?: $prevTextGlobal;
                    }
                @endphp
                <div class="question"
                     data-description0to05="{{ $competency->description_0_to_05 }}"
                     data-description025="{{ $competency->description_025 }}"
                     data-description075to1="{{ $competency->description_075_to_1 }}"
                     data-description-above-expectations="{{ $competency->description_above_expectations }}"
                     data-competency-type="{{ $competency->competency_type }}">

                        <div class="question-header">
                            <div class="badge-container">
                                <span class="badge competency {{ getCompetencyClass($competency->competency_type) }}">{{ $competency->competency_type }}</span>
                                <span class="badge level">Poziom {{ $currentLevel }}. {{ $currentLevelName }}</span>
                            </div>
                        </div>
                        <div style="font-weight:600;margin:6px 0 6px;">Jak oceniasz swoją kompetencję/cechę:</div>
                        <label style="display:block;font-size:18px;font-weight:700;margin-bottom:8px;">{{ $competency->competency_name }}:
                            @if(!empty($prevAnswers['score'][$competency->id]))
                                <span class="prev-badge"><i class="fa fa-history"></i> Poprzednio: {{ $prevAnswers['score'][$competency->id] }}@if(!empty($prevAnswers['above_expectations'][$competency->id])) ⭐@endif</span>
                            @endif
                        </label>
                        <div class="rating-dots" role="radiogroup" aria-label="Ocena">
                            <input type="hidden" name="competency_id[]" value="{{ $competency->id }}">
                            @php
                                $current = $savedAnswers['score'][$competency->id] ?? 0;
                                $prev = $prevAnswers['score'][$competency->id] ?? null;
                                $options = [
                                    ['v'=>0, 'label'=>'Nie dotyczy'],
                                    ['v'=>0.25, 'label'=>'Słabo'],
                                    ['v'=>0.5, 'label'=>'Średnio'],
                                    ['v'=>0.75, 'label'=>'Dobrze'],
                                    ['v'=>1, 'label'=>'Bardzo dobrze'],
                                ];
                            @endphp
                            <div class="dots-legend">
                                @foreach($options as $opt)
                                    @php $isSelected = ((string)$current === (string)$opt['v']); @endphp
                                    <span class="legend-item {{ $isSelected ? 'active' : '' }}" data-value="{{ $opt['v'] }}">{{ $opt['label'] }}</span>
                                @endforeach
                                <span class="legend-item" data-star="1">Powyżej oczekiwań</span>
                            </div>
                            <div class="dots-wrap" style="margin-top:6px;">
                                @foreach($options as $opt)
                                    @php
                                        $isSelected = ((string)$current === (string)$opt['v']);
                                        $isPrev = ((string)$prev === (string)$opt['v']);
                                        $prevHadStar = !empty($prevAnswers['above_expectations'][$competency->id]);
                                        $prevStarOnThisDot = $isPrev && $prevHadStar; // gwiazdka na przycisku z oceną z zeszłego roku
                                    @endphp
                                    <button type="button" class="dot {{ $isSelected ? 'selected' : '' }} {{ $isPrev ? 'prev' : '' }} {{ $prevStarOnThisDot ? 'prev-star' : '' }}" data-value="{{ $opt['v'] }}" aria-pressed="{{ $isSelected ? 'true' : 'false' }}" title="{{ $opt['label'] }}">
                                        <span class="sr-only">{{ $opt['label'] }}</span>
                                    </button>
                                @endforeach
                                <!-- Above expectations star only reflects current selection visually (no prev-star here) -->
                                <button type="button" class="dot star {{ !empty($savedAnswers['above_expectations'][$competency->id]) ? 'selected' : '' }}" data-star="1" title="Powyżej oczekiwań">{{ !empty($savedAnswers['above_expectations'][$competency->id]) ? '★' : '☆' }}</button>
                                <input type="hidden" name="score[{{ $competency->id }}]" value="{{ $current }}" class="score-input">
                                <input type="hidden" name="above_expectations[{{ $competency->id }}]" value="{{ !empty($savedAnswers['above_expectations'][$competency->id]) ? 1 : 0 }}" class="star-input">
                            </div>
                        </div>

                        <!-- Opis suwaka -->
                        <div class="slider-description" style="display:none; margin-top:15px;"></div>

                        <!-- Definicja wybranego poziomu (dymek) -->
                        <div class="definition-bubble" style="display:none;">
                            <div style="font-weight:600;color:#1976d2;margin-bottom:6px;">Definicja wybranego poziomu kompetencji:</div>
                            <div class="def-content"></div>
                        </div>

                        <!-- Checkbox "Dodaj uzasadnienie" -->
                        <div class="add-description-container">
                            <input type="checkbox" name="add_description[{{ $competency->id }}]" onchange="toggleDescriptionInput(this)" {{ isset($savedAnswers['add_description'][$competency->id]) ? 'checked' : '' }}>
                            <label>Dodaj uzasadnienie</label>
                        </div>

                        @if(!empty($prevTextGlobal))
                            <!-- Dymek z opisem z poprzedniego roku, poniżej oceny/definicji i "Dodaj uzasadnienie" -->
                            <div class="prev-definition-bubble prev-value">
                                <div style="font-weight:600;color:#a15a00;margin-bottom:6px;">Jak opisaliśmy to poprzednio:</div>
                                <div class="prev-def-content">{{ $prevTextGlobal }}</div>
                            </div>
                        @endif

                        @php $hasPrev = !empty($prevAnswers['comments'][$competency->id]) || !empty($prevAnswers['manager_feedback'][$competency->id]); @endphp

                        <!-- Pole tekstowe na opis -->
                        <div class="textarea-description" style="display:none;">
                            <textarea name="comments[{{ $competency->id }}]" placeholder="Wpisz opis/argumentację...">{{ $savedAnswers['comments'][$competency->id] ?? '' }}</textarea>
                        </div>
                        @if(!empty($prevTextGlobal))
                            <!-- Dymek z opisem z poprzedniego roku, poniżej pola tekstowego -->
                            <div class="prev-definition-bubble prev-value" style="margin-top:8px;">
                                <div style="font-weight:600;color:#a15a00;margin-bottom:6px;">Jak opisaliśmy to poprzednio:</div>
                                <div class="prev-def-content">{{ $prevTextGlobal }}</div>
                            </div>
                        @endif
                        <!-- Poprzedni komentarz (read-only box below textarea) -->
                        @if($hasPrev)
                        <div class="prev-value" style="margin-top:10px;">
                            @if(!empty($prevAnswers['comments'][$competency->id]))
                                <div style="font-weight:600;margin-bottom:6px;">Co napisałeś/aś poprzednim razem:</div>
                                <div class="prev-comment" style="white-space:pre-wrap;">{{ $prevAnswers['comments'][$competency->id] }}</div>
                            @endif
                            @if(!empty($prevAnswers['manager_feedback'][$competency->id]))
                                <div class="prev-manager" style="white-space:pre-wrap;margin-top:12px;">
                                    <a href="#" class="toggle-manager" style="color:#1976d2;text-decoration:underline;" onclick="this.nextElementSibling.style.display = (this.nextElementSibling.style.display==='none'||!this.nextElementSibling.style.display)?'block':'none';return false;">Jakiej odpowiedzi udzielił manager ></a>
                                    <div class="manager-feedback" style="display:none;margin-top:6px;">{{ $prevAnswers['manager_feedback'][$competency->id] }}</div>
                                </div>
                            @endif
                        </div>
                        @endif
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
    <script>
    function copyToClipboard() {
        var copyText = document.getElementById("editLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Dla urządzeń mobilnych

        document.execCommand("copy");

        alert("Link został skopiowany do schowka.");
    }
    </script>
<script>
// Handle rating dots interactions
document.querySelectorAll('.question').forEach(function(q){
    const wrap = q.querySelector('.rating-dots'); if(!wrap) return;
    const scoreInput = q.querySelector('.score-input');
    const starInput = q.querySelector('.star-input');
    const defBubble = q.querySelector('.definition-bubble');
    const defContent = q.querySelector('.definition-bubble .def-content');

    function updateDefinition(val){
        const d0 = q.getAttribute('data-description0to05') || '';
        const d025 = q.getAttribute('data-description025') || '';
        const d075 = q.getAttribute('data-description075to1') || '';
        const dAbove = q.getAttribute('data-description-above-expectations') || '';
        let text = '';
        if (parseFloat(val) >= 0.75) text = d075; else if (parseFloat(val) >= 0.25) text = d025; else text = d0;
        if (parseInt(starInput.value)) text = dAbove || text;
        if (text){ defBubble.style.display='block'; defContent.textContent = text; } else { defBubble.style.display='none'; }
    }

    wrap.querySelectorAll('.dot').forEach(function(dot){
        dot.addEventListener('click', function(){
            if (dot.classList.contains('star')){
                // toggle star
                const val = starInput.value === '1' ? '0' : '1';
                starInput.value = val;
                dot.classList.toggle('selected', val === '1');
                    dot.setAttribute('aria-pressed', val === '1' ? 'true' : 'false');
                    // change icon
                    dot.textContent = (val === '1') ? '★' : '☆';
                    if (val === '1') scoreInput.value = '1'; // star enforces 1.0
                    // Update legend active for 1.0
                    const legends = q.querySelectorAll('.dots-legend .legend-item');
                    legends.forEach(l=>l.classList.toggle('active', l.getAttribute('data-value')==='1'));
                updateDefinition(scoreInput.value);
                return;
            }
            // select score dot
            const value = this.getAttribute('data-value');
            scoreInput.value = value;
            wrap.querySelectorAll('.dot').forEach(d=>{ if(!d.classList.contains('star')) d.classList.remove('selected'); });
                this.classList.add('selected');
                // set aria
                wrap.querySelectorAll('.dot').forEach(d=>{ if(!d.classList.contains('star')) d.setAttribute('aria-pressed','false'); });
                this.setAttribute('aria-pressed','true');
            // If not star, ensure star remains as it was; definition updates accordingly
                const legends = q.querySelectorAll('.dots-legend .legend-item');
                legends.forEach(l=>l.classList.toggle('active', l.getAttribute('data-value')===value));
            updateDefinition(value);
        });
    });

    // Init bubble
    updateDefinition(scoreInput.value || '0');
        // Init star icon and aria
        const starBtn = wrap.querySelector('.dot.star');
        if (starBtn){
            const isOn = starInput.value === '1';
            starBtn.textContent = isOn ? '★' : '☆';
            starBtn.setAttribute('aria-pressed', isOn ? 'true' : 'false');
        }
});

// Toggle manager feedback disclosure
document.querySelectorAll('.prev-manager .toggle-manager').forEach(function(a){
    a.addEventListener('click', function(e){ e.preventDefault(); const box = this.nextElementSibling; box.style.display = (box.style.display==='none'||!box.style.display)?'block':'none'; });
});
</script>
</body>



</html>
