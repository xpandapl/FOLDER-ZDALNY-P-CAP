<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Raport Samooceny</title>
    <style>
        /* Stylizacja do PDF */
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 4px; word-wrap: break-word; }
        th { background-color: #f2f2f2; }
        .level-header {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        .summary-row {
            font-weight: bold;
        }
        /* Dostosuj szerokości kolumn */
        .col-competency { width: 20%; }
        .col-level { width: 7%; }
        .col-type { width: 10%; }
        .col-score { width: 8%; }
        .col-above { width: 8%; }
        .col-comments { width: 23%; }
        .col-value { width: 8%; }
        .col-earned { width: 8%; }
    </style>
</head>
<body>
    <h2>Raport Samooceny dla {{ $employee->name }}</h2>
    <table>
        <thead>
            <tr>
                <th class="col-competency">Kompetencja</th>
                <th class="col-level">Poziom</th>
                <th class="col-type">Rodzaj kompetencji</th>
                <th class="col-score">Ocena</th>
                <th class="col-above">Powyżej oczekiwań</th>
                <th class="col-comments">Argumentacja</th>
                <th class="col-value">Wartość</th>
                <th class="col-earned">Punkty uzyskane</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentLevel = null;
                $levelEarnedPoints = 0;
                $levelPossiblePoints = 0;
                $totalEarnedPoints = 0;
                $totalPossiblePoints = 0;
            @endphp
            @foreach($results->sortBy('competency.level') as $result)
                @php
                    $resultLevel = trim($result->competency->level);
                @endphp
                @if($currentLevel != $resultLevel)
                    @if($currentLevel != null)
                        <!-- Display per-level totals -->
                        <tr class="summary-row">
                            <td colspan="6"><strong>Razem dla poziomu {{ $currentLevel }}</strong></td>
                            <td><strong>{{ $levelPossiblePoints }}</strong></td>
                            <td><strong>{{ $levelEarnedPoints }}</strong></td>
                        </tr>
                        @php
                            $percentage = $levelPossiblePoints > 0 ? ($levelEarnedPoints / $levelPossiblePoints) * 100 : 'N/D';
                        @endphp
                        <tr class="summary-row">
                            <td colspan="8"><strong>Procent uzyskany na poziomie {{ $currentLevel }}: {{ is_numeric($percentage) ? number_format($percentage, 2).'%' : $percentage }}</strong></td>
                        </tr>
                        <!-- Add an empty row as a separator -->
                        <tr><td colspan="8"></td></tr>
                    @endif
                    @php
                        $currentLevel = $resultLevel;
                        $levelEarnedPoints = 0;
                        $levelPossiblePoints = 0;
                    @endphp
                    <!-- Level header -->
                    <tr>
                        <td colspan="8" class="level-header">Poziom: {{ $currentLevel }}</td>
                    </tr>
                @endif
                @php
                    // Get competency value
                    $competencyValue = $result->competency->getValueForTeam($employee->team->id);

                    // User's score
                    $userScore = $result->score;

                    // Calculate earned points if score > 0
                    if ($userScore > 0) {
                        $earnedPoints = $competencyValue * $userScore;
                        $levelEarnedPoints += $earnedPoints;
                        $levelPossiblePoints += $competencyValue;

                        // Accumulate total sums
                        $totalEarnedPoints += $earnedPoints;
                        $totalPossiblePoints += $competencyValue;
                    } else {
                        $earnedPoints = 'N/D';
                    }

                    // Display score
                    $displayScore = $userScore > 0 ? $userScore : 'N/D';
                @endphp
                <tr>
                    <td class="col-competency">{{ $result->competency->competency_name }}</td>
                    <td class="col-level">{{ $result->competency->level }}</td>
                    <td class="col-type">{{ $result->competency->competency_type }}</td>
                    <td class="col-score">{{ $displayScore }}</td>
                    <td class="col-above">{{ $result->above_expectations ? 'Tak' : 'Nie' }}</td>
                    <td class="col-comments">{{ $result->comments }}</td>
                    <td class="col-value">{{ $competencyValue }}</td>
                    <td class="col-earned">{{ is_numeric($earnedPoints) ? $earnedPoints : $earnedPoints }}</td>
                </tr>
            @endforeach

            <!-- Display per-level totals for the last level -->
            @if($currentLevel != null)
                <tr class="summary-row">
                    <td colspan="6"><strong>Razem dla poziomu {{ $currentLevel }}</strong></td>
                    <td><strong>{{ $levelPossiblePoints }}</strong></td>
                    <td><strong>{{ $levelEarnedPoints }}</strong></td>
                </tr>
                @php
                    $percentage = $levelPossiblePoints > 0 ? ($levelEarnedPoints / $levelPossiblePoints) * 100 : 'N/D';
                @endphp
                <tr class="summary-row">
                    <td colspan="8"><strong>Procent uzyskany na poziomie {{ $currentLevel }}: {{ is_numeric($percentage) ? number_format($percentage, 2).'%' : $percentage }}</strong></td>
                </tr>
            @endif

            <!-- Display overall totals -->
            <tr><td colspan="8"></td></tr>
            <tr class="summary-row">
                <td colspan="6"><strong>Razem</strong></td>
                <td><strong>{{ $totalPossiblePoints }}</strong></td>
                <td><strong>{{ $totalEarnedPoints }}</strong></td>
            </tr>
            @php
                $totalPercentage = $totalPossiblePoints > 0 ? ($totalEarnedPoints / $totalPossiblePoints) * 100 : 'N/D';
            @endphp
            <tr class="summary-row">
                <td colspan="8"><strong>Ogólny procent uzyskany: {{ is_numeric($totalPercentage) ? number_format($totalPercentage, 2).'%' : $totalPercentage }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
