@extends('layouts.self-assessment')

@section('content')
    <div class="center" style="margin-bottom:12px;">
        <h1 style="color: var(--primary);">Witaj w procesie P-CAP</h1>
        <h2 style="font-size:14px;" class="muted">Welcome to the P-CAP process (People Competences Accelerating Process)</h2>
        <p><strong>If you are an English speaker and you need an English version of this form, please use your browser translate tool.</strong></p>
        <ul style="text-align:left; margin: 12px auto; max-width: 760px;">
            <li>Poniżej znajdziesz formularz do samooceny - każdy pracownik wykonuje to samodzielnie.</li>
            <li>Zarezerwuj czas: między 20 min a 1 h - w zależności od poziomu rozwoju zawodowego.</li>
            <li>Oceń siebie z perspektywy kompetencji osobistych, społecznych, zawodowych, liderskich do poziomu, na którym jesteś, tj. Junior, Specjalista, Senior, Supervisor, Manager.</li>
            <li>Przy każdej kompetencji możesz zostawić przykład wykorzystania danej kompetencji w pracy (krótko i na faktach).</li>
        </ul>
        <p>Oceń siebie za ten rok pracy (lub krótszy okres, jaki z nami jesteś). Twoja ocena trafi do Twojego lidera/liderki do dalszego etapu procesu P-CAP.</p>
        <p>W przypadku pytań lub problemów - odezwij się do Asi Tonkowicz (<a href="mailto:jto@adsystem.pl">jto@adsystem.pl</a>).</p>
    </div>

    <style>
        /* Scoped spacing for step1 */
        .sa-step1 .form-row{ display:flex; gap:16px; flex-wrap:wrap; margin-bottom:16px; }
        .sa-step1 .form-group{ flex:1 1 260px; margin-bottom:16px; }
        .sa-step1 label{ display:block; margin-bottom:6px; font-weight:600; }
        .sa-step1 select, .sa-step1 input[type="text"]{ width:100%; box-sizing:border-box; padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px; }
        .sa-step1 .btn{ margin-top:20px; }
        @media (max-width: 640px){
            .sa-step1 .form-row{ flex-direction:column; gap:12px; }
        }
    </style>

    <div class="sa-step1">
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
        <button type="submit" class="btn btn-success" style="width:100%">Przejdź dalej</button>
    </form>
    </div>
@endsection

@section('scripts')
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
@endsection
