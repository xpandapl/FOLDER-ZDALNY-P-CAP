<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- Meta i tytuł -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krok 1: Formularz samooceny P-CAP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Zaktualizowane style CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px; /* Dodano padding */
        }

        .container-wrapper {
            display: flex;
            justify-content: center;
            /* Usunięto align-items: center; */
            width: 100%;
            /* Usunięto height: 100vh; */
        }

        .container {
            background-color: white;
            padding: 30px;
            margin-top: 20px; /* Dodano marginesy */
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        h1, h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 18px;
            color: dodgerblue;
            margin-bottom:2px;
        }

        h2 {
            font-size: 10px;
        }
        li {
            font-size: 14px; /* Możesz zwiększyć, np. 18px dla lepszej czytelności */
            color: #333; /* Zmiana koloru na ciemniejszy dla lepszego kontrastu */
            text-align: left; /* Zmiana z justify na left */
            font-weight: 400; /* Lżejszy font dla lepszej czytelności */
            line-height: 1.6; /* Zwiększenie odstępu między liniami */
        }
        p {
            font-size: 14px; /* Możesz zwiększyć, np. 18px dla lepszej czytelności */
            color: #333; /* Zmiana koloru na ciemniejszy dla lepszego kontrastu */
            line-height: 1.8; /* Zwiększenie odstępu między liniami */
            margin-bottom: 20px;
            text-align: left; /* Zmiana z justify na left */
            font-weight: 400; /* Lżejszy font dla lepszej czytelności */
        }


        .form-group {
            margin-bottom: 20px;
            flex: 1; /* Każde pole zajmuje równą szerokość */
        }

        .form-row {
            display: flex;
            gap: 20px; /* Odstęp między polami */
        }


        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
            display: block;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 90%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.2s;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            h2 {
                font-size: 18px;
            }

            p {
                font-size: 14px;
            }

            button[type="submit"] {
                font-size: 14px;
            }
        }
    </style>
    <script>
        // Your existing JavaScript code
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.getElementById('department').addEventListener('change', function () {
                const department = this.value;
                const managerSelect = document.getElementById('manager');

                // Clear previous options
                managerSelect.innerHTML = '<option value="">-- Wybierz przełożonego --</option>';

                if (department) {
                    // Make AJAX request to fetch managers
                    fetch("{{ route('get.managers') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ department: department })
                    })

                    .then(response => response.json())
                    .then(managers => {
                        // `managers` is an object with key-value pairs
                        for (const [id, name] of Object.entries(managers)) {
                            const option = document.createElement('option');
                            option.value = name; // Use name as value if you store manager's name
                            option.textContent = name;
                            managerSelect.appendChild(option);
                        }
                    })
                    .catch(error => console.error('Błąd:', error));
                }
            });
        });

    </script>

</head>
<body>
    <div class="container-wrapper">
        <div class="container">
            <h1><strong>Witaj w procesie P-CAP - Proces Rozwoju Kompetencji Ludzkich</strong></h1>
            <h2>Welcome to the P-CAP process (eng. People Competences Accelerating Process)</h2>
            <p><strong>If you are an English speaker and You need an English version of this form, pls open this form via Chrome browser and use Chrome Translator Tool.</strong></p>
            <ul>
                <li>Poniżej znajdziesz formularz do samooceny - każdy pracownik wykonuje to samodzielnie.</li>
                <li>Zarezerwuj czas: między 20 min a 1 h - w zależności od poziomu rozwoju zawodowego.</li>
                <li>Oceń siebie z perspektywy kompetencji osobistych, społecznych, zawodowych, liderskich do poziomu, na którym jesteś, tj. Junior, Specjalista, Senior, Supervisor, Manager.
                </li>
                <li>Przy każdej kompetencji możesz zostawić przykład wykorzystania danej kompetencji w pracy, krótko i na faktach, do czego zachęcamy.</li>
            </ul>
            <p>
                Oceń siebie za ten rok pracy (lub krótszy okres, jaki z nami jesteś). 
                Oceń swoje kompetencje subiektywnie i samodzielnie. 
                Twoja uzupełniona ocena trafi do Twojego lidera/liderki do dalszego etapu procesu P-CAP.
                Szczegóły procesu masz opisane w adVersum.
            </p>
            <p>
                W przypadku pytań lub problemów - odezwij się do Asi Tonkowicz (<a href="mailto:jto@adsystem.pl">jto@adsystem.pl</a>)
            </p>
            <!-- Formularz -->
            <form action="{{ route('self.assessment.step1.save') }}" method="POST">
                @csrf
                <div class="form-row">
                <div class="form-group">
                    <label for="name">Imię i nazwisko:</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="job_title">Nazwa stanowiska:</label>
                    <input type="text" name="job_title" id="job_title" required>
                </div>
                </div>
                <div class="form-group">
                    <label for="department">Wybierz dział:</label>
                    <select name="department" id="department" required>
                        <option value="">-- Wybierz dział --</option>
                        <option value="Growth">Growth</option>
                        <option value="Logistyka">Logistyka</option>
                        <option value="Production">Produkcja</option>
                        <option value="Sales">Sprzedaż</option>
                        <option value="Order Care">Order Care</option>
                        <option value="People & Culture">People & Culture</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="manager">Moim przełożonym jest:</label>
                    <select name="manager" id="manager" required>
                        <option value="">-- Wybierz przełożonego --</option>
                    </select>
                </div>
                <button type="submit">Przejdź dalej</button>
            </form>
        </div>
    </div>
</body>
</html>
