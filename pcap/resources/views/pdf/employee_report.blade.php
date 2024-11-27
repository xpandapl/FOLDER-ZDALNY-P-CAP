<!DOCTYPE html>
<html>
<head>
    <title>Raport użytkownika</title>
    <style>
        /* Styles for PDF */
        @page {
            margin: 20px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px; /* Reduce font size to fit more content */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Ensures columns have fixed widths */
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
            word-wrap: break-word; /* Allows long words to break */
            white-space: normal;   /* Enables text wrapping */
        }
        th {
            background-color: #ddd;
        }
        /* Set specific widths for columns */
        .col-competency { width: 15%; }
        .col-level { width: 5%; }
        .col-type { width: 10%; }
        .col-score { width: 5%; }
        .col-above { width: 7%; }
        .col-comments { width: 15%; }
        .col-value { width: 5%; }
        .col-score-manager { width: 5%; }
        .col-above-manager { width: 7%; }
        .col-feedback { width: 15%; }
        .col-earned { width: 7%; }
    </style>
</head>
<body>
    <h2>Raport użytkownika: {{ $employee->name }}</h2>
    <table>
        <thead>
            <tr>
                <th class="col-competency">Kompetencja</th>
                <th class="col-level">Poziom</th>
                <th class="col-type">Rodzaj kompetencji</th>
                <th class="col-score">Ocena użytkownika</th>
                <th class="col-above">Powyżej oczekiwań (użytkownik)</th>
                <th class="col-comments">Argumentacja użytkownika</th>
                <th class="col-value">Wartość</th>
                <th class="col-score-manager">Ocena managera</th>
                <th class="col-above-manager">Powyżej oczekiwań (manager)</th>
                <th class="col-feedback">Feedback od managera</th>
                <th class="col-earned">Punkty uzyskane</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentLevel = null;
                $levelEarnedPoints = 0;
                $levelPossiblePoints = 0;
            @endphp
            @foreach($results->sortBy('competency.level') as $result)
                @if($result->competency)
                    @php
                        $resultLevel = trim($result->competency->level);
                    @endphp
                    @if($currentLevel != $resultLevel)
                        @if($currentLevel != null)
                            <!-- Display per-level totals -->
                            <tr class="summary-row">
                                <td colspan="6"><strong>Razem dla poziomu {{ $currentLevel }}</strong></td>
                                <td><strong>{{ $levelPossiblePoints }}</strong></td>
                                <td colspan="3"></td>
                                <td><strong>{{ $levelEarnedPoints }}</strong></td>
                            </tr>
                            @php
                                $percentage = $levelPossiblePoints > 0 ? ($levelEarnedPoints / $levelPossiblePoints) * 100 : 0;
                            @endphp
                            <tr class="summary-row">
                                <td colspan="11"><strong>Procent uzyskany na poziomie {{ $currentLevel }}: {{ number_format($percentage, 2) }}%</strong></td>
                            </tr>
                            <!-- Add an empty row as a separator between levels -->
                            <tr><td colspan="11"></td></tr>
                        @endif
                        @php
                            $currentLevel = $resultLevel;
                            // Reset per-level sums
                            $levelEarnedPoints = 0;
                            $levelPossiblePoints = 0;
                        @endphp
                        <!-- Add a header row for the new level -->
                        <tr>
                            <td colspan="11" style="background-color: #f2f2f2;"><strong>Poziom: {{ $currentLevel }}</strong></td>
                        </tr>
                    @endif
                    @php
                        // Pobierz wartość kompetencji
                        $competencyValue = $result->competency->getValueForTeam($employee->team->id);

                        // Wybierz ocenę do obliczeń (ocena managera jeśli jest, inaczej użytkownika)
                        $score = $result->score_manager !== null ? $result->score_manager : $result->score;

                        // Jeśli ocena wynosi 0, traktuj jako N/D
                        if ($score > 0) {
                            $earnedPoints = $competencyValue * $score;

                            // Akumuluj sumy
                            $levelEarnedPoints += $earnedPoints;
                            $levelPossiblePoints += $competencyValue;
                        } else {
                            $earnedPoints = 'N/D';
                        }

                        // Wyświetlana ocena
                        $displayScore = $score > 0 ? $score : 'N/D';
                    @endphp

                    <tr>
                        <td class="col-competency">{{ $result->competency->competency_name }}</td>
                        <td class="col-level">{{ $result->competency->level }}</td>
                        <td class="col-type">{{ $result->competency->competency_type }}</td>
                        <td class="col-score">{{ $result->score > 0 ? $result->score : 'N/D' }}</td>
                        <td class="col-above">{{ $result->above_expectations ? 'Tak' : 'Nie' }}</td>
                        <td class="col-comments">{{ $result->comments }}</td>
                        <td class="col-value">{{ $competencyValue }}</td>
                        <td class="col-score-manager">
                            @if($result->score_manager !== null)
                                @if($result->score_manager > 0)
                                    {{ $result->score_manager }}
                                @elseif($result->score_manager == 0)
                                    N/D
                                @endif
                            @else
                                <!-- Puste pole -->
                            @endif
                        </td>
                        <td class="col-above-manager">{{ $result->above_expectations_manager ? 'Tak' : 'Nie' }}</td>
                        <td class="col-feedback">{{ $result->feedback_manager }}</td>
                        <td class="col-earned">{{ is_numeric($earnedPoints) ? $earnedPoints : $earnedPoints }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="11">Brak danych kompetencji dla wyniku ID: {{ $result->id }}</td>
                    </tr>
                @endif
            @endforeach

            <!-- Display per-level totals for the last level -->
            @if($currentLevel != null)
                <tr class="summary-row">
                    <td colspan="6"><strong>Razem dla poziomu {{ $currentLevel }}</strong></td>
                    <td><strong>{{ $levelPossiblePoints }}</strong></td>
                    <td colspan="3"></td>
                    <td><strong>{{ $levelEarnedPoints }}</strong></td>
                </tr>
                @php
                    $percentage = $levelPossiblePoints > 0 ? ($levelEarnedPoints / $levelPossiblePoints) * 100 : 'N/D';
                @endphp
                <tr class="summary-row">
                <td colspan="11"><strong>Procent uzyskany na poziomie {{ $currentLevel }}:
                    @if(is_numeric($percentage))
                        {{ number_format($percentage, 2) }}%
                    @else
                        {{ $percentage }}
                    @endif
                </strong></td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
