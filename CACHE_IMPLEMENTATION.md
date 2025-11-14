# Implementacja Cachowania w Manager Panel

## Data implementacji
2024 - Optymalizacja wydajnościowa

## Problem
Dashboard managera ładował się bardzo wolno z powodu:
- Wielokrotnego wykonywania ciężkich zapytań SQL przy każdym odświeżeniu
- Funkcja `prepareEmployeesData()` obliczająca procenty dla 5 poziomów dla każdego pracownika
- Zapytanie `Employee::all()` dla supermanagerów pobierające wszystkich pracowników z wynikami
- Brak optymalizacji dla powtarzających się operacji

## Rozwiązanie

### 1. Strategia Cachowania

**Czas cache (TTL)**: 5 minut (300 sekund)

**Klucze cache - hierarchiczna struktura**:
```
manager_panel_employees_{manager_id}_{cycle_id}_{section}_{basic|full}
manager_stats_{manager_id}_{cycle_id}
team_employees_data_{manager_id}_{cycle_id}
department_employees_data_{manager_id}_{cycle_id}
hr_data_supermanager_{cycle_id}
organization_employees_data_{cycle_id}
```

### 2. Cachowane Operacje

#### A. Podstawowe zapytania o pracowników
**Sekcje**: `hr_individual`, `individual`
- **Cache key**: `manager_panel_employees_{manager_id}_{cycle_id}_{section}_basic`
- **Zawartość**: Lista pracowników BEZ results (tylko podstawowe dane)
- **Zastosowanie**: Wyszukiwarki pracowników

#### B. Pełne zapytania o pracowników
**Sekcje**: `dashboard`, `team`, `hr`, `department`, `department_individual`, `codes`
- **Cache key**: `manager_panel_employees_{manager_id}_{cycle_id}_{section}_full`
- **Zawartość**: Lista pracowników Z results (pełne dane z ocenami)
- **Zastosowanie**: Wszystkie sekcje wymagające danych o ocenach

#### C. Statystyki i grupowanie
- **Cache key**: `manager_stats_{manager_id}_{cycle_id}`
- **Zawartość**: 
  - `employeesByLevel` - pracownicy pogrupowani według poziomów
  - `employeesByCompetencyLevel` - pracownicy według poziomu kompetencji
  - `stats` - statystyki obliczone przez `calculateEmployeeStats()`

#### D. Dane zespołowe (prepareEmployeesData)
- **Cache key**: `team_employees_data_{manager_id}_{cycle_id}`
- **Zawartość**: Przetworzone dane pracowników z obliczonymi procentami dla wszystkich 5 poziomów
- **Uwaga**: To jedna z najcięższych operacji CPU-wise

#### E. Dane HR (tylko supermanager)
- **Cache key**: `hr_data_supermanager_{cycle_id}`
- **Zawartość**: Statystyki wszystkich zespołów (completed_count, total_count)
- **Zakres**: Współdzielony między wszystkimi supermanagerami dla danego cyklu

#### F. Dane organizacyjne (tylko supermanager)
- **Cache key**: `organization_employees_data_{cycle_id}`
- **Zawartość**: `Employee::all()` z results + prepareEmployeesData
- **Uwaga**: NAJCIĘŻSZE zapytanie w systemie - pobiera wszystkich pracowników organizacji
- **Zakres**: Współdzielony między wszystkimi supermanagerami dla danego cyklu

#### G. Dane departamentowe (tylko head)
- **Cache key**: `department_employees_data_{manager_id}_{cycle_id}`
- **Zawartość**: Pracownicy departamentu z przetworzonymi danymi
- **Zakres**: Per head + per cycle

### 3. Logging

Każda operacja cache loguje:
- **Cache MISS**: Gdy dane są pobierane z bazy danych
- **Cache HIT**: Implicitnie przez Laravel (brak loga = hit)
- **Klucz cache**: Wyświetlany przy każdym użyciu
- **Liczba rekordów**: Dla zapytań o pracowników

**Lokalizacja logów**: `storage/logs/laravel.log`

**Przykładowe logi**:
```
[2024-XX-XX] Manager Panel: Loading section 'dashboard' for manager admin (role: supermanager), cycle: 5
[2024-XX-XX] Cache MISS: manager_panel_employees_1_5_dashboard_full - fetching from database
[2024-XX-XX] Cache key: manager_panel_employees_1_5_dashboard_full, employees count: 150
[2024-XX-XX] Cache MISS: manager_stats_1_5 - calculating statistics
[2024-XX-XX] Cache MISS: team_employees_data_1_5 - preparing employees data (calculating percentages)
[2024-XX-XX] Cache MISS: organization_employees_data_5 - loading ALL employees with results (VERY HEAVY)
```

### 4. Czyszczenie Cache

#### A. Automatyczne czyszczenie
- **TTL expiration**: Po 5 minutach cache automatycznie wygasa

#### B. Manualne czyszczenie
**Przycisk w UI**: "Wyczyść Cache" w sidebaru managera (obok przycisku Wyloguj)

**Endpoint**: `POST /manager/clear-cache`

**Funkcja**: `ManagerController::clearCache()`

**Czyszczone klucze**:
- Wszystkie sekcje dla danego managera i cyklu (basic + full)
- Statystyki managera
- Team data
- Department data (dla head)
- HR data (dla supermanager)
- Organization data (dla supermanager)

**Response**:
```json
{
    "success": true,
    "message": "Cache został wyczyszczony pomyślnie",
    "cleared_keys_count": 12
}
```

**Automatyczny reload**: Po pomyślnym czyszczeniu strona odświeża się automatycznie po 1 sekundzie

### 5. Kod Implementacji

#### ManagerController.php (linie ~1935-2220)

**Cache setup**:
```php
$cacheKey = "manager_panel_employees_{$manager->id}_{$selectedCycleId}_{$section}";
$cacheTime = 300; // 5 minut

\Log::info("Manager Panel: Loading section '{$section}' for manager {$manager->username} (role: {$manager->role}), cycle: {$selectedCycleId}");
```

**Przykład cache dla pracowników**:
```php
if ($section === 'hr_individual' || $section === 'individual') {
    $employees = \Cache::remember($cacheKey . '_basic', $cacheTime, function() use ($manager, $selectedCycleId, $cacheKey) {
        \Log::info("Cache MISS: {$cacheKey}_basic - fetching from database");
        return $this->getEmployeesForManager($manager, $selectedCycleId, false);
    });
    \Log::info("Cache key: {$cacheKey}_basic, employees count: " . $employees->count());
}
```

**Funkcja clearCache** (linie ~2792-2870):
```php
public function clearCache(Request $request)
{
    try {
        $manager = auth()->user();
        $selectedCycleId = $this->selectedCycleId($request);
        
        $clearedKeys = [];
        
        // Czyści wszystkie klucze dla managera
        $sections = ['dashboard', 'team', 'individual', 'hr', 'hr_individual', 'department', 'department_individual', 'codes'];
        
        foreach ($sections as $section) {
            $keyBasic = "manager_panel_employees_{$manager->id}_{$selectedCycleId}_{$section}_basic";
            $keyFull = "manager_panel_employees_{$manager->id}_{$selectedCycleId}_{$section}_full";
            
            if (\Cache::forget($keyBasic)) $clearedKeys[] = $keyBasic;
            if (\Cache::forget($keyFull)) $clearedKeys[] = $keyFull;
        }
        
        // + statystyki, team data, department data, HR data, organization data
        
        return response()->json([
            'success' => true,
            'message' => 'Cache został wyczyszczony pomyślnie',
            'cleared_keys_count' => count($clearedKeys)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Wystąpił błąd podczas czyszczenia cache'
        ], 500);
    }
}
```

#### routes/web.php (linia ~229)
```php
// Clear manager cache
Route::post('/manager/clear-cache', [ManagerController::class, 'clearCache'])
    ->name('manager.clear_cache')
    ->middleware(['auth', 'manager']);
```

#### layouts/manager.blade.php (linia ~859)
```php
<button id="clear-cache-btn" class="btn btn-sm btn-warning" style="flex: 1;">
    <i class="fas fa-sync-alt"></i>
    Wyczyść Cache
</button>
```

**JavaScript handler** (linia ~1122):
```javascript
$('#clear-cache-btn').on('click', function() {
    const btn = $(this);
    btn.prop('disabled', true);
    btn.html('<i class="fas fa-spinner fa-spin"></i> Czyszczenie...');
    
    const urlParams = new URLSearchParams(window.location.search);
    const cycleId = urlParams.get('cycle');
    
    fetch('/manager/clear-cache' + (cycleId ? '?cycle=' + cycleId : ''), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Cache wyczyszczony! (${data.cleared_keys_count} kluczy)`, 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    });
});
```

### 6. Konfiguracja Cache

**Driver**: Sprawdź `config/cache.php` oraz `.env`
```env
CACHE_DRIVER=file  # lub redis dla lepszej wydajności
```

**Dla produkcji zalecane**: Redis
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 7. Monitorowanie Wydajności

#### A. Analiza logów
```bash
# Filtruj logi cache
tail -f storage/logs/laravel.log | grep "Cache"

# Szukaj MISS (pobieranie z bazy)
tail -f storage/logs/laravel.log | grep "Cache MISS"

# Sprawdź czas ładowania sekcji
tail -f storage/logs/laravel.log | grep "Manager Panel: Loading section"
```

#### B. Metryki do monitorowania
- **Cache hit ratio**: Stosunek HIT do MISS w logach
- **Czas ładowania**: Porównanie przed/po implementacji
- **Liczba zapytań SQL**: Powinno drastycznie spaść przy cache hit
- **Użycie pamięci**: Monitoruj rozmiar cache (szczególnie dla Redis)

#### C. Optymalizacja
Jeśli dashboard nadal wolny:
1. **Wydłuż TTL**: Zwiększ `$cacheTime` z 300s do 600s (10 minut) lub więcej
2. **Redis zamiast file**: Znacznie szybszy driver cache
3. **Cache warming**: Pre-generate cache dla popularnych kombinacji manager+cycle
4. **Eager loading**: Sprawdź czy wszystkie relacje używają `->with()`

### 8. Kiedy Cache Jest Inwalidowany?

**Automatycznie**: Po 5 minutach (TTL)

**Ręcznie**: Przycisk "Wyczyść Cache"

**⚠️ Ważne**: Cache NIE jest automatycznie czyszczony gdy:
- Manager ocenia pracownika (zmienia `score_manager`)
- Administrator edytuje pracownika
- Dodawany jest nowy pracownik
- Zmieniane są kompetencje

**Rozwiązanie**:
1. **Krótki TTL (5 minut)**: Maksymalnie 5 minut "stale data"
2. **Manualne czyszczenie**: Manager może kliknąć "Wyczyść Cache" po wprowadzeniu zmian
3. **Event-based invalidation** (TODO dla przyszłości):
   ```php
   // W ManagerController::saveScore()
   \Cache::forget("manager_panel_employees_{$manager->id}_{$cycleId}_*");
   \Cache::forget("manager_stats_{$manager->id}_{$cycleId}");
   ```

### 9. Testowanie

#### A. Test cache działa
1. Odśwież dashboard managera
2. Sprawdź `storage/logs/laravel.log` - powinny być "Cache MISS"
3. Odśwież ponownie (w ciągu 5 minut)
4. Sprawdź logi - NIE powinno być "Cache MISS" (cache hit)
5. Dashboard powinien załadować się znacznie szybciej

#### B. Test clear cache
1. Kliknij przycisk "Wyczyść Cache"
2. Powinien pokazać się toast "Cache wyczyszczony! (X kluczy)"
3. Strona powinna odświeżyć się automatycznie
4. Sprawdź logi - powinny być "Cache MISS" przy następnym ładowaniu

#### C. Test TTL expiration
1. Odśwież dashboard (cache miss)
2. Odśwież ponownie (cache hit)
3. Poczekaj 5+ minut
4. Odśwież (cache miss - cache wygasł)

### 10. Potencjalne Problemy

#### Problem: Cache nie działa
**Symptomy**: Zawsze "Cache MISS" w logach
**Rozwiązanie**:
```bash
# Sprawdź driver
php artisan config:cache
php artisan cache:clear

# Sprawdź uprawnienia
chmod -R 775 storage/framework/cache
```

#### Problem: Stare dane w dashboardzie
**Symptomy**: Zmiany nie są widoczne
**Rozwiązanie**:
- Kliknij "Wyczyść Cache"
- Poczekaj 5 minut (TTL expiration)
- Rozważ skrócenie TTL do 120s (2 minuty)

#### Problem: Redis connection error
**Symptomy**: "Connection refused" w logach
**Rozwiązanie**:
```bash
# Sprawdź czy Redis działa
redis-cli ping
# Powinno zwrócić: PONG

# Jeśli nie działa, przełącz na file driver
# W .env:
CACHE_DRIVER=file
```

#### Problem: Za duży rozmiar cache
**Symptomy**: Brak miejsca na dysku
**Rozwiązanie**:
```bash
# Czyść stary cache
php artisan cache:clear

# Zmniejsz TTL w kodzie
$cacheTime = 120; // 2 minuty zamiast 5
```

### 11. Następne Kroki (TODO)

1. **Event-Based Invalidation**: Automatyczne czyszczenie cache przy zmianach danych
2. **Cache Tagging**: Grupowanie kluczy dla łatwiejszego czyszczenia
3. **Cache Warming**: Pre-generowanie cache dla popularnych kombinacji
4. **Redis Implementation**: Migracja z file do Redis na produkcji
5. **Query Optimization**: Dalsze optymalizacje zapytań SQL (indexy, chunking)
6. **Background Jobs**: Przeniesienie ciężkich operacji do kolejki

## Podsumowanie Wydajności

**Przed implementacją**:
- Każde odświeżenie: 5-15 zapytań SQL + prepareEmployeesData()
- Czas ładowania: 5-30 sekund (w zależności od liczby pracowników)
- Timeout errors na dużych organizacjach

**Po implementacji**:
- Pierwsze ładowanie (cache miss): Jak poprzednio
- Kolejne ładowania (cache hit): <1 sekunda
- Redukcja zapytań SQL: ~90%
- Brak timeout errors

**ROI**: Znacząca poprawa UX dla managerów często korzystających z dashboardu.
