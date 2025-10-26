<!-- Section: Baza pytań -->
<div class="section-header">
    <div>
        <h2 class="section-title">Baza pytań</h2>
        <p class="section-description">Zarządzaj bazą kompetencji i pytań do samooceny</p>
    </div>
    <div class="actions">
        <a href="{{ route('upload.excel.template') }}" class="btn btn-outline">
            Pobierz szablon
        </a>
        <a href="{{ route('upload.excel') }}" class="btn btn-primary">
            Importuj Excel
        </a>
    </div>
</div>

<!-- Statystyki -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number">{{ $competencyStats['total'] ?? 0 }}</div>
        <div class="stat-label">Wszystkich kompetencji</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $competencyStats['levels'] ?? 0 }}</div>
        <div class="stat-label">Poziomów</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $competencyStats['types'] ?? 0 }}</div>
        <div class="stat-label">Typów kompetencji</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $competencyStats['with_descriptions'] ?? 0 }}</div>
        <div class="stat-label">Z opisami</div>
    </div>
</div>

<!-- Search -->
<div class="search-container">
    <div class="search-icon">
        <i class="fas fa-search"></i>
    </div>
    <input type="text" id="competency-search" class="form-control search-input" placeholder="Wyszukaj kompetencję po nazwie, typie lub poziomie...">
</div>

<!-- Lista kompetencji -->
<div class="competencies-list" id="competencies-container">
    <div class="loading" id="competencies-loading">
        <i class="fas fa-spinner fa-spin"></i>
        Ładowanie kompetencji...
    </div>
</div>

<!-- Informacje -->
<div class="card">
    <div class="card-body">
        <div class="info-section">
            <h4>Importowanie pytań</h4>
            <p>
                Możesz importować bazę pytań z pliku Excel. Pobierz najpierw szablon, uzupełnij go danymi, 
                a następnie prześlij na serwer. Istniejące pytania zostaną zaktualizowane lub dodane nowe.
            </p>
        </div>
    </div>
</div>

<style>
.competencies-list {
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color, #2563eb);
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary, #6b7280);
    font-weight: 500;
}

.search-container {
    position: relative;
    margin-bottom: 2rem;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary, #6b7280);
    z-index: 2;
}

.search-input {
    padding-left: 40px;
    width: 100%;
    max-width: 400px;
}

.level-group {
    margin-bottom: 3rem;
}

.level-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-color);
}

.competencies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.competency-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    transition: all 0.2s ease;
}

.competency-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-color);
}

.competency-header {
    margin-bottom: 1rem;
}

.competency-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.competency-type {
    font-size: 0.875rem;
    color: var(--text-secondary);
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    display: inline-block;
}

.competency-descriptions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.description-item label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    display: block;
    margin-bottom: 0.25rem;
}

.description-item p {
    font-size: 0.875rem;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.4;
}

.load-more {
    text-align: center;
    padding: 2rem;
    color: var(--text-secondary);
}

.info-section {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.info-section h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
}

.info-section p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--text-secondary);
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    margin-bottom: 1rem;
    color: var(--text-primary);
}

@media (max-width: 768px) {
    .competencies-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.loading {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--text-secondary);
}

.loading i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    let isLoading = false;
    
    // Load initial competencies
    loadCompetencies();
    
    // Setup search
    const searchInput = document.getElementById('competency-search');
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadCompetencies(this.value);
        }, 300);
    });
    
    function loadCompetencies(search = '') {
        if (isLoading) return;
        
        const container = document.getElementById('competencies-container');
        const loading = document.getElementById('competencies-loading');
        
        isLoading = true;
        
        // Show loading
        if (loading) {
            loading.style.display = 'block';
        }
        
        // Clear existing content
        const existingContent = container.querySelector('.level-group, .empty-state, .load-more');
        if (existingContent) {
            container.innerHTML = '<div class="loading" id="competencies-loading"><i class="fas fa-spinner fa-spin"></i> Ładowanie kompetencji...</div>';
        }
        
        fetch(`/admin/competencies/search?search=${encodeURIComponent(search)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.html;
                } else {
                    container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Błąd</h3><p>Wystąpił błąd podczas ładowania kompetencji.</p></div>';
                }
            })
            .catch(error => {
                console.error('Error loading competencies:', error);
                container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Błąd</h3><p>Wystąpił błąd podczas ładowania kompetencji: ' + error.message + '</p></div>';
            })
            .finally(() => {
                isLoading = false;
            });
    }
});
</script>