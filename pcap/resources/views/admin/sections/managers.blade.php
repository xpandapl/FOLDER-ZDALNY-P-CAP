<!-- Section: Managerowie -->
<div class="section-header d-flex justify-content-between align-items-center">
    <div>
        <h2 class="section-title">Zarządzanie Managerami</h2>
        <p class="section-description">Dodawaj i zarządzaj kontami użytkowników z uprawnieniami managerskimi</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addManagerModal')">
        <i class="fas fa-plus"></i>
        Dodaj managera
    </button>
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-users-cog"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $users->count() }}</h3>
            <p>Łącznie managerów</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $users->where('role', 'supermanager')->count() }}</h3>
            <p>Supermanagerów</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-crown"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $users->where('role', 'head')->count() }}</h3>
            <p>Head'ów</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $users->where('role', 'manager')->count() }}</h3>
            <p>Managerów</p>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Użytkownik</th>
                <th>Login</th>
                <th>Rola</th>
                <th>Data utworzenia</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-role">{{ $user->email ?? 'Brak email' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <code>{{ $user->username }}</code>
                </td>
                <td>
                    @switch($user->role)
                        @case('supermanager')
                            <span class="badge badge-danger">Supermanager</span>
                            @break
                        @case('head')
                            <span class="badge badge-warning">Head</span>
                            @break
                        @case('manager')
                            <span class="badge badge-primary">Manager</span>
                            @break
                        @case('supervisor')
                            <span class="badge badge-success">Supervisor</span>
                            @break
                        @default
                            <span class="badge badge-secondary">{{ ucfirst($user->role) }}</span>
                    @endswitch
                </td>
                <td>
                    <div style="font-size: 12px;">
                        {{ $user->created_at ? $user->created_at->format('d.m.Y') : 'N/A' }}
                        @if($user->created_at)
                            <div class="text-muted">{{ $user->created_at->format('H:i') }}</div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-outline btn-warning btn-icon" 
                                onclick="editManager({{ $user->id }})" 
                                title="Edytuj managera">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline btn-info btn-icon" 
                                onclick="resetPassword({{ $user->id }})" 
                                title="Resetuj hasło">
                            <i class="fas fa-key"></i>
                        </button>
                        @if($user->id !== auth()->id())
                            <button class="btn btn-outline btn-danger btn-icon" 
                                    onclick="deleteManager({{ $user->id }})" 
                                    title="Usuń managera">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Manager Modal -->
<div id="addManagerModal" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Dodaj Managera</h3>
            <button class="modal-close" onclick="closeModal('addManagerModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.add_manager') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="form-label">Imię i Nazwisko</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="username" class="form-label">Login</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Hasło</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="role" class="form-label">Rola</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">Wybierz rolę</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="department" class="form-label">Dział</label>
                    <select class="form-control" id="department" name="department" required>
                        <option value="">Wybierz dział</option>
                        @foreach($teams as $team)
                            <option value="{{ $team }}">{{ $team }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addManagerModal')">Anuluj</button>
                <button type="submit" class="btn btn-primary">Dodaj managera</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Manager Modal -->
<div id="editManagerModal" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edytuj Managera</h3>
            <button class="modal-close" onclick="closeModal('editManagerModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.update_manager') }}">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_user_id" name="user_id">
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_name" class="form-label">Imię i Nazwisko</label>
                    <input type="text" class="form-control" id="edit_name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_username" class="form-label">Login</label>
                    <input type="text" class="form-control" id="edit_username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="edit_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_role" class="form-label">Rola</label>
                    <select class="form-control" id="edit_role" name="role" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_department" class="form-label">Dział</label>
                    <select class="form-control" id="edit_department" name="department" required>
                        @foreach($teams as $team)
                            <option value="{{ $team }}">{{ $team }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editManagerModal')">Anuluj</button>
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
            </div>
        </form>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Resetuj Hasło</h3>
            <button class="modal-close" onclick="closeModal('resetPasswordModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.reset_password') }}">
            @csrf
            <input type="hidden" id="reset_user_id" name="user_id">
            
            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Czy na pewno chcesz zresetować hasło dla użytkownika <strong id="reset_user_name"></strong>?
                </div>
                
                <div class="form-group">
                    <label for="new_password" class="form-label">
                        Nowe hasło
                        <small class="text-muted">(minimum 8 znaków)</small>
                    </label>
                    <input type="password" class="form-control" id="new_password" name="password" minlength="8" required>
                    <small class="form-text text-muted">Hasło musi zawierać co najmniej 8 znaków</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Potwierdź hasło</label>
                    <input type="password" class="form-control" id="confirm_password" name="password_confirmation" minlength="8" required>
                    <small class="form-text text-muted" id="password-match-message"></small>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('resetPasswordModal')">Anuluj</button>
                <button type="submit" class="btn btn-warning">Resetuj hasło</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Functions are now defined globally in admin_panel_new.blade.php
    
    // Validate password confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        const matchMessage = document.getElementById('password-match-message');
        const submitButton = document.querySelector('#resetPasswordModal button[type="submit"]');
        
        function validatePasswords() {
            if (!newPassword || !confirmPassword || !matchMessage) return;
            
            const newPwd = newPassword.value;
            const confirmPwd = confirmPassword.value;
            
            // Check minimum length
            if (newPwd.length > 0 && newPwd.length < 8) {
                matchMessage.textContent = 'Hasło musi mieć co najmniej 8 znaków';
                matchMessage.style.color = 'red';
                if (submitButton) submitButton.disabled = true;
                return;
            }
            
            // Check if passwords match
            if (confirmPwd.length > 0) {
                if (newPwd === confirmPwd) {
                    matchMessage.textContent = '✓ Hasła są identyczne';
                    matchMessage.style.color = 'green';
                    confirmPassword.setCustomValidity('');
                    if (submitButton) submitButton.disabled = false;
                } else {
                    matchMessage.textContent = '✗ Hasła nie są identyczne';
                    matchMessage.style.color = 'red';
                    confirmPassword.setCustomValidity('Hasła nie są identyczne');
                    if (submitButton) submitButton.disabled = true;
                }
            } else {
                matchMessage.textContent = '';
                confirmPassword.setCustomValidity('');
                if (submitButton) submitButton.disabled = false;
            }
        }
        
        if (newPassword && confirmPassword) {
            newPassword.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
            
            // Clear validation when modal closes
            const resetModal = document.getElementById('resetPasswordModal');
            if (resetModal) {
                const closeButtons = resetModal.querySelectorAll('[onclick*="closeModal"]');
                closeButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (newPassword) newPassword.value = '';
                        if (confirmPassword) confirmPassword.value = '';
                        if (matchMessage) matchMessage.textContent = '';
                        if (submitButton) submitButton.disabled = false;
                    });
                });
            }
        }
    });
</script>