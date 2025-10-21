@extends('layouts.self-assessment')

@section('content')
    <div class="center" style="margin-bottom:12px;">
        <h1 style="color: var(--primary);">Witaj w procesie P-CAP</h1>
        <h2 style="font-size:14px;" class="muted">Welcome to the P-CAP process (People Competences Accelerating Process)</h2>
        
        <div class="welcome-content">
            {!! \App\Models\AppSetting::get('welcome_text') !!}
        </div>
        
        <p>W przypadku pytań lub problemów - odezwij się do {{ \App\Models\AppSetting::get('contact_name', 'Administratora') }} 
           (<a href="mailto:{{ \App\Models\AppSetting::get('contact_email') }}">{{ \App\Models\AppSetting::get('contact_email') }}</a>).
        </p>
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
        @if(session('error'))
            <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                <ul style="margin: 0; list-style: none; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <form action="{{ route('self.assessment.step1.save') }}" method="POST">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="first_name">Imię:</label>
                <input type="text" name="first_name" id="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Nazwisko:</label>
                <input type="text" name="last_name" id="last_name" required>
            </div>
        </div>
        <div class="form-row">
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
        <button type="submit" class="btn btn-primary" style="width:100%">Przejdź dalej</button>
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
