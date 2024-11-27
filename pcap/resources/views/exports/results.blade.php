<!-- resources/views/exports/results.blade.php -->

<table>
    <thead>
        <tr>
            <th>Kompetencja</th>
            <th>Poziom</th>
            <th>Rodzaj kompetencji</th>
            <th>Ocena użytkownika</th>
            <th>Czy powyżej oczekiwań (użytkownik)</th>
            <th>Argumentacja użytkownika</th>
            <th>Wartość</th>
            <th>Punkty uzyskane</th>
            <!-- Dodaj kolumny managera, jeśli są potrzebne -->
        </tr>
    </thead>
    <tbody>
        @php
            $currentLevel = null;

            // Inicjalizacja sum dla poziomu
            $levelEarnedPoints = 0;
            $levelPossiblePoints = 0;

            // Inicjalizacja sum ogólnych
            $totalEarnedPoints = 0;
            $totalPossiblePoints = 0;

            // Licznik kompetencji dla danego poziomu
            $levelCompetencyCount = 0;
        @endphp
        @foreach($results->sortBy('competency.level') as $result)
            @if($currentLevel != $result->competency->level)
                @if($currentLevel != null)
                    <!-- Wyświetl podsumowanie dla poziomu przed rozpoczęciem nowego -->
                    <tr>
                        <td colspan="6"><strong>Razem dla poziomu {{ $currentLevel }}</strong></td>
                        <td><strong>{{ $levelPossiblePoints }}</strong></td>
                        <td><strong>{{ $levelEarnedPoints }}</strong></td>
                    </tr>
                    @php
                        $percentage = $levelPossiblePoints > 0 ? ($levelEarnedPoints / $levelPossiblePoints) * 100 : 'N/D';
                    @endphp
                    <tr>
                        <td colspan="8"><strong>Procent uzyskany na poziomie {{ $currentLevel }}: {{ is_numeric($percentage) ? number_format($percentage, 2).'%' : $percentage }}</strong></td>
                    </tr>
                    <!-- Dodaj pusty wiersz jako separator między poziomami -->
                    <tr><td colspan="8"></td></tr>
                @endif
                @php
                    $currentLevel = $result->competency->level;

                    // Zresetuj sumy dla nowego poziomu
                    $levelEarnedPoints = 0;
                    $levelPossiblePoints = 0;
                    $levelCompetencyCount = 0;
                @endphp
                <!-- Dodaj wiersz nagłówkowy dla nowego poziomu -->
                <tr>
                    <td colspan="8" style="background-color: #f2f2f2;"><strong>Poziom: {{ $currentLevel }}</strong></td>
                </tr>
            @endif
            @php
                // Pobierz wartość kompetencji dla zespołu pracownika
                $competencyValue = $result->competency->getValueForTeam($team->id);

                // Ocena użytkownika
                $userScore = $result->score;

                // Oblicz zdobyte punkty tylko jeśli ocena > 0
                if ($userScore > 0) {
                    $earnedPoints = $competencyValue * $userScore;

                    // Akumuluj sumy dla poziomu
                    $levelEarnedPoints += $earnedPoints;
                    $levelPossiblePoints += $competencyValue;

                    // Akumuluj sumy ogólne
                    $totalEarnedPoints += $earnedPoints;
                    $totalPossiblePoints += $competencyValue;

                    $levelCompetencyCount++;
                } else {
                    $earnedPoints = 'N/D';
                }

                // Ustal wyświetlaną ocenę
                $displayScore = $userScore > 0 ? $userScore : 'N/D';
            @endphp
            <tr>
                <td>{{ $result->competency->competency_name }}</td>
                <td>{{ $result->competency->level }}</td>
                <td>{{ $result->competency->competency_type }}</td>
                <td>{{ $displayScore }}</td>
                <td>{{ $result->above_expectations ? 'Tak' : 'Nie' }}</td>
                <td>{{ $result->comments }}</td>
                <td>{{ $competencyValue }}</td>
                <td>{{ is_numeric($earnedPoints) ? $earnedPoints : $earnedPoints }}</td>
            </tr>
        @endforeach

        <!-- Wyświetl podsumowanie dla ostatniego poziomu -->
        @if($currentLevel != null)
            <tr>
                <td colspan="6"><strong>Razem dla poziomu {{ $currentLevel }}</strong></td>
                <td><strong>{{ $levelPossiblePoints }}</strong></td>
                <td><strong>{{ $levelEarnedPoints }}</strong></td>
            </tr>
            @php
                $percentage = $levelPossiblePoints > 0 ? ($levelEarnedPoints / $levelPossiblePoints) * 100 : 'N/D';
            @endphp
            <tr>
                <td colspan="8"><strong>Procent uzyskany na poziomie {{ $currentLevel }}: {{ is_numeric($percentage) ? number_format($percentage, 2).'%' : $percentage }}</strong></td>
            </tr>
        @endif

        <!-- Wyświetl podsumowanie ogólne -->
        <tr><td colspan="8"></td></tr>
        <tr>
            <td colspan="6"><strong>Razem</strong></td>
            <td><strong>{{ $totalPossiblePoints }}</strong></td>
            <td><strong>{{ $totalEarnedPoints }}</strong></td>
        </tr>
        @php
            $totalPercentage = $totalPossiblePoints > 0 ? ($totalEarnedPoints / $totalPossiblePoints) * 100 : 'N/D';
        @endphp
        <tr>
            <td colspan="8"><strong>Ogólny procent uzyskany: {{ is_numeric($totalPercentage) ? number_format($totalPercentage, 2).'%' : $totalPercentage }}</strong></td>
        </tr>
    </tbody>
</table>
