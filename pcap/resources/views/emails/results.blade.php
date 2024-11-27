<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Twoje wyniki samooceny</title>
</head>
<body>
    <h1>Twoje wyniki samooceny</h1>
    <table>
        <thead>
            <tr>
                <th>Kompetencja</th>
                <th>Ocena</th>
                <th>Powyżej oczekiwań</th>
                <th>Argumentacja</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result->competency->competency_name }}</td>
                    <td>{{ $result->score }}</td>
                    <td>{{ $result->above_expectations ? 'Tak' : 'Nie' }}</td>
                    <td>{{ $result->comments }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
