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
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Czy na pewno chcesz zresetować hasło dla użytkownika <strong id="reset_user_name"></strong>?
                </div>
                
                <div class="form-group">
                    <label for="new_password" class="form-label">Nowe hasło</label>
                    <input type="password" class="form-control" id="new_password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Potwierdź hasło</label>
                    <input type="password" class="form-control" id="confirm_password" name="password_confirmation" required>
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
    function editManager(managerId) {
        fetch(`/admin/manager/${managerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    // Populate edit form
                    document.getElementById('edit_user_id').value = data.id;
                    document.getElementById('edit_name').value = data.name || '';
                    document.getElementById('edit_username').value = data.username || '';
                    document.getElementById('edit_email').value = data.email || '';
                    document.getElementById('edit_role').value = data.role || '';
                    document.getElementById('edit_department').value = data.department || '';
                    
                    // Open modal
                    openModal('editManagerModal');
                } else {
                    showNotification('Nie udało się pobrać danych managera', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Wystąpił błąd podczas pobierania danych managera', 'error');
            });
    }
    
    function resetPassword(managerId) {
        fetch(`/admin/manager/${managerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    document.getElementById('reset_user_id').value = data.id;
                    document.getElementById('reset_user_name').textContent = data.name;
                    document.getElementById('new_password').value = '';
                    document.getElementById('confirm_password').value = '';
                    
                    openModal('resetPasswordModal');
                } else {
                    showNotification('Nie udało się pobrać danych managera', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Wystąpił błąd podczas pobierania danych managera', 'error');
            });
    }
    
    function deleteManager(managerId) {
        if (confirm('Czy na pewno chcesz usunąć tego managera? Ta operacja jest nieodwracalna.')) {
            fetch(`/admin/manager/${managerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Manager został usunięty', 'success');
                    // Reload the section
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Nie udało się usunąć managera', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Wystąpił błąd podczas usuwania managera', 'error');
            });
        }
    }
    
    // Validate password confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (newPassword && confirmPassword) {
            confirmPassword.addEventListener('input', function() {
                if (this.value && newPassword.value && this.value !== newPassword.value) {
                    this.setCustomValidity('Hasła nie są identyczne');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    });
</script>