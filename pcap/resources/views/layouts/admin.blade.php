<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora - P-CAP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #2563eb;
            --primary-600: #1d4ed8;
            --primary-700: #1e40af;
            --ring: rgba(37, 99, 235, 0.15);
            --accent: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --border: #e5e7eb;
            --sidebar-width: 280px;
            --header-height: 64px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--card);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .sidebar-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .nav-item {
            margin: 0 16px 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--muted);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover {
            background-color: #f3f4f6;
            color: var(--text);
        }

        .nav-link.active {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 4px 12px var(--ring);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: -16px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--primary-700);
            border-radius: 0 3px 3px 0;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .user-details {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
        }

        .user-role {
            font-size: 12px;
            color: var(--muted);
        }

        .logout-actions {
            display: flex;
            gap: 8px;
        }

        .btn-logout, .btn-manager {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border: 1px solid var(--border);
            background: white;
            color: var(--muted);
            text-decoration: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: #fef2f2;
            border-color: var(--danger);
            color: var(--danger);
        }

        .btn-manager:hover {
            background: #f0f9ff;
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Main content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-header {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 1.5rem 32px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .content-body {
            flex: 1;
            padding: 2rem 32px;
            overflow-x: auto;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
            margin-top: 4px;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb-separator {
            color: var(--border);
        }

        /* Cards and components */
        .card {
            background: var(--card);
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            background: #fafbfc;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin: 0;
        }

        .card-body {
            padding: 24px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            line-height: 1;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            outline: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-600);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--ring);
        }

        .btn-secondary {
            background: white;
            color: var(--muted);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: #f9fafb;
            color: var(--text);
            border-color: var(--muted);
        }

        .btn-success {
            background: var(--accent);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: var(--text);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text);
            background: white;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--ring);
        }

        /* Tables */
        .table-container {
            background: var(--card);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table th {
            background: #fafbfc;
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
            border-bottom: 1px solid var(--border);
        }

        .table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #fafbfc;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Section headers */
        .section-header {
            margin-bottom: 2rem;
        }

        .section-title {
            margin-bottom: 0.5rem;
        }

        .section-description {
            margin-bottom: 0;
            opacity: 0.8;
        }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .stat-icon.primary { background: var(--primary); }
        .stat-icon.success { background: var(--accent); }
        .stat-icon.warning { background: var(--warning); }
        .stat-icon.danger { background: var(--danger); }

        .stat-content h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 4px 0;
        }

        .stat-content p {
            font-size: 14px;
            color: var(--muted);
            margin: 0;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            line-height: 1;
        }

        .badge-primary {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-secondary {
            background: #f3f4f6;
            color: var(--muted);
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-body {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .mobile-menu-toggle {
                display: block;
            }
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text);
            cursor: pointer;
            padding: 8px;
        }

        /* Loading and animations */
        .loading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 14px;
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #f3f4f6;
            border-top: 2px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Utilities */
        .text-muted { color: var(--muted); }
        .text-primary { color: var(--primary); }
        .text-success { color: var(--accent); }
        .text-warning { color: var(--warning); }
        .text-danger { color: var(--danger); }

        .mb-0 { margin-bottom: 0; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }

        .mt-0 { margin-top: 0; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }

        .d-flex { display: flex; }
        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .gap-4 { gap: 16px; }

        .w-full { width: 100%; }
        .text-center { text-align: center; }
    </style>

    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-cog text-primary"></i>
                <h1>Panel Admin</h1>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="#" data-section="employees"
                       class="nav-link section-link {{ (request('section', 'employees') === 'employees' && !request()->routeIs('admin.hierarchy.*')) ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        Pracownicy
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" data-section="managers"
                       class="nav-link section-link {{ request('section') === 'managers' ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        Managerowie
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" data-section="hierarchy"
                       class="nav-link section-link {{ (request('section') === 'hierarchy' || request()->routeIs('admin.hierarchy.*')) ? 'active' : '' }}">
                        <i class="fas fa-sitemap"></i>
                        Hierarchia
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" data-section="dates"
                       class="nav-link section-link {{ request('section') === 'dates' ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        Zarządzanie Datami
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" data-section="competencies"
                       class="nav-link section-link {{ request('section') === 'competencies' ? 'active' : '' }}">
                        <i class="fas fa-brain"></i>
                        Baza pytań
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" data-section="cycles"
                       class="nav-link section-link {{ request('section') === 'cycles' ? 'active' : '' }}">
                        <i class="fas fa-sync-alt"></i>
                        Cykl ocen
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" data-section="settings"
                       class="nav-link section-link {{ request('section') === 'settings' ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        Ustawienia
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                @auth
                <div class="user-info">
                    <div class="user-avatar">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ auth()->user()->name ?? 'Administrator' }}</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
                @endauth
                
                <div class="logout-actions">
                    <a href="{{ url('/manager-panel') }}" class="btn-manager">
                        <i class="fas fa-user-tie"></i>
                        Panel managera
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <button class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Wyloguj
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <div>
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'Panel Administratora')</h1>
                    @hasSection('breadcrumb')
                    <div class="breadcrumb">
                        @yield('breadcrumb')
                    </div>
                    @endif
                </div>
                <div>
                    @yield('header-actions')
                </div>
            </header>

            <div class="content-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !toggle.contains(e.target) && 
                sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });

        // Handle navigation from hierarchy routes
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're on a hierarchy route (not the main admin panel)
            const isHierarchyRoute = window.location.pathname.includes('/admin/hierarchy/');
            
            if (isHierarchyRoute) {
                // Override section-link clicks on hierarchy pages to redirect instead of AJAX
                const sectionLinks = document.querySelectorAll('.section-link');
                sectionLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const section = this.getAttribute('data-section');
                        if (section) {
                            // Redirect to admin panel with section parameter
                            window.location.href = `/admin?section=${section}`;
                        }
                    });
                });
            }
        });
        
        // Loading state for manager panel link
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-manager')) {
                // Create loading overlay
                if (!document.getElementById('manager-loading-overlay')) {
                    const overlay = document.createElement('div');
                    overlay.id = 'manager-loading-overlay';
                    overlay.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 9999;
                        color: white;
                        font-size: 18px;
                    `;
                    overlay.innerHTML = `
                        <div style="text-align: center;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 32px; margin-bottom: 16px;"></i>
                            <div>Ładowanie panelu managera...</div>
                        </div>
                    `;
                    document.body.appendChild(overlay);
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>