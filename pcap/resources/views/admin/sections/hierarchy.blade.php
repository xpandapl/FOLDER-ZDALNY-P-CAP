<!-- Sekcja Hierarchii -->
<div class="section-content">
    <div class="section-header">
        <h2>Zarządzanie Hierarchią</h2>
        <div class="actions">
            <a href="{{ route('admin.hierarchy.create') }}" class="btn btn-primary">Dodaj Strukturę</a>
        </div>
    </div>

    <!-- Statystyki -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $totalStructures ?? 0 }}</div>
            <div class="stat-label">Struktur hierarchii</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $uniqueDepartments ?? 0 }}</div>
            <div class="stat-label">Działów</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ isset($hierarchyStructures) ? $hierarchyStructures->whereNotNull('supervisor_username')->count() : 0 }}</div>
            <div class="stat-label">Z supervisorem</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ isset($hierarchyStructures) ? $hierarchyStructures->whereNotNull('team_name')->count() : 0 }}</div>
            <div class="stat-label">Z zespołami</div>
        </div>
    </div>

    <!-- Filtry -->
    <div class="filters">
        <select id="departmentFilter" class="form-control">
            <option value="">Wszystkie działy</option>
            @if(isset($hierarchyStructures))
                @foreach($hierarchyStructures->unique('department') as $structure)
                    <option value="{{ $structure->department }}">{{ $structure->department }} ({{ $departmentCounts[$structure->department] ?? 0 }})</option>
                @endforeach
            @endif
        </select>
        
        <select id="statusFilter" class="form-control">
            <option value="">Wszystkie statusy</option>
            <option value="complete">Kompletna hierarchia</option>
            <option value="incomplete">Wymaga managera/heada</option>
        </select>
        
        <input type="text" id="searchFilter" class="form-control" placeholder="Szukaj po dziale lub zespole...">
    </div>

    <!-- Lista struktur hierarchii -->
    <div class="hierarchy-list">
        @if(isset($hierarchyStructures) && $hierarchyStructures->count() > 0)
            @foreach($hierarchyStructures as $structure)
            @php
                // Struktura jest kompletna jeśli ma przynajmniej managera lub heada
                $isComplete = $structure->manager_username || $structure->head_username;
                $statusLabel = $isComplete ? 'Kompletna' : 'Wymaga managera/heada';
            @endphp
            <div class="hierarchy-item" 
                 data-department="{{ $structure->department }}" 
                 data-status="{{ $isComplete ? 'complete' : 'incomplete' }}">
                <div class="hierarchy-info">
                    <div class="position-name">{{ $structure->department }}</div>
                    <div class="position-details">
                        @if($structure->team_name)
                            <span class="team">Zespół: {{ $structure->team_name }}</span>
                        @endif
                        <span class="status-badge {{ $isComplete ? 'complete' : 'incomplete' }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
                
                <div class="hierarchy-roles">
                    <div class="role-item">
                        <label>Supervisor:</label>
                        <span>{{ $structure->supervisor ? $structure->supervisor->name : 'Brak' }}</span>
                    </div>
                    <div class="role-item">
                        <label>Manager:</label>
                        <span>{{ $structure->manager ? $structure->manager->name : 'Brak' }}</span>
                    </div>
                    <div class="role-item">
                        <label>Head:</label>
                        <span>{{ $structure->head ? $structure->head->name : 'Brak' }}</span>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="{{ route('admin.hierarchy.edit', $structure->id) }}" class="btn btn-sm btn-outline">Edytuj</a>
                    <button onclick="deleteHierarchy({{ $structure->id }})" class="btn btn-sm btn-danger">Usuń</button>
                </div>
            </div>
            @endforeach
        @else
            <div class="empty-state">
                <p>Brak struktur hierarchii</p>
                <a href="{{ route('admin.hierarchy.create') }}" class="btn btn-primary">Dodaj pierwszą strukturę</a>
            </div>
        @endif
    </div>
</div>

<style>
.filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.filters .form-control {
    flex: 1;
    min-width: 200px;
}

.hierarchy-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.hierarchy-item {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    transition: all 0.2s ease;
    min-height: 200px;
}

.hierarchy-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-color);
}

.hierarchy-info {
    flex: 1;
}

.position-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.position-details {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #666;
    align-items: center;
}

.team {
    background: #e3f2fd;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 500;
    color: #1976d2;
}

.hierarchy-roles {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.role-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.role-item label {
    font-weight: 600;
    color: #495057;
    margin: 0;
    font-size: 0.875rem;
}

.role-item span {
    color: #212529;
    font-size: 0.875rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-badge.complete {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-badge.incomplete {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
    margin-top: auto;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.empty-state p {
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .hierarchy-list {
        grid-template-columns: 1fr;
    }
    
    .hierarchy-item {
        min-height: auto;
    }
    
    .actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchFilter = document.getElementById('searchFilter');
    
    function filterHierarchy() {
        const department = departmentFilter.value;
        const status = statusFilter.value;
        const search = searchFilter.value.toLowerCase();
        
        document.querySelectorAll('.hierarchy-item').forEach(item => {
            const itemDepartment = item.dataset.department;
            const itemStatus = item.dataset.status;
            const itemText = item.textContent.toLowerCase();
            
            const matchesDepartment = !department || itemDepartment === department;
            const matchesStatus = !status || itemStatus === status;
            const matchesSearch = !search || itemText.includes(search);
            
            if (matchesDepartment && matchesStatus && matchesSearch) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    departmentFilter.addEventListener('change', filterHierarchy);
    statusFilter.addEventListener('change', filterHierarchy);
    searchFilter.addEventListener('input', filterHierarchy);
});

function deleteHierarchy(id) {
    if (confirm('Czy na pewno chcesz usunąć tę strukturę hierarchii?')) {
        fetch(`/admin/hierarchy/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Błąd podczas usuwania struktury');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Błąd podczas usuwania struktury');
        });
    }
}
</script>