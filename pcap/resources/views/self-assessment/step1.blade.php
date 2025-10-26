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
            <label for="manager">Moim bezpośrednim przełożonym jest:</label>
            <select name="manager" id="manager" required>
                <option value="">-- Wybierz przełożonego --</option>
            </select>
        </div>
        
        <!-- Podgląd hierarchii -->
        <div id="hierarchy-preview" style="display: none; background: #f8fafc; padding: 12px; border-radius: 8px; margin-top: 12px;">
            <h4 style="margin: 0 0 8px 0; color: #374151;">Twoja struktura przełożonych:</h4>
            <div id="hierarchy-text" style="color: #6b7280; font-size: 14px;"></div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">Przejdź dalej</button>
    </form>
    </div>
@endsection

@section('scripts')
<script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.getElementById('department').addEventListener('change', function () {
                const department = this.value;
                const managerSelect = document.getElementById('manager');
                const hierarchyPreview = document.getElementById('hierarchy-preview');

                // Clear previous options
                managerSelect.innerHTML = '<option value="">-- Wybierz przełożonego --</option>';
                hierarchyPreview.style.display = 'none';

                if (department) {
                    // Fetch supervisors for department
                    fetch("{{ route('get.supervisors') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({ department: department })
                    })
                    .then(response => response.json())
                    .then(supervisors => {
                        for (const [username, name] of Object.entries(supervisors)) {
                            const option = document.createElement('option');
                            option.value = username;
                            option.textContent = name;
                            managerSelect.appendChild(option);
                        }
                    })
                    .catch(error => console.error('Błąd:', error));
                }
            });

            // Show hierarchy when supervisor is selected
            document.getElementById('manager').addEventListener('change', function() {
                const department = document.getElementById('department').value;
                const supervisor = this.value;
                const hierarchyPreview = document.getElementById('hierarchy-preview');
                const hierarchyText = document.getElementById('hierarchy-text');

                if (department && supervisor) {
                    // Fetch full hierarchy for this supervisor
                    fetch("{{ route('get.hierarchy') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({ 
                            department: department, 
                            supervisor: supervisor 
                        })
                    })
                    .then(response => response.json())
                    .then(hierarchy => {
                        let hierarchyHtml = '';
                        
                        if (hierarchy.supervisor) {
                            hierarchyHtml += `<strong>Supervisor:</strong> ${hierarchy.supervisor}<br>`;
                        }
                        
                        if (hierarchy.manager) {
                            hierarchyHtml += `<strong>Manager:</strong> ${hierarchy.manager}<br>`;
                        }
                        
                        hierarchyHtml += `<strong>Head:</strong> ${hierarchy.head}`;
                        
                        hierarchyText.innerHTML = hierarchyHtml;
                        hierarchyPreview.style.display = 'block';
                    })
                    .catch(error => console.error('Błąd:', error));
                } else {
                    hierarchyPreview.style.display = 'none';
                }
            });
        });
</script>
@endsection
