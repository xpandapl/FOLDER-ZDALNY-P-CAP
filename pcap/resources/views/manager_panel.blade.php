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
            margin-top:50px;
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

        .competency-value-display {
            font-weight: bold;
        }

        .competency-value-overridden {
            color: lightblue;
        }

        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: lightgrey;
            border-radius: 50%;
            padding: 2px;
            margin-left: 5px; /* Optional spacing between the value and the icon */
            cursor: pointer;
        }

        .icon-wrapper:hover {
            background-color: grey; /* Optional: Change background color on hover */
        }

        .icon-button {
            color: black;
            font-size: 14px; /* Adjust size as needed */
        }
        .notification-bar {
            background-color: midnightblue; /* Jasnoniebieski kolor */
            color: white;             /* Biały kolor tekstu */
            padding: 5px;             /* Margines wewnętrzny */
            text-align: center;       /* Wyśrodkowanie tekstu */
            font-size: 14px;          /* Rozmiar czcionki */
            position: fixed;          /* Przyklejenie paska do góry ekranu */
            top: 0;                   /* Pozycja od góry */
            left: 0;                  /* Pozycja od lewej */
            width: 100%;              /* Szerokość na całe okno */
            z-index: 1000;            /* Zapewnienie, że pasek będzie na wierzchu */
        }




    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <!-- Select2 - biblioteka potrzebna do wyszukiwania na droplistach-->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div style="display:none;" class="notification-bar">
            W nocy była aktualizacja, przez którą trochę się to wszystko zacina, proszę o cierpliwość - jutro powinno być lepiej.
        </div>
    <div class="container">
        <h2>Panel Managera</h2>

        <!-- Przycisk Wyloguj i Panel Administratora -->
        <div style="text-align: right; margin-bottom: 20px;">
            <!-- Formularz wylogowania -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            @if($manager->role == 'supermanager')
                <!-- Przycisk Panel Administratora -->
                <a href="{{ url('/admin') }}">
                    <button class="logout-button">
                        Panel Administratora
                    </button>
                </a>
            @endif

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
        <button class="button-tab" data-tab="codes">
            <i class="fas fa-key"></i> Kody dostępu
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

    <!-- Cycle switcher -->
    <div class="form-group" style="margin-top:10px;">
        <label for="cycle-select">Cykl:</label>
        <select id="cycle-select" class="custom-select" onchange="onCycleChange()">
            @foreach(($cycles ?? []) as $c)
                <option value="{{ $c->id }}" {{ (isset($selectedCycleId) && $selectedCycleId == $c->id) ? 'selected' : '' }}>
                    {{ $c->label }} {{ $c->is_active ? '(aktywny)' : '' }}
                </option>
            @endforeach
        </select>
        @if(isset($selectedCycle) && !$isSelectedCycleActive)
            <span style="color:#b00; font-size:12px;">Wybrany cykl jest historyczny – edycja zablokowana.</span>
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
        <a href="{{ route('manager.generate_pdf', ['employeeId' => $employee->id]) }}?cycle={{ $selectedCycleId }}" target="_blank">
            <button type="button">
                PDF <i class="fas fa-download download-icon"></i>
            </button>
        </a>
        <a href="{{ route('manager.generate_xls', ['employeeId' => $employee->id]) }}?cycle={{ $selectedCycleId }}" target="_blank">
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
        @if(session('generated_code') && session('generated_code_employee_id') == $employee->id)
            <div style="margin:10px 0; padding:10px; background:#e6ffed; border:1px solid #b2f5bc;">
                <strong>Nowy kod dostępu:</strong>
                <span style="font-family:monospace;">{{ session('generated_code') }}</span>
                <span style="color:#555;">(zapisz teraz – nie będzie już widoczny w całości)</span>
            </div>
        @endif
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
                                $competencyValue = $employee->getCompetencyValue($result->competency_id) ?? 0;
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
                                <td>
                                    <div class="competency-value-container" data-competency-id="{{ $result->competency_id }}" data-original-value="{{ $overriddenValues[$result->competency_id] ?? $competencyValue }}" data-team-value="{{ $competencyValue }}">
                                        <span class="competency-value-display {{ isset($overriddenValues[$result->competency_id]) ? 'competency-value-overridden' : '' }}">
                                            {{ $overriddenValues[$result->competency_id] ?? $competencyValue }}
                                        </span>
                                        <input style="width:50px; display: none;" type="number" step="5" value="{{ $overriddenValues[$result->competency_id] ?? $competencyValue }}">
                                        <span class="icon-wrapper">
                                            <i class="fas {{ isset($overriddenValues[$result->competency_id]) ? 'fa-trash remove-overridden-value-button' : 'fa-pencil-alt edit-competency-value-button' }} icon-button" data-competency-id="{{ $result->competency_id }}"></i>
                                        </span>
                                    </div>
                                </td>




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
                <button type="submit" {{ (isset($isSelectedCycleActive) && !$isSelectedCycleActive) ? 'disabled title="Edycja zablokowana dla cyklu historycznego"' : '' }}>Zapisz zmiany</button>
                @if(isset($isSelectedCycleActive) && $isSelectedCycleActive)
                    <form action="{{ route('manager.generate_access_code', ['employeeId' => $employee->id]) }}" method="POST" style="display:inline-block; margin-left:10px;">
                        @csrf
                        <button type="submit" title="Wygeneruj kod dla aktywnego cyklu">Wygeneruj kod dostępu</button>
                    </form>
                @endif
            </div>
        </form>
    @else
        <p>Wybierz pracownika, aby zobaczyć wyniki.</p>
    @endif
</div> <!-- Koniec zakładki Indywidualne -->

<!-- Zakładka Kody dostępu -->
<div id="codes-tab" style="display:none;">
    <h3>Kody dostępu dla zespołu</h3>
    @php
        // Zależnie od roli, wybierz odpowiednią listę pracowników
        $codesEmployees = collect();
        if ($manager->role == 'supermanager') {
            $codesEmployees = $allEmployees;
        } elseif ($manager->role == 'head') {
            $codesEmployees = $departmentEmployees;
        } else {
            $codesEmployees = $employees;
        }
    @endphp
    <div class="form-group" style="margin:10px 0;">
        <span style="font-size:12px;color:#555;">Cykl: {{ $selectedCycle->label ?? '-' }}. Generowanie dostępne tylko dla aktywnego cyklu.</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Imię i nazwisko</th>
                <th>Stanowisko</th>
                <th>Dział/Zespół</th>
                <th>Ostatnie 4</th>
                <th>Wygasa</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($codesEmployees ?? collect()) as $emp)
                @php
                    $code = ($employeeAccessCodes ?? collect())->get($emp->id);
                @endphp
                <tr>
                    <td>{{ $emp->name }}</td>
                    <td>{{ $emp->job_title }}</td>
                    <td>{{ $emp->department }}</td>
                    <td>
                        @if($code && $code->raw_last4)
                            <span style="font-family:monospace;" title="Ostatnie 4">•••• {{ $code->raw_last4 }}</span>
                            <button class="small-button" onclick="copyText('{{ $code->raw_last4 }}')" title="Kopiuj ostatnie 4">Kopiuj</button>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($code && $code->expires_at)
                            {{ optional($code->expires_at)->format('Y-m-d H:i') }}
                        @else
                            bez terminu
                        @endif
                    </td>
                    <td>
                        @if(isset($isSelectedCycleActive) && $isSelectedCycleActive)
                            <form action="{{ route('manager.generate_access_code', ['employeeId' => $emp->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="small-button">{{ $code ? 'Regeneruj' : 'Generuj' }}</button>
                            </form>
                        @else
                            <span style="font-size:12px;color:#999;" title="Tylko dla aktywnego cyklu">zablokowane</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(session('generated_code'))
        <div style="margin:10px 0; padding:10px; background:#e6ffed; border:1px solid #b2f5bc;">
            <strong>Nowy kod (pełny, jednorazowy podgląd):</strong>
            <span style="font-family:monospace;">{{ session('generated_code') }}</span>
            <button class="small-button" onclick="copyText('{{ session('generated_code') }}')">Kopiuj</button>
            <div style="font-size:12px;color:#555;">Kod nie będzie już widoczny w całości po odświeżeniu.</div>
        </div>
    @endif
</div>

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
        <a href="{{ route('manager.generate_pdf', ['employeeId' => $employee->id]) }}?cycle={{ $selectedCycleId }}" target="_blank">
            <button type="button">
                PDF <i class="fas fa-download download-icon"></i>
            </button>
        </a>
        <a href="{{ route('manager.generate_xls', ['employeeId' => $employee->id]) }}?cycle={{ $selectedCycleId }}" target="_blank">
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
                <button type="submit" {{ (isset($isSelectedCycleActive) && !$isSelectedCycleActive) ? 'disabled title="Edycja zablokowana dla cyklu historycznego"' : '' }}>Zapisz zmiany</button>
            </div>
        </form>
    @else
        <p>Wybierz pracownika, aby zobaczyć wyniki.</p>
    @endif
</div> <!-- Koniec zakładki HR - Indywidualne -->
@endif



        <!-- Zakładka Cały Zespół -->
        <div id="team-tab" style="display:none;">
            @if($employees->isEmpty())
                <div style="text-align: center; padding: 40px; background-color: #f8f9fa; border-radius: 8px; margin: 20px 0;">
                    <i class="fas fa-users" style="font-size: 48px; color: #6c757d; margin-bottom: 15px;"></i>
                    <h3 style="color: #6c757d; margin-bottom: 10px;">Brak przypisanych pracowników</h3>
                    <p style="color: #6c757d; margin-bottom: 20px;">
                        @if($manager->role === 'supermanager')
                            Aby widzieć pracowników w zakładce "Twój zespół", musisz być przypisany do struktury hierarchii jako supervisor, manager lub head.
                            <br><br>
                            <strong>Wszyscy pracownicy są dostępni w zakładkach HR.</strong>
                        @else
                            Nie masz jeszcze przypisanych pracowników w systemie hierarchii.
                        @endif
                    </p>
                    @if($manager->role === 'supermanager')
                        <a href="/admin" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
                            <i class="fas fa-sitemap"></i> Przejdź do panelu administracyjnego
                        </a>
                    @endif
                </div>
            @else
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
                                            $threshold = $levelKey == 1 ? 80 : ($levelKey == 2 || $levelKey == 3 ? 85 : 80);
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
            @endif


            @php
                // Initialize variables for counting
                $levelCounts = array_fill_keys($levelNames, 0);

                foreach($teamEmployeesData as $emp) {
                    // Calculate the highest level based on manager's assessment
                    $highestLevel = $emp['highestLevelManager'];

                    // MAPOWANIE poziomu 3/4. Senior/Supervisor na 4. Supervisor dla podsumowań
                    if ($highestLevel === "3/4. Senior/Supervisor") {
                        $highestLevel = "4. Supervisor";
                    }

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
                        // Ustal ikonę i kolor na podstawie fragmentów tekstu w $levelName
                        $iconClass = 'fas fa-briefcase';
                        $iconColor = '#4CAF50'; // Domyślny kolor ikony jeśli nie rozpoznamy poziomu
                        $count = $levelCounts[$levelName] ?? 0;

                        if (strpos($levelName, '1. Junior') !== false) {
                            $iconClass = 'fas fa-user-graduate';
                            $iconColor = '#2196F3';
                        } elseif (strpos($levelName, '2. Specjalista') !== false) {
                            $iconClass = 'fas fa-user';
                            $iconColor = '#4CAF50';
                        } elseif (strpos($levelName, '3/4. Senior/Supervisor') !== false) {
                            $iconClass = 'fas fa-chalkboard-teacher';
                            $iconColor = '#9C27B0';
                        } elseif (strpos($levelName, '3. Senior') !== false) {
                            $iconClass = 'fas fa-user-tie';
                            $iconColor = '#FF9800';
                        } elseif (strpos($levelName, '4. Supervisor') !== false) {
                            $iconClass = 'fas fa-chalkboard-teacher';
                            $iconColor = '#9C27B0';
                        } elseif (strpos($levelName, '5. Manager') !== false) {
                            $iconClass = 'fas fa-user-cog';
                            $iconColor = '#F44336';
                        }

                        // Ustal kolor ikony na podstawie wartości liczby
                        $iconColorClass = $levelCounts[$levelName] == 0 ? 'icon-gray' : 'icon-green';
                    @endphp

                    <div class="card">
                        <i class="{{ $iconClass }} {{ $iconColorClass }}"></i>
                        <div class="card-content">
                            <strong>{{ $levelName }}</strong>
                            <p>{{ $count }} {{ pluralForm($count, ['osoba', 'osoby', 'osób']) }}</p>
                        </div>

                        <script>
                            function onCycleChange(){
                                const sel = document.getElementById('cycle-select');
                                const cycle = sel ? sel.value : '';
                                const url = new URL(window.location.href);
                                if(cycle){ url.searchParams.set('cycle', cycle); }
                                else { url.searchParams.delete('cycle'); }
                                // Preserve selected employee if any
                                const empSel = document.getElementById('employee-select');
                                if(empSel && empSel.value){ url.searchParams.set('employee', empSel.value); }
                                window.location.href = url.toString();
                            }
                            function filterByEmployee(){
                                const empSel = document.getElementById('employee-select');
                                const url = new URL(window.location.href);
                                if(empSel && empSel.value){ url.searchParams.set('employee', empSel.value); }
                                else { url.searchParams.delete('employee'); }
                                // keep cycle
                                const cycleSel = document.getElementById('cycle-select');
                                if(cycleSel && cycleSel.value){ url.searchParams.set('cycle', cycleSel.value); }
                                window.location.href = url.toString();
                            }
                            function filterByHREmployee(){
                                // mirrors filterByEmployee for HR tab
                                filterByEmployee();
                            }
                        </script>
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
            <div class="form-group" style="margin:10px 0;">
                <input type="text" id="hr-search" class="custom-input" placeholder="Wyszukaj po imieniu, nazwisku lub stanowisku..." style="max-width:420px; width:100%;">
            </div>
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
            <tbody id="hr-table-body">
                @foreach($organizationEmployeesData as $emp)
                    <tr>
                        <td>{{ $emp['name'] }}</td>
                        <td>{{ $emp['job_title'] }}</td>
                        @foreach($levelNames as $levelKey => $levelName)
                            <!-- Manager's Percentage -->
                            <td>
                                @php
                                    $percentageManager = $emp['levelPercentagesManager'][$levelName] ?? null;
                                    $threshold = $levelKey == 1 ? 80 : ($levelKey == 2 || $levelKey == 3 ? 85 : 80);
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

                    // MAPOWANIE poziomu 3/4. Senior/Supervisor na 4. Supervisor dla podsumowań
                    if ($highestLevel === "3/4. Senior/Supervisor") {
                        $highestLevel = "4. Supervisor";
                    }

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
                        if($levelName == '1. Junior') {
                            $iconClass = 'fas fa-user-graduate';
                        } elseif($levelName == '2. Specjalista') {
                            $iconClass = 'fas fa-user';
                        } elseif($levelName == '3. Senior') {
                            $iconClass = 'fas fa-user-tie';
                        } elseif($levelName == 'Supervisor') {
                            $iconClass = 'fas fa-chalkboard-teacher';
                        } elseif($levelName == 'Manager') {
                            $iconClass = 'fas fa-user-cog';
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
                                    <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
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
             <!-- Add Download Button -->
            <div style="margin-bottom: 20px;">
                <form action="{{ route('department.export') }}" method="GET">
                    @csrf
                    <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                    <button type="submit">
                        Pobierz XLS <i class="fas fa-download download-icon"></i>
                    </button>
                </form>
            </div>
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
                                    $threshold = $levelKey == 1 ? 80 : ($levelKey == 2 || $levelKey == 3 ? 85 : 80);
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

                    // MAPOWANIE poziomu 3/4. Senior/Supervisor na 4. Supervisor dla podsumowań
                    if ($highestLevel === "3/4. Senior/Supervisor") {
                        $highestLevel = "4. Supervisor";
                    }

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
        $tabIds = ['individual', 'team', 'codes'];
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
        if (!function_exists('pluralForm')) {
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
        }
        @endphp




        <script>
        $(document).ready(function() {
            // Inicjalizacja Select2 dla odpowiednich pól wyboru
            $('#employee-select').select2({
                placeholder: '-- Wybierz pracownika --',
                allowClear: true,
                width: 'resolve'
            });

            $('#hr-employee-select').select2({
                placeholder: '-- Wybierz pracownika --',
                allowClear: true,
                width: 'resolve'
            });

            $('#department-employee-select').select2({
                placeholder: '-- Wybierz pracownika --',
                allowClear: true,
                width: 'resolve'
            });

            // Dodaj nasłuchiwacze zdarzeń do przycisków zakładek
            const tabButtons = document.querySelectorAll('.button-tab');
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tab = this.getAttribute('data-tab');
                    showTab(tab);
                });
            });

            // Ustaw domyślną zakładkę na podstawie parametrów URL
            const urlParams = new URLSearchParams(window.location.search);
            const defaultTab = urlParams.get('tab') || 'individual';
            console.log('DOMContentLoaded: defaultTab is', defaultTab);
            showTab(defaultTab);

            // Globalny nasłuchiwacz zdarzeń kliknięcia
            // HR filtrowanie – po imieniu/nazwisku i stanowisku
            $('#hr-search').on('input', function(){
                const q = $(this).val().toLowerCase();
                $('#hr-table-body tr').each(function(){
                    const name = ($(this).find('td').eq(0).text() || '').toLowerCase();
                    const job  = ($(this).find('td').eq(1).text() || '').toLowerCase();
                    const match = name.indexOf(q) > -1 || job.indexOf(q) > -1;
                    $(this).toggle(match);
                });
            });

            document.addEventListener('click', function(event) {
                const target = event.target;

                // Obsługa kliknięć ikon edycji wartości kompetencji
                if (target.classList.contains('edit-competency-value-button')) {
                    handleEditCompetencyValue(target);
                } else if (target.classList.contains('save-competency-value-button')) {
                    handleSaveCompetencyValue(target);
                } else if (target.classList.contains('remove-overridden-value-button')) {
                    handleRemoveOverriddenValue(target);
                }

                // Zamknięcie modala po kliknięciu poza jego zawartość
                const modal = document.getElementById('definitionModal');
                if (event.target === modal) {
                    closeDefinitionModal();
                }
            });
        });

        // Definicje funkcji
        const tabIds = @json($tabIds);
        console.log('tabIds:', tabIds);

        function showTab(tab) {
            tabIds.forEach(tabId => {
                const tabElement = document.getElementById(tabId + '-tab');
                if (tabElement) {
                    tabElement.style.display = tab === tabId ? 'block' : 'none';
                }
            });

            // Aktualizacja klasy focus na przyciskach
            const tabButtons = document.querySelectorAll('.button-tab');
            tabButtons.forEach(button => {
                if (button.getAttribute('data-tab') === tab) {
                    button.classList.add('focus');
                } else {
                    button.classList.remove('focus');
                }
            });

            // Aktualizacja parametru URL bez przeładowania strony
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url.toString());
        }

        function filterByEmployee() {
            const employeeId = document.getElementById('employee-select').value;
            const url = new URL(window.location.href);
            if (employeeId) {
                url.searchParams.set('employee', employeeId);
            } else {
                url.searchParams.delete('employee');
            }
            url.searchParams.delete('tab'); // Opcjonalnie resetuj zakładkę
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

        function copyText(text) {
            if (!navigator.clipboard) {
                // Fallback
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); } catch(e) {}
                document.body.removeChild(ta);
                return;
            }
            navigator.clipboard.writeText(text);
        }

        // Funkcje obsługujące edycję wartości kompetencji
        function handleEditCompetencyValue(icon) {
            const competencyId = icon.getAttribute('data-competency-id');
            const container = document.querySelector('.competency-value-container[data-competency-id="' + competencyId + '"]');
            const inputField = container.querySelector('input');
            const displaySpan = container.querySelector('.competency-value-display');

            // Pokaż pole input i ukryj wyświetlaną wartość
            displaySpan.style.display = 'none';
            inputField.style.display = 'inline-block';
            inputField.focus();

            // Zmień ikonę na ikonę zapisu
            icon.classList.remove('edit-competency-value-button', 'fa-pencil-alt');
            icon.classList.add('save-competency-value-button', 'fa-save');
        }

        function handleSaveCompetencyValue(icon) {
            const competencyId = icon.getAttribute('data-competency-id');
            const container = document.querySelector('.competency-value-container[data-competency-id="' + competencyId + '"]');
            const inputField = container.querySelector('input');
            const displaySpan = container.querySelector('.competency-value-display');

            // Aktualizuj wyświetlaną wartość
            const newValue = inputField.value;
            displaySpan.textContent = newValue;
            displaySpan.style.color = 'lightblue';

            // Ukryj pole input i pokaż wyświetlaną wartość
            inputField.style.display = 'none';
            displaySpan.style.display = 'inline';

            // Dodaj klasę oznaczającą nadpisaną wartość
            displaySpan.classList.add('competency-value-overridden');

            // Dodaj atrybut name do inputa, aby został przesłany w formularzu
            inputField.setAttribute('name', 'competency_values[' + competencyId + ']');

            // Zmień ikonę na ikonę usuwania
            icon.classList.remove('save-competency-value-button', 'fa-save');
            icon.classList.add('remove-overridden-value-button', 'fa-trash');
        }

        function handleRemoveOverriddenValue(icon) {
            const competencyId = icon.getAttribute('data-competency-id');
            const container = document.querySelector('.competency-value-container[data-competency-id="' + competencyId + '"]');
            const inputField = container.querySelector('input');
            const displaySpan = container.querySelector('.competency-value-display');
            const teamValue = container.getAttribute('data-team-value');

            // Usuń atrybut name z inputa, aby nie był przesyłany
            inputField.removeAttribute('name');

            // Ustaw wyświetlaną wartość na wartość zespołu
            displaySpan.textContent = teamValue;
            displaySpan.style.color = ''; // Resetuj kolor

            // Ukryj pole input i pokaż wyświetlaną wartość
            inputField.style.display = 'none';
            displaySpan.style.display = 'inline';

            // Usuń klasę oznaczającą nadpisaną wartość
            displaySpan.classList.remove('competency-value-overridden');

            // Zmień ikonę na ikonę edycji
            icon.classList.remove('remove-overridden-value-button', 'fa-trash');
            icon.classList.add('edit-competency-value-button', 'fa-pencil-alt');

            // Dodaj ukryte pole input sygnalizujące usunięcie nadpisanej wartości
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete_competency_values[]';
            deleteInput.value = competencyId;
            container.appendChild(deleteInput);

            // Aktualizuj atrybut data-original-value na wartość zespołu
            container.setAttribute('data-original-value', teamValue);
        }
</script>

</body>
</html>