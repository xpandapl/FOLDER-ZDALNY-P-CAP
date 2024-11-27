<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Panel Managera</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 1200px;
            margin: auto;
            box-sizing: border-box;
        }
        /* Form Group Styles */
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }
        .form-group label {
            font-size: 16px;
        }
        .form-group select {
            padding: 5px;
            font-size: 14px;
            width: 200px;
        }
        /* Button Styles */
        .form-group button {
            padding: 5px 10px;
            font-size: 14px;
        }
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }
        th, td {
            padding: 6px;
            text-align: left;
            white-space: normal;
            word-wrap: break-word;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
        }
        tr {
            font-size: 14px;
        }
        .col-kompetencja {
            width: 15%;
        }

        .col-small {
            width: 7%;
        }

        .col-wide {
            width: 20%;
        }
        /* Select and Textarea Styles in Table */
        td select {
            width: 80px;
            font-size: 12px;
        }
        td textarea {
            width: 100%;
            height: 60px;
            font-size: 12px;
            resize: vertical;
        }

        .feedback {
            resize: vertical; /* Rozciąganie okna w dół */
            appearance: none; /* Ukrywa domyślny styl przeglądarki */
            -webkit-appearance: none; /* Dla przeglądarek opartych na WebKit */
            -moz-appearance: none; /* Dla Firefoksa */
            background-color: #fff;
            /* padding: 10px 40px 10px 10px; */
            font-size: 14px;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            cursor: text;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s, box-shadow 0.3s;
            }

        .feedback:hover {
            border-color: #888;
        }

        .feedback:focus {
            border-color: #4CAF50;
            box-shadow: 0 2px 6px rgba(0, 128, 0, 0.2);
        }
        /* Badge Styles */
        .badge-container {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 8px;
            color: white;
            font-size: 10px;
        }
        .badge.level {
            background-color: lightgrey;
            color: black;
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
        /* Other Styles */
        button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #45a049;
        }
        .logout-button {
            background-color: grey;
            padding: 5px 10px;
            font-size: 12px;
        }
        .logout-button:hover {
            background-color: darkgrey;
        }
        /* Tabs */
        .tabs {
            margin-bottom: 20px;
        }
        .button-tab {
            background-color: #f2f2f2;
            border-radius: 10px 10px 0 0;
            color: black;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin-right: 5px;
        }
        .button-tab:hover{
            background-color:lightgrey;
        }
        .button-tab:focus {
            background-color: white;
        }
        .button-tab.focus {
            background-color: white;
            box-shadow: 0 -4px 8px -4px rgba(0, 0, 0, 0.3); /* Cień skierowany w górę */
        }
        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            max-width: 600px;
            position: relative;
        }
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        /* Team Table */
        #team-tab table {
            margin-top: 10px;
        }
        #team-tab th, #team-tab td {
            padding: 8px;
            font-size: 12px;
        }
        .info-icon {
            display: inline-block;
            margin-left: 5px;
            background-color: #2196F3;
            color: white;
            border-radius: 50%;
            width: 15px;
            height: 15px;
            text-align: center;
            font-size: 12px;
            line-height: 15px;
            cursor: pointer;
        }
        .high-percentage {
            color: green;
            font-weight: bold; /* Opcjonalnie: pogrubienie tekstu dla lepszej widoczności */
        }
        .summary {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 20px;
        }

        .summary p {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .summary ul {
            list-style-type: none;
            padding-left: 0;
        }

        .summary ul li {
            margin-bottom: 5px;
        }
        .custom-select {
            appearance: none; /* Ukrywa domyślny styl przeglądarki */
            -webkit-appearance: none; /* Dla przeglądarek opartych na WebKit */
            -moz-appearance: none; /* Dla Firefoksa */
            background-color: #fff;
            padding: 10px 40px 10px 10px;
            font-size: 14px;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s, box-shadow 0.3s;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="gray"><path d="M7 10l5 5 5-5H7z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 15px;
        }

        .custom-select:hover {
            border-color: #888;
        }

        .custom-select:focus {
            border-color: #4CAF50;
            box-shadow: 0 2px 6px rgba(0, 128, 0, 0.2);
        }
        /* HR Tab Table Styles */
        #hr-tab table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        #hr-tab th, #hr-tab td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        #hr-tab th {
            background-color: #f2f2f2;
        }

        #hr-tab tr:hover {
            background-color: #f9f9f9;
        }

        /* Style sekcji szczegółów pracownika */
        .employee-details {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .employee-details-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .employee-detail-item {
            display: flex;
            align-items: center;
            width: 48%; /* Dwie kolumny obok siebie */
        }

        .employee-detail-item i {
            margin-right: 5px;
            color: #555; /* Opcjonalnie: zmień kolor ikony */
        }

        .employee-detail-item strong {
            margin-right: 5px;
        }

        /* Dostosowanie responsywności */
        @media (max-width: 600px) {
            .employee-details-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .employee-detail-item {
                width: 100%;
                margin-bottom: 5px;
            }
        }

        /* Kontener kart */
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        /* Pojedyncza karta */
        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: calc(14% - 10px); /* Trzy karty w wierszu */
            padding: 15px;
            box-sizing: border-box;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card i {
            font-size: 16px;
            /* color: #4CAF50; */ /* Usuwamy ten wiersz */
            margin-bottom: 10px;
        }

        /* Nowe klasy dla kolorów ikon */
        .icon-green {
            color: #4CAF50; /* Zielony kolor */
        }

        .icon-gray {
            color: #ccc; /* Szary kolor */
        }

        .card-content strong {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .card-content p {
            font-size: 12px;
            margin: 0;
        }

        /* Dostosowanie responsywności */
        @media (max-width: 768px) {
            .card {
                width: calc(50% - 10px); /* Dwie karty w wierszu */
            }
        }

        @media (max-width: 480px) {
            .card {
                width: 100%; /* Jedna karta w wierszu */
            }
        }
        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }

        /* Karty podsumowania poziomów */
        .level-summaries .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        .level-summaries .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: calc(16.4% - 10px); /* 6 kart w wierszu */
            padding: 15px;
            box-sizing: border-box;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .level-summaries .card i {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .level-summaries .card-content strong {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .level-summaries .card-content p {
            font-size: 12px;
            margin: 0;
        }

        /* Efekt hover dla kart */
        .level-summaries .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }

        /* Dostosowanie responsywności */
        /* Dla tabletów */
        @media (max-width: 1024px) {
            .level-summaries .card {
                width: calc(25% - 10px); /* Cztery karty w wierszu */
            }
        }

        /* Dla mniejszych ekranów */
        @media (max-width: 768px) {
            .level-summaries .card {
                width: calc(33.333% - 10px); /* Trzy karty w wierszu */
            }
        }

        @media (max-width: 480px) {
            .level-summaries .card {
                width: calc(50% - 10px); /* Dwie karty w wierszu */
            }
        }

        @media (max-width: 320px) {
            .level-summaries .card {
                width: 100%; /* Jedna karta w wierszu */
            }
        }

        .download-buttons {
            margin-top: 10px;
        }

        .small-button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 8px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin: 2px;
        }

        .small-button:hover {
            background-color: #45a049;
        }




    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Panel Managera</h2>

        <!-- Przycisk Wyloguj -->
        <div style="text-align: right; margin-bottom: 20px;">
            <!-- Formularz wylogowania -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <!-- Przycisk Wyloguj -->
            <button class="logout-button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Wyloguj
            </button>
        </div>

        <!-- Zakładki -->
        <div class="tabs">
        <button class="button-tab" data-tab="individual">
            <i class="fas fa-user"></i> Indywidualne oceny
        </button>
        <button class="button-tab" data-tab="team">
            <i class="fas fa-users"></i> Cały zespół
        </button>
        @if($manager->role == 'supermanager')
            <button class="button-tab" data-tab="hr_individual">
                <i class="fas fa-user-circle"></i> HR - Indywidualne
            </button>
            <button class="button-tab" data-tab="hr">
                <i class="fas fa-chart-bar"></i> HR
            </button>
        @endif
        @if($manager->role == 'head')
            <button class="button-tab" data-tab="department_individual">
                <i class="fas fa-user-circle"></i> Dział - Indywidualne
            </button>
            <button class="button-tab" data-tab="department">
                <i class="fas fa-chart-bar"></i> Dział
            </button>
        @endif
    </div>


<!-- Zakładka Indywidualna -->
<div id="individual-tab">
    <!-- Filtrowanie po użytkowniku -->
    <div class="form-group">
        <label for="employee-select">Wybierz pracownika:</label>
        <select id="employee-select" class="custom-select" onchange="filterByEmployee()">
            <option value="">-- Wybierz pracownika --</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ isset($employee) && $employee->id == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
        </select>

        @if(isset($employee))
        <a href="{{ route('manager.generate_pdf', ['employeeId' => $employee->id]) }}" target="_blank">
            <button type="button">
                PDF <i class="fas fa-download download-icon"></i>
            </button>
        </a>
        <a href="{{ route('manager.generate_xls', ['employeeId' => $employee->id]) }}" target="_blank">
            <button type="button">
                XLS <i class="fas fa-download download-icon"></i>
            </button>
        </a>
        @endif
    </div>

    @if(isset($employee))
        <div class="employee-details">
            <div class="employee-details-row">
                <div class="employee-detail-item">
                    <i class="fas fa-user"></i> <strong>Imię i nazwisko:</strong> {{ $employee->name }}
                </div>
                <div class="employee-detail-item">
                    <i class="fas fa-calendar-alt"></i> <strong>Data przesłania:</strong> {{ $employee->created_at }}
                </div>
            </div>
            <div class="employee-details-row">
                <div class="employee-detail-item">
                    <i class="fas fa-building"></i> <strong>Dział:</strong> {{ $employee->department }}
                </div>
                <div class="employee-detail-item">
                    <i class="fas fa-sync-alt"></i> <strong>Data aktualizacji:</strong> {{ $employee->updated_at }}
                </div>
            </div>
            <div class="employee-details-row">
                <div class="employee-detail-item">
                    <i class="fas fa-user-tie"></i> <strong>Przełożony:</strong> {{ $employee->manager_username }}
                </div>
                <div class="employee-detail-item">
                    <i class="fas fa-clipboard"></i> <strong>Stanowisko:</strong> {{ $employee->job_title }}
                </div>
            </div>
        </div>
    @endif




    @if(isset($employee))
        <!-- Formularz wyników -->
        <form action="{{ route('manager.panel.update') }}" method="POST">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <div>
                <table id="results-table">
                    <thead>
                        <tr>
                            <th class="col-kompetencja">Kompetencja</th>
                            <th class="col-small">Ocena użytkownika</th>
                            <th class="col-small">Czy powyżej oczekiwań (użytkownik)</th>
                            <th class="col-wide">Argumentacja użytkownika</th>
                            <th class="col-small">Wartość</th>
                            <th class="col-small">Ocena managera</th>
                            <th class="col-wide">Feedback od managera</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentLevel = null;
                            $getCompetencyClass = function($competencyType) {
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
                            };
                        @endphp
                        @foreach($results->sortBy('competency.level') as $result)
                            @if($currentLevel != $result->competency->level)
                                @if($currentLevel != null)
                                    <!-- Separator między poziomami -->
                                    <tr><td colspan="7"></td></tr>
                                @endif
                                @php
                                    $currentLevel = $result->competency->level;
                                @endphp
                                <!-- Nagłówek poziomu -->
                                <tr>
                                    <td colspan="7" style="background-color: #f2f2f2;"><strong>Poziom: {{ $currentLevel }}</strong></td>
                                </tr>
                            @endif
                            @php
                                // Pobierz wartość kompetencji dla zespołu pracownika
                                $competencyValue = $result->competency->getValueForTeam($employee->team->id);
                            @endphp
                            <tr>
                                <td>
                                    {{ $result->competency->competency_name }}
                                    <span class="info-icon" onclick="showDefinitionModal({{ $result->competency->id }})">i</span>
                                    <div class="badge-container">
                                        <span class="badge level">Poziom {{ $result->competency->level }}</span>
                                        <span class="badge competency {{ $getCompetencyClass($result->competency->competency_type) }}">
                                            {{ $result->competency->competency_type }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $result->score > 0 ? $result->score : 'N/D' }}</td>
                                <td>{{ $result->above_expectations ? 'Tak' : 'Nie' }}</td>
                                <td>{{ $result->comments }}</td>
                                <td>{{ $competencyValue }}</td>

                                <!-- Pozostałe kolumny -->
                                <td>
                                    <!-- Ocena managera -->
                                    <select class="custom-select" name="score_manager[{{ $result->id }}]">
                                        <option value="" {{ is_null($result->score_manager) ? 'selected' : '' }}>Ok</option>
                                        <option value="0" {{ $result->score_manager === 0.0 && !$result->above_expectations_manager ? 'selected' : '' }}>N/D</option>
                                        <option value="0.25" {{ $result->score_manager === 0.25 ? 'selected' : '' }}>0.25</option>
                                        <option value="0.5" {{ $result->score_manager === 0.5 ? 'selected' : '' }}>0.5</option>
                                        <option value="0.75" {{ $result->score_manager === 0.75 ? 'selected' : '' }}>0.75</option>
                                        <option value="1" {{ $result->score_manager === 1.0 && !$result->above_expectations_manager ? 'selected' : '' }}>1</option>
                                        <option value="above_expectations" {{ $result->above_expectations_manager ? 'selected' : '' }}>Powyżej oczekiwań</option>
                                    </select>
                                </td>

                                <td>
                                    <textarea class="feedback" name="feedback_manager[{{ $result->id }}]">{{ $result->feedback_manager }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(isset($levelSummaries))
                <div class="level-summaries">
                    <div class="cards-container">
                        @foreach($levelSummaries as $level => $summary)
                            <div class="card">
                                @php
                                // Wybór ikony i koloru w zależności od poziomu
                                        $iconClass = 'fas fa-star';
                                        $iconColor = '#4CAF50'; // Domyślny kolor

                                        if(strpos($level, '1') !== false) {
                                            $iconClass = 'fas fa-user-graduate';
                                            $iconColor = '#2196F3'; // Niebieski
                                        } elseif(strpos($level, '2') !== false) {
                                            $iconClass = 'fas fa-user';
                                            $iconColor = '#4CAF50'; // Zielony
                                        } elseif(strpos($level, '3') !== false) {
                                            $iconClass = 'fas fa-user-tie';
                                            $iconColor = '#FF9800'; // Pomarańczowy
                                        } elseif(strpos($level, '4') !== false) {
                                            $iconClass = 'fas fa-chalkboard-teacher';
                                            $iconColor = '#9C27B0'; // Fioletowy
                                        } elseif(strpos($level, '5') !== false) {
                                            $iconClass = 'fas fa-user-cog';
                                            $iconColor = '#F44336'; // Czerwony
                                        } elseif(strpos($level, '6') !== false) {
                                            $iconClass = 'fas fa-user-shield';
                                            $iconColor = '#795548'; // Brązowy
                                        }
                                @endphp
                                <i class="{{ $iconClass }}" style="color: {{ $iconColor }};"></i>
                                <div class="card-content">
                                    <strong>Poziom {{ $level }}</strong>
                                    <p>
                                        {{ $summary['earnedPointsManager'] }} / {{ $summary['maxPoints'] }} pkt.
                                    </p>
                                    @if(is_numeric($summary['percentageEmployee']))
                                        <p>
                                            Samoocena:
                                            <span class="{{ $summary['percentageEmployee'] >= ($level == 1 ? 80 : 85) ? 'high-percentage' : '' }}">
                                                {{ number_format($summary['percentageEmployee'], 2) }}%
                                            </span>
                                        </p>
                                    @else
                                        <p>Samoocena: {{ $summary['percentageEmployee'] }}</p>
                                    @endif
                                    @if(is_numeric($summary['percentageManager']))
                                        <p>
                                            Feedback:
                                            <span class="{{ $summary['percentageManager'] >= ($level == 1 ? 80 : 85) ? 'high-percentage' : '' }}">
                                                {{ number_format($summary['percentageManager'], 2) }}%
                                            </span>
                                        </p>
                                    @else
                                        <p>Feedback: {{ $summary['percentageManager'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>


                @endif


            </div>

            <!-- Zapisz przyciski -->
            <div style="margin-top: 20px;">
                <button type="submit">Zapisz zmiany</button>
            </div>
        </form>
    @else
        <p>Wybierz pracownika, aby zobaczyć wyniki.</p>
    @endif
</div> <!-- Koniec zakładki Indywidualne -->

@if($manager->role == 'supermanager')
<!-- Zakładka HR - Indywidualne -->
<div id="hr_individual-tab" style="display:none;">
    <!-- Filtrowanie po użytkowniku -->
    <div class="form-group">
        <label for="hr-employee-select">Wybierz pracownika:</label>
        <select id="hr-employee-select" class="custom-select" onchange="filterByHREmployee()">
            <option value="">-- Wybierz pracownika --</option>
            @foreach($allEmployees as $emp)
                <option value="{{ $emp->id }}" {{ isset($employee) && $employee->id == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
        </select>

        @if(isset($employee))
        <a href="{{ route('manager.generate_pdf', ['employeeId' => $employee->id]) }}" target="_blank">
            <button type="button">
                PDF <i class="fas fa-download download-icon"></i>
            </button>
        </a>
        <a href="{{ route('manager.generate_xls', ['employeeId' => $employee->id]) }}" target="_blank">
            <button type="button">
                XLS <i class="fas fa-download download-icon"></i>
            </button>
        </a>
        @endif
    </div>

    @if(isset($employee))
        <div class="employee-details">
            <div class="employee-details-row">
                <div class="employee-detail-item">
                    <i class="fas fa-user"></i> <strong>Imię i nazwisko:</strong> {{ $employee->name }}
                </div>
                <div class="employee-detail-item">
                    <i class="fas fa-calendar-alt"></i> <strong>Data przesłania:</strong> {{ $employee->created_at }}
                </div>
            </div>
            <div class="employee-details-row">
                <div class="employee-detail-item">
                    <i class="fas fa-building"></i> <strong>Dział:</strong> {{ $employee->department }}
                </div>
                <div class="employee-detail-item">
                    <i class="fas fa-sync-alt"></i> <strong>Data aktualizacji:</strong> {{ $employee->updated_at }}
                </div>
            </div>
            <div class="employee-details-row">
                <div class="employee-detail-item">
                    <i class="fas fa-user-tie"></i> <strong>Przełożony:</strong> {{ $employee->manager_username }}
                </div>
                <div class="employee-detail-item">
                    <i class="fas fa-clipboard"></i> <strong>Stanowisko:</strong> {{ $employee->job_title }}
                </div>
            </div>
        </div>
    @endif


    @if(isset($employee))
        <!-- Formularz wyników -->
        <form action="{{ route('manager.panel.update') }}" method="POST">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <div>
                <table id="results-table">
                    <thead>
                        <tr>
                            <th class="col-kompetencja">Kompetencja</th>
                            <th class="col-small">Ocena użytkownika</th>
                            <th class="col-small">Czy powyżej oczekiwań (użytkownik)</th>
                            <th class="col-wide">Argumentacja użytkownika</th>
                            <th class="col-small">Wartość</th>
                            <th class="col-small">Ocena managera</th>
                            <th class="col-wide">Feedback od managera</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentLevel = null;
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
                        @foreach($results->sortBy('competency.level') as $result)
                            @if($currentLevel != $result->competency->level)
                                @if($currentLevel != null)
                                    <!-- Separator między poziomami -->
                                    <tr><td colspan="7"></td></tr>
                                @endif
                                @php
                                    $currentLevel = $result->competency->level;
                                @endphp
                                <!-- Nagłówek poziomu -->
                                <tr>
                                    <td colspan="7" style="background-color: #f2f2f2;"><strong>Poziom: {{ $currentLevel }}</strong></td>
                                </tr>
                            @endif
                            @php
                                // Pobierz wartość kompetencji dla zespołu pracownika
                                $competencyValue = $result->competency->getValueForTeam($employee->team->id);
                            @endphp
                            <tr>
                                <td>
                                    {{ $result->competency->competency_name }}
                                    <span class="info-icon" onclick="showDefinitionModal({{ $result->competency->id }})">i</span>
                                    <div class="badge-container">
                                        <span class="badge level">Poziom {{ $result->competency->level }}</span>
                                        <span class="badge competency {{ getCompetencyClass($result->competency->competency_type) }}">{{ $result->competency->competency_type }}</span>
                                    </div>
                                </td>
                                <td>{{ $result->score > 0 ? $result->score : 'N/D' }}</td>
                                <td>{{ $result->above_expectations ? 'Tak' : 'Nie' }}</td>
                                <td>{{ $result->comments }}</td>
                                <td>{{ $competencyValue }}</td>

                                <!-- Pozostałe kolumny -->
                                <td>
                                    <!-- Ocena managera -->
                                    <select class="custom-select" name="score_manager[{{ $result->id }}]">
                                        <option value="" {{ is_null($result->score_manager) ? 'selected' : '' }}>Ok</option>
                                        <option value="0" {{ $result->score_manager === 0.0 && !$result->above_expectations_manager ? 'selected' : '' }}>N/D</option>
                                        <option value="0.25" {{ $result->score_manager === 0.25 ? 'selected' : '' }}>0.25</option>
                                        <option value="0.5" {{ $result->score_manager === 0.5 ? 'selected' : '' }}>0.5</option>
                                        <option value="0.75" {{ $result->score_manager === 0.75 ? 'selected' : '' }}>0.75</option>
                                        <option value="1" {{ $result->score_manager === 1.0 && !$result->above_expectations_manager ? 'selected' : '' }}>1</option>
                                        <option value="above_expectations" {{ $result->above_expectations_manager ? 'selected' : '' }}>Powyżej oczekiwań</option>
                                    </select>
                                </td>

                                <td>
                                    <textarea class="feedback" name="feedback_manager[{{ $result->id }}]">{{ $result->feedback_manager }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(isset($levelSummaries))
                    <div class="level-summaries">
                        <div class="cards-container">
                            @foreach($levelSummaries as $level => $summary)
                                <div class="card">
                                    @php
                                        // Wybór ikony i koloru w zależności od poziomu
                                        $iconClass = 'fas fa-star';
                                        $iconColor = '#4CAF50'; // Domyślny kolor

                                        if(strpos($level, '1') !== false) {
                                            $iconClass = 'fas fa-user-graduate';
                                            $iconColor = '#2196F3'; // Niebieski
                                        } elseif(strpos($level, '2') !== false) {
                                            $iconClass = 'fas fa-user';
                                            $iconColor = '#4CAF50'; // Zielony
                                        } elseif(strpos($level, '3') !== false) {
                                            $iconClass = 'fas fa-user-tie';
                                            $iconColor = '#FF9800'; // Pomarańczowy
                                        } elseif(strpos($level, '4') !== false) {
                                            $iconClass = 'fas fa-chalkboard-teacher';
                                            $iconColor = '#9C27B0'; // Fioletowy
                                        } elseif(strpos($level, '5') !== false) {
                                            $iconClass = 'fas fa-user-cog';
                                            $iconColor = '#F44336'; // Czerwony
                                        } elseif(strpos($level, '6') !== false) {
                                            $iconClass = 'fas fa-user-shield';
                                            $iconColor = '#795548'; // Brązowy
                                        }
                                    @endphp
                                    <i class="{{ $iconClass }}" style="color: {{ $iconColor }};"></i>
                                    <div class="card-content">
                                        <strong>Poziom {{ $level }}</strong>
                                        <p>
                                            {{ $summary['earnedPointsManager'] }} / {{ $summary['maxPoints'] }} pkt.
                                        </p>
                                        @if(is_numeric($summary['percentageEmployee']))
                                            <p>
                                                Samoocena:
                                                <span class="{{ $summary['percentageEmployee'] >= ($level == 1 ? 80 : 85) ? 'high-percentage' : '' }}">
                                                    {{ number_format($summary['percentageEmployee'], 2) }}%
                                                </span>
                                            </p>
                                        @else
                                            <p>Samoocena: {{ $summary['percentageEmployee'] }}</p>
                                        @endif
                                        @if(is_numeric($summary['percentageManager']))
                                            <p>
                                                Feedback:
                                                <span class="{{ $summary['percentageManager'] >= ($level == 1 ? 80 : 85) ? 'high-percentage' : '' }}">
                                                    {{ number_format($summary['percentageManager'], 2) }}%
                                                </span>
                                            </p>
                                        @else
                                            <p>Feedback: {{ $summary['percentageManager'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Zapisz przyciski -->
            <div style="margin-top: 20px;">
                <button type="submit">Zapisz zmiany</button>
            </div>
        </form>
    @else
        <p>Wybierz pracownika, aby zobaczyć wyniki.</p>
    @endif
</div> <!-- Koniec zakładki HR - Indywidualne -->
@endif



        <!-- Zakładka Cały Zespół -->
        <div id="team-tab" style="display:none;">
            <!-- Tabela z podsumowaniem zespołu -->
            <table>
                <thead>
                    <tr>
                        <th>Imię i nazwisko</th>
                        <th>Nazwa stanowiska</th>
                        @foreach($levelNames as $levelKey => $levelName)
                            <th>{{ $levelName }}</th>
                        @endforeach
                        <th>Poziom</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teamEmployeesData as $emp)
                        <tr>
                            <td>{{ $emp['name'] }}</td>
                            <td>{{ $emp['job_title'] }}</td>
                            @foreach($levelNames as $levelKey => $levelName)
                                <!-- Manager's Percentage -->
                                <td>
                                    @php
                                        $percentageManager = $emp['levelPercentagesManager'][$levelName] ?? null;
                                        $threshold = $levelKey == 1 ? 80 : 85;
                                        $isHighManager = is_numeric($percentageManager) && $percentageManager >= $threshold;
                                    @endphp
                                    <span class="{{ $isHighManager ? 'high-percentage' : '' }}">
                                        {{ is_numeric($percentageManager) ? number_format((float)$percentageManager, 2) . '%' : 'N/D' }}
                                    </span>
                                </td>
                            @endforeach
                            <!-- Highest Level based on Manager's Assessment -->
                            <td>{{ $emp['highestLevelManager'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


            @php
                // Initialize variables for counting
                $levelCounts = array_fill_keys($levelNames, 0);

                foreach($teamEmployeesData as $emp) {
                    // Calculate the highest level based on manager's assessment
                    $highestLevel = $emp['highestLevelManager'];

                    // Count employees at each level
                    if(isset($highestLevel)) {
                        $levelCounts[$highestLevel] += 1;
                    }
                }
            @endphp
            @php
                $filledSurveyCount = 0;
                foreach($teamEmployeesData as $emp) {
                    if (!empty($emp['levelPercentagesManager'])) {
                        $filledSurveyCount += 1;
                    }
                }
            @endphp




            <div class="summary" style="margin-top: 20px;">
            <div class="cards-container">
                @foreach($levelNames as $levelName)
                    @php
                        // Ustal ikonę na podstawie nazwy poziomu
                        if($levelName == 'Junior') {
                            $iconClass = 'fas fa-user-graduate';
                        } elseif($levelName == 'Specjalista') {
                            $iconClass = 'fas fa-user';
                        } elseif($levelName == 'Senior') {
                            $iconClass = 'fas fa-user-tie';
                        } elseif($levelName == 'Supervisor') {
                            $iconClass = 'fas fa-chalkboard-teacher';
                        } elseif($levelName == 'Manager') {
                            $iconClass = 'fas fa-user-cog';
                        } elseif($levelName == 'Head of') {
                            $iconClass = 'fas fa-user-shield';
                        } else {
                            $iconClass = 'fas fa-briefcase';
                        }

                        // Use counts based on manager's assessments
                        $count = $levelCounts[$levelName] ?? 0;

                        // Determine icon color based on count
                        $iconColorClass = $count == 0 ? 'icon-gray' : 'icon-green';
                    @endphp
                    <div class="card">
                        <i class="{{ $iconClass }} {{ $iconColorClass }}"></i>
                        <div class="card-content">
                            <strong>{{ $levelName }}</strong>
                            <p>{{ $count }} {{ pluralForm($count, ['osoba', 'osoby', 'osób']) }}</p>
                        </div>
                    </div>
                @endforeach

                @if(isset($filledSurveyCount))
                    <div class="card">
                        <i class="fas fa-check icon-green"></i>
                        <div class="card-content">
                            <strong>Przesłało</strong>
                            <p>{{ $filledSurveyCount }} {{ pluralForm($filledSurveyCount, ['osoba', 'osoby', 'osób']) }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>


        </div>

        @if($manager->role == 'supermanager')
        <!-- Zakładka HR -->
        <div id="hr-tab" style="display:none;">
            <h3>Podsumowanie wypełnionych samoocen w zespołach</h3>
            <table>
                <thead>
                    <tr>
                        <th>Zespół</th>
                        <th>Liczba osób, które wypełniły samoocenę</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hrData as $data)
                    <tr>
                        <td>{{ $data['team_name'] }}</td>
                        <td>{{ $data['completed_count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Tabela z podsumowaniem całej organizacji -->
            <h3>Podsumowanie kompetencji w organizacji</h3>
            <table>
            <thead>
                <tr>
                    <th>Imię i nazwisko</th>
                    <th>Nazwa stanowiska</th>
                    @foreach($levelNames as $levelKey => $levelName)
                        <th>{{ $levelName }}</th>
                    @endforeach
                    <th>Poziom</th>
                </tr>
            </thead>
            <tbody>
                @foreach($organizationEmployeesData as $emp)
                    <tr>
                        <td>{{ $emp['name'] }}</td>
                        <td>{{ $emp['job_title'] }}</td>
                        @foreach($levelNames as $levelKey => $levelName)
                            <!-- Manager's Percentage -->
                            <td>
                                @php
                                    $percentageManager = $emp['levelPercentagesManager'][$levelName] ?? null;
                                    $threshold = $levelKey == 1 ? 80 : 85;
                                    $isHighManager = is_numeric($percentageManager) && $percentageManager >= $threshold;
                                @endphp
                                <span class="{{ $isHighManager ? 'high-percentage' : '' }}">
                                    {{ is_numeric($percentageManager) ? number_format((float)$percentageManager, 2) . '%' : 'N/D' }}
                                </span>
                            </td>
                        @endforeach
                        <!-- Highest Level based on Manager's Assessment -->
                        <td>{{ $emp['highestLevelManager'] }}</td>
                    </tr>
                @endforeach
            </tbody>


            </table>

            @php
                // Initialize variables for counting
                $levelCounts = array_fill_keys($levelNames, 0);
                $filledSurveyCount = 0;

                foreach($organizationEmployeesData as $emp) {
                    // Calculate the highest level based on manager's assessment
                    $highestLevel = $emp['highestLevelManager'];

                    // Count employees at each level
                    if(isset($highestLevel)) {
                        $levelCounts[$highestLevel] += 1;
                    }

                    // Check if the employee has filled out the survey
                    if (!empty($emp['levelPercentagesManager'])) {
                        $filledSurveyCount += 1;
                    }
                }
            @endphp


            <div class="summary" style="margin-top: 20px;">
            <div class="cards-container">
                @foreach($levelNames as $levelName)
                    @php
                        // Ustal ikonę na podstawie nazwy poziomu
                        if($levelName == 'Junior') {
                            $iconClass = 'fas fa-user-graduate';
                        } elseif($levelName == 'Specjalista') {
                            $iconClass = 'fas fa-user';
                        } elseif($levelName == 'Senior') {
                            $iconClass = 'fas fa-user-tie';
                        } elseif($levelName == 'Supervisor') {
                            $iconClass = 'fas fa-chalkboard-teacher';
                        } elseif($levelName == 'Manager') {
                            $iconClass = 'fas fa-user-cog';
                        } elseif($levelName == 'Head of') {
                            $iconClass = 'fas fa-user-shield';
                        } else {
                            $iconClass = 'fas fa-briefcase';
                        }

                        // Ustal kolor ikony na podstawie wartości liczby
                        $iconColorClass = $levelCounts[$levelName] == 0 ? 'icon-gray' : 'icon-green';
                    @endphp
                    <div class="card">
                        <i class="{{ $iconClass }} {{ $iconColorClass }}"></i>
                        <div class="card-content">
                            <strong>{{ $levelName }}</strong>
                            <p>{{ $levelCounts[$levelName] }} {{ pluralForm($levelCounts[$levelName], ['osoba', 'osoby', 'osób']) }}</p>
                        </div>
                    </div>
                @endforeach

                    <div class="card">
                        <i class="fas fa-check icon-green"></i>
                        <div class="card-content">
                            <strong>Przesłało</strong>
                            <p>{{ $filledSurveyCount }} {{ pluralForm($filledSurveyCount, ['osoba', 'osoby', 'osób']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Pobierz raport dla zespołu:</h3>
            <div class="summary" style="margin-top: 20px;">
            <div class="cards-container">
                @foreach($teams as $team)
                    <div class="card">
                        <i class="fas fa-building"></i>
                        <div class="card-content">
                            <strong>{{ $team->name }}</strong>
                            <div class="download-buttons">
                                <form action="{{ route('manager.download_team_report') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="team_id" value="{{ $team->id }}">
                                    <button type="submit" name="format" value="pdf" class="small-button">
                                        PDF <i class="fas fa-download download-icon"></i>
                                    </button>
                                    <button type="submit" name="format" value="xls" class="small-button">
                                        XLS <i class="fas fa-download download-icon"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        </div>
        @endif

        @if($manager->role == 'head')
        <!-- Zakładka Dział - Indywidualne -->
        <div id="department_individual-tab" style="display:none;">
            <!-- Filtrowanie po użytkowniku -->
            <div class="form-group">
                <label for="department-employee-select">Wybierz pracownika:</label>
                <select id="department-employee-select" class="custom-select" onchange="filterByDepartmentEmployee()">
                    <option value="">-- Wybierz pracownika --</option>
                    @foreach($departmentEmployees as $emp)
                        <option value="{{ $emp->id }}" {{ isset($employee) && $employee->id == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>

                @if(isset($employee))
                <a href="{{ route('manager.generate_pdf', ['employeeId' => $employee->id]) }}" target="_blank">
                    <button type="button">
                        PDF <i class="fas fa-download download-icon"></i>
                    </button>
                </a>
                <a href="{{ route('manager.generate_xls', ['employeeId' => $employee->id]) }}" target="_blank">
                    <button type="button">
                        XLS <i class="fas fa-download download-icon"></i>
                    </button>
                </a>
                @endif
            </div>


            @if(isset($employee))
                <div class="employee-details">
                    <div class="employee-details-row">
                        <div class="employee-detail-item">
                            <i class="fas fa-user"></i> <strong>Imię i nazwisko:</strong> {{ $employee->name }}
                        </div>
                        <div class="employee-detail-item">
                            <i class="fas fa-calendar-alt"></i> <strong>Data przesłania:</strong> {{ $employee->created_at }}
                        </div>
                    </div>
                    <div class="employee-details-row">
                        <div class="employee-detail-item">
                            <i class="fas fa-building"></i> <strong>Dział:</strong> {{ $employee->department }}
                        </div>
                        <div class="employee-detail-item">
                            <i class="fas fa-sync-alt"></i> <strong>Data aktualizacji:</strong> {{ $employee->updated_at }}
                        </div>
                    </div>
                    <div class="employee-details-row">
                        <div class="employee-detail-item">
                            <i class="fas fa-user-tie"></i> <strong>Przełożony:</strong> {{ $employee->manager_username }}
                        </div>
                        <div class="employee-detail-item">
                            <i class="fas fa-clipboard"></i> <strong>Stanowisko:</strong> {{ $employee->job_title }}
                        </div>
                    </div>
                </div>

                <!-- Formularz wyników (podobny do HR - Indywidualne) -->
                <form action="{{ route('manager.panel.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <div>
                        <table id="results-table">
                            <thead>
                                <tr>
                                    <th class="col-kompetencja">Kompetencja</th>
                                    <th class="col-small">Ocena użytkownika</th>
                                    <th class="col-small">Czy powyżej oczekiwań (użytkownik)</th>
                                    <th class="col-wide">Argumentacja użytkownika</th>
                                    <th class="col-small">Wartość</th>
                                    <th class="col-small">Ocena managera</th>
                                    <th class="col-wide">Feedback od managera</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentLevel = null;
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
                                @foreach($results->sortBy('competency.level') as $result)
                                    @if($currentLevel != $result->competency->level)
                                        @if($currentLevel != null)
                                            <!-- Separator między poziomami -->
                                            <tr><td colspan="7"></td></tr>
                                        @endif
                                        @php
                                            $currentLevel = $result->competency->level;
                                        @endphp
                                        <!-- Nagłówek poziomu -->
                                        <tr>
                                            <td colspan="7" style="background-color: #f2f2f2;"><strong>Poziom: {{ $currentLevel }}</strong></td>
                                        </tr>
                                    @endif
                                    @php
                                        // Pobierz wartość kompetencji dla zespołu pracownika
                                        $competencyValue = $result->competency->getValueForTeam($employee->team->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $result->competency->competency_name }}
                                            <span class="info-icon" onclick="showDefinitionModal({{ $result->competency->id }})">i</span>
                                            <div class="badge-container">
                                                <span class="badge level">Poziom {{ $result->competency->level }}</span>
                                                <span class="badge competency {{ getCompetencyClass($result->competency->competency_type) }}">{{ $result->competency->competency_type }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $result->score > 0 ? $result->score : 'N/D' }}</td>
                                        <td>{{ $result->above_expectations ? 'Tak' : 'Nie' }}</td>
                                        <td>{{ $result->comments }}</td>
                                        <td>{{ $competencyValue }}</td>

                                        <!-- Pozostałe kolumny -->
                                        <td>
                                            <!-- Ocena managera -->
                                            <select class="custom-select" name="score_manager[{{ $result->id }}]">
                                                <option value="" {{ is_null($result->score_manager) ? 'selected' : '' }}>Ok</option>
                                                <option value="0" {{ $result->score_manager === 0.0 && !$result->above_expectations_manager ? 'selected' : '' }}>N/D</option>
                                                <option value="0.25" {{ $result->score_manager === 0.25 ? 'selected' : '' }}>0.25</option>
                                                <option value="0.5" {{ $result->score_manager === 0.5 ? 'selected' : '' }}>0.5</option>
                                                <option value="0.75" {{ $result->score_manager === 0.75 ? 'selected' : '' }}>0.75</option>
                                                <option value="1" {{ $result->score_manager === 1.0 && !$result->above_expectations_manager ? 'selected' : '' }}>1</option>
                                                <option value="above_expectations" {{ $result->above_expectations_manager ? 'selected' : '' }}>Powyżej oczekiwań</option>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea class="feedback" name="feedback_manager[{{ $result->id }}]">{{ $result->feedback_manager }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(isset($levelSummaries))
                    <div class="level-summaries">
                        <div class="cards-container">
                            @foreach($levelSummaries as $level => $summary)
                                <div class="card">
                                    @php
                                        // Wybór ikony i koloru w zależności od poziomu
                                        $iconClass = 'fas fa-star';
                                        $iconColor = '#4CAF50'; // Domyślny kolor

                                        if(strpos($level, '1') !== false) {
                                            $iconClass = 'fas fa-user-graduate';
                                            $iconColor = '#2196F3'; // Niebieski
                                        } elseif(strpos($level, '2') !== false) {
                                            $iconClass = 'fas fa-user';
                                            $iconColor = '#4CAF50'; // Zielony
                                        } elseif(strpos($level, '3') !== false) {
                                            $iconClass = 'fas fa-user-tie';
                                            $iconColor = '#FF9800'; // Pomarańczowy
                                        } elseif(strpos($level, '4') !== false) {
                                            $iconClass = 'fas fa-chalkboard-teacher';
                                            $iconColor = '#9C27B0'; // Fioletowy
                                        } elseif(strpos($level, '5') !== false) {
                                            $iconClass = 'fas fa-user-cog';
                                            $iconColor = '#F44336'; // Czerwony
                                        } elseif(strpos($level, '6') !== false) {
                                            $iconClass = 'fas fa-user-shield';
                                            $iconColor = '#795548'; // Brązowy
                                        }
                                    @endphp
                                    <i class="{{ $iconClass }}" style="color: {{ $iconColor }};"></i>
                                    <div class="card-content">
                                        <strong>Poziom {{ $level }}</strong>
                                        <p>
                                            {{ $summary['earnedPointsManager'] }} / {{ $summary['maxPoints'] }} pkt.
                                        </p>
                                        @if(is_numeric($summary['percentageEmployee']))
                                            <p>
                                                Samoocena:
                                                <span class="{{ $summary['percentageEmployee'] >= ($level == 1 ? 80 : 85) ? 'high-percentage' : '' }}">
                                                    {{ number_format($summary['percentageEmployee'], 2) }}%
                                                </span>
                                            </p>
                                        @else
                                            <p>Samoocena: {{ $summary['percentageEmployee'] }}</p>
                                        @endif
                                        @if(is_numeric($summary['percentageManager']))
                                            <p>
                                                Feedback:
                                                <span class="{{ $summary['percentageManager'] >= ($level == 1 ? 80 : 85) ? 'high-percentage' : '' }}">
                                                    {{ number_format($summary['percentageManager'], 2) }}%
                                                </span>
                                            </p>
                                        @else
                                            <p>Feedback: {{ $summary['percentageManager'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                    </div>

                    <!-- Zapisz przyciski -->
                    <div style="margin-top: 20px;">
                        <button type="submit">Zapisz zmiany</button>
                    </div>
                </form>
            @else
                <p>Wybierz pracownika, aby zobaczyć wyniki.</p>
            @endif
        </div>
        @endif

        @if($manager->role == 'head')
        <!-- Zakładka Dział -->
        <div id="department-tab" style="display:none;">
            <!-- Tabela z podsumowaniem działu -->
            <table>
            <thead>
                <tr>
                    <th>Imię i nazwisko</th>
                    <th>Nazwa stanowiska</th>
                    @foreach($levelNames as $levelKey => $levelName)
                        <th>{{ $levelName }}</th>
                    @endforeach
                    <th>Poziom</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departmentEmployeesData as $emp)
                    <tr>
                        <td>{{ $emp['name'] }}</td>
                        <td>{{ $emp['job_title'] }}</td>
                        @foreach($levelNames as $levelKey => $levelName)
                            <!-- Manager's Percentage -->
                            <td>
                                @php
                                    $percentageManager = $emp['levelPercentagesManager'][$levelName] ?? null;
                                    $threshold = $levelKey == 1 ? 80 : 85;
                                    $isHighManager = is_numeric($percentageManager) && $percentageManager >= $threshold;
                                @endphp
                                <span class="{{ $isHighManager ? 'high-percentage' : '' }}">
                                    {{ is_numeric($percentageManager) ? number_format((float)$percentageManager, 2) . '%' : 'N/D' }}
                                </span>
                            </td>
                        @endforeach
                        <!-- Highest Level based on Manager's Assessment -->
                        <td>{{ $emp['highestLevelManager'] }}</td>
                    </tr>
                @endforeach
            </tbody>


            </table>

            @php
                // Initialize variables for counting
                $levelCounts = array_fill_keys($levelNames, 0);
                $filledSurveyCount = 0;

                foreach($departmentEmployeesData as $emp) {
                    // Calculate the highest level based on manager's assessment
                    $highestLevel = $emp['highestLevelManager'];

                    // Count employees at each level
                    if(isset($highestLevel)) {
                        $levelCounts[$highestLevel] += 1;
                    }

                    // Check if the employee has filled out the survey
                    if (!empty($emp['levelPercentagesManager'])) {
                        $filledSurveyCount += 1;
                    }
                }
            @endphp




            <!-- Summary similar to the HR tab -->
            <div class="summary" style="margin-top: 20px;">
            <div class="cards-container">
                @foreach($levelNames as $levelName)
                    @php
                        // Ustal ikonę na podstawie nazwy poziomu
                        if($levelName == 'Junior') {
                            $iconClass = 'fas fa-user-graduate';
                        } elseif($levelName == 'Specjalista') {
                            $iconClass = 'fas fa-user';
                        } elseif($levelName == 'Senior') {
                            $iconClass = 'fas fa-user-tie';
                        } elseif($levelName == 'Supervisor') {
                            $iconClass = 'fas fa-chalkboard-teacher';
                        } elseif($levelName == 'Manager') {
                            $iconClass = 'fas fa-user-cog';
                        } elseif($levelName == 'Head of') {
                            $iconClass = 'fas fa-user-shield';
                        } else {
                            $iconClass = 'fas fa-briefcase';
                        }

                        // Ustal kolor ikony na podstawie wartości liczby
                        $iconColorClass = $levelCounts[$levelName] == 0 ? 'icon-gray' : 'icon-green';
                    @endphp
                    <div class="card">
                        <i class="{{ $iconClass }} {{ $iconColorClass }}"></i>
                        <div class="card-content">
                            <strong>{{ $levelName }}</strong>
                            <p>{{ $levelCounts[$levelName] }} {{ pluralForm($levelCounts[$levelName], ['osoba', 'osoby', 'osób']) }}</p>
                        </div>
                    </div>
                @endforeach

                    <div class="card">
                        <i class="fas fa-check icon-green"></i>
                        <div class="card-content">
                            <strong>Przesłało</strong>
                            <p>{{ $filledSurveyCount }} {{ pluralForm($filledSurveyCount, ['osoba', 'osoby', 'osób']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif





        <!-- Modal -->
        <div id="definitionModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="closeDefinitionModal()">&times;</span>
                <div id="definitionContent">
                    <!-- The definition content will be loaded here -->
                </div>
            </div>
        </div>

        @php
        $tabIds = ['individual', 'team'];
        if ($manager->role == 'supermanager') {
            $tabIds[] = 'hr_individual';
            $tabIds[] = 'hr';
        }
        if ($manager->role == 'head') {
            $tabIds[] = 'department_individual';
            $tabIds[] = 'department';
        }
        @endphp

        @php
        function pluralForm($number, $forms) {
            $number = abs($number);
            if ($number == 1) {
                return $forms[0];
            } elseif ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20)) {
                return $forms[1];
            } else {
                return $forms[2];
            }
        }
        @endphp



        <script>
    const tabIds = @json($tabIds);
    console.log('tabIds:', tabIds);

    function showTab(tab) {
        tabIds.forEach(tabId => {
            const tabElement = document.getElementById(tabId + '-tab');
            if (tabElement) {
                tabElement.style.display = tab === tabId ? 'block' : 'none';
            }
        });

        // Update focus class on buttons
        const tabButtons = document.querySelectorAll('.button-tab');
        tabButtons.forEach(button => {
            if (button.getAttribute('data-tab') === tab) {
                button.classList.add('focus');
            } else {
                button.classList.remove('focus');
            }
        });

        // Update the URL parameter without reloading the page
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url.toString());
    }

    function filterByDepartmentEmployee() {
        const employeeId = document.getElementById('department-employee-select').value;
        const url = new URL(window.location.href);
        if (employeeId) {
            url.searchParams.set('employee', employeeId);
        } else {
            url.searchParams.delete('employee');
        }
        url.searchParams.set('tab', 'department_individual');
        window.location.href = url.toString();
    }


    document.addEventListener('DOMContentLoaded', function() {
        // Dodaj nasłuchiwacze zdarzeń do przycisków
        const tabButtons = document.querySelectorAll('.button-tab');
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tab = this.getAttribute('data-tab');
                showTab(tab);
            });
        });

        const urlParams = new URLSearchParams(window.location.search);
        const defaultTab = urlParams.get('tab') || 'individual';
        console.log('DOMContentLoaded: defaultTab is', defaultTab);
        showTab(defaultTab);
    });

    function filterByEmployee() {
        const employeeId = document.getElementById('employee-select').value;
        const url = new URL(window.location.href);
        if (employeeId) {
            url.searchParams.set('employee', employeeId);
        } else {
            url.searchParams.delete('employee');
        }
        url.searchParams.delete('tab'); // Optionally reset the tab
        window.location.href = url.toString();
    }

    function filterByHREmployee() {
        const employeeId = document.getElementById('hr-employee-select').value;
        const url = new URL(window.location.href);
        if (employeeId) {
            url.searchParams.set('employee', employeeId);
        } else {
            url.searchParams.delete('employee');
        }
        url.searchParams.set('tab', 'hr_individual');
        window.location.href = url.toString();
    }

            function showDefinitionModal(competencyId) {
                fetch('/competency-definition/' + competencyId)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('definitionContent').innerHTML = data;
                        document.getElementById('definitionModal').style.display = 'block';
                    });
            }

            function closeDefinitionModal() {
                document.getElementById('definitionModal').style.display = 'none';
            }

            document.addEventListener('click', function(event) {
                const modal = document.getElementById('definitionModal');
                if (event.target === modal) {
                    closeDefinitionModal();
                }
            });
        </script>
    </div>
</body>
</html>