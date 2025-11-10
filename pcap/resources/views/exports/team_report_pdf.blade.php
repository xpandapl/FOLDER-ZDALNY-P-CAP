<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Raport Zespołu - {{ $team->name }}</title>
    <style>
        /* Stylizacja do PDF */
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 4px; word-wrap: break-word; }
        th { background-color: #f2f2f2; }
        h2 { font-size: 14px; margin-bottom: 10px; }
        .high-percentage {
            color: green;
            font-weight: bold;
        }
        .col-name { width: 25%; }
        .col-job-title { width: 25%; }
        .col-level { width: 10%; }
        .col-percentage { width: 10%; }
        .col-highest-level { width: 20%; }
    </style>
</head>
<body>
    <h2>Raport Zespołu: {{ $team->name }}</h2>
    <table>
        <thead>
            <tr>
                <th class="col-name">Imię i nazwisko</th>
                <th class="col-job-title">Nazwa stanowiska</th>
                @foreach($levelNames as $levelName)
                    <th class="col-level">{{ $levelName }}</th>
                @endforeach
                <th class="col-highest-level">Poziom</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employeesData as $emp)
                <tr>
                    <td class="col-name">{{ $emp['name'] }}</td>
                    <td class="col-job-title">{{ $emp['job_title'] }}</td>
                    @foreach($levelNames as $levelName)
                        <td class="col-percentage">
                            @if(isset($emp['levelPercentagesManager'][$levelName]) && $emp['levelPercentagesManager'][$levelName] !== null)
                                @php
                                    $percentage = $emp['levelPercentagesManager'][$levelName];
                                    $isHigh = is_numeric($percentage) && $percentage >= 80;
                                @endphp
                                <span class="{{ $isHigh ? 'high-percentage' : '' }}">
                                    {{ is_numeric($percentage) ? number_format((float)$percentage, 2) . '%' : 'N/D' }}
                                </span>
                            @else
                                N/D
                            @endif
                        </td>
                    @endforeach
                    <td class="col-highest-level">{{ $emp['highestLevelManager'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
