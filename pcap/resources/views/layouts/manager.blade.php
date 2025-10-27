<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Managera - P-CAP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Select2 for enhanced dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
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

        .manager-layout {
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
            cursor: pointer;
        }

        .nav-link:hover {
            color: var(--primary);
            background-color: rgba(37, 99, 235, 0.05);
        }

        .nav-link.active {
            color: var(--primary);
            background: var(--ring);
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--primary);
            border-radius: 0 2px 2px 0;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-text {
            flex: 1;
        }

        .nav-badge {
            background: var(--primary);
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 600;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-details h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .user-details p {
            font-size: 12px;
            color: var(--muted);
        }

        .logout-btn {
            width: 100%;
            padding: 8px 12px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: var(--danger);
            color: white;
            border-color: var(--danger);
        }

        /* Outline button */
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .btn-outline:hover {
            background: var(--hover);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }

        .header {
            height: var(--header-height);
            background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb-separator {
            color: var(--border);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .cycle-selector {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
        }

        .cycle-selector select {
            border: none;
            background: transparent;
            font-size: 14px;
            color: var(--text);
            cursor: pointer;
        }

        .cycle-status {
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 500;
        }

        .cycle-status.active {
            background: #dcfce7;
            color: #166534;
        }

        .cycle-status.historical {
            background: #fef3c7;
            color: #92400e;
        }

        /* Content Area */
        .content {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }

        /* Cards */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            background: var(--bg);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .card-description {
            color: var(--muted);
            font-size: 14px;
        }

        .card-content {
            padding: 24px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            background: var(--card);
            color: var(--text);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--ring);
        }

        .feedback-textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 13px;
            background: var(--card);
            color: var(--text);
            resize: vertical;
            min-height: 40px;
            transition: all 0.2s ease;
        }

        .feedback-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--ring);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 14px;
        }

        .data-table th {
            background: var(--bg);
            padding: 12px 16px;
            border: 1px solid var(--border);
            font-weight: 600;
            text-align: left;
            color: var(--text);
        }

        .data-table td {
            padding: 12px 16px;
            border: 1px solid var(--border);
            vertical-align: top;
            background: var(--card);
        }

        .data-table tr:hover td {
            background: var(--hover);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-600);
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--border);
        }

        .btn-success {
            background: var(--accent);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 13px;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            font-size: 14px;
            color: var(--muted);
        }

        .loading-spinner i {
            font-size: 24px;
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .header {
                padding: 0 16px;
            }

            .content {
                padding: 16px;
            }
        }

        /* Custom Select2 Styling */
        .select2-container--default .select2-selection--single {
            height: 44px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            padding-left: 0;
            color: var(--text);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
            right: 8px;
        }

        .select2-dropdown {
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--ring);
            color: var(--primary);
        }

        /* Dashboard Styles */
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
        }

        .dashboard-row {
            display: grid;
            gap: 24px;
            margin-bottom: 24px;
        }

        .dashboard-row.three-col {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .dashboard-row.four-col {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .dashboard-row.two-col {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        }

        .dashboard-tile {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .dashboard-tile:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .tile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .tile-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tile-icon {
            width: 32px;
            height: 32px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .tile-content {
            color: var(--muted);
            line-height: 1.6;
        }

        .quick-action-btn {
            display: block;
            width: 100%;
            padding: 16px;
            background: var(--card);
            border: 2px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            text-align: center;
            cursor: pointer;
        }

        .quick-action-btn:hover {
            border-color: var(--primary);
            background: var(--ring);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }

        .quick-action-btn i {
            display: block;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .progress-bar {
            background: var(--border);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 12px;
        }

        .progress-fill {
            height: 100%;
            background: var(--accent);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .level-analysis {
            margin-top: 16px;
        }

        .level-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .level-item:last-child {
            border-bottom: none;
        }

        .level-name {
            font-weight: 500;
            color: var(--text);
        }

        .level-count {
            background: var(--bg);
            color: var(--primary);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .dashboard-row.three-col,
            .dashboard-row.four-col,
            .dashboard-row.two-col {
                grid-template-columns: 1fr;
            }
            
            .dashboard-tile {
                padding: 16px;
            }
            
            .tile-title {
                font-size: 16px;
            }
            
            .stat-number {
                font-size: 24px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="manager-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <div class="nav-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <h1>Panel Managera</h1>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                @php
                    $currentSection = request('section', 'dashboard');
                @endphp
                
                <div class="nav-item">
                    <a href="#" class="nav-link {{ $currentSection === 'dashboard' ? 'active' : '' }}" data-section="dashboard">
                        <div class="nav-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="nav-text">Dashboard</div>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="#" class="nav-link {{ $currentSection === 'individual' ? 'active' : '' }}" data-section="individual">
                        <div class="nav-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="nav-text">Indywidualne oceny</div>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="#" class="nav-link {{ $currentSection === 'team' ? 'active' : '' }}" data-section="team">
                        <div class="nav-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="nav-text">Cały zespół</div>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="#" class="nav-link {{ $currentSection === 'codes' ? 'active' : '' }}" data-section="codes">
                        <div class="nav-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="nav-text">Kody dostępu</div>
                    </a>
                </div>

                @if(isset($manager) && $manager->role == 'supermanager')
                    <div class="nav-item">
                        <a href="#" class="nav-link {{ $currentSection === 'hr_individual' ? 'active' : '' }}" data-section="hr_individual">
                            <div class="nav-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="nav-text">HR - Indywidualne</div>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link {{ $currentSection === 'hr' ? 'active' : '' }}" data-section="hr">
                            <div class="nav-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="nav-text">HR - Organizacja</div>
                        </a>
                    </div>
                @endif

                @if(isset($manager) && $manager->role == 'head')
                    <div class="nav-item">
                        <a href="#" class="nav-link {{ $currentSection === 'department_individual' ? 'active' : '' }}" data-section="department_individual">
                            <div class="nav-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="nav-text">Dział - Indywidualne</div>
                        </a>
                    </div>
                    
                    <div class="nav-item">
                        <a href="#" class="nav-link {{ $currentSection === 'department' ? 'active' : '' }}" data-section="department">
                            <div class="nav-icon">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <div class="nav-text">Dział - Zespół</div>
                        </a>
                    </div>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr($manager->username ?? 'M', 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <h3>{{ $manager->name ?? $manager->username ?? 'Manager' }}</h3>
                        <p>{{ ucfirst($manager->role ?? 'manager') }}</p>
                    </div>
                </div>
                
                <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                    <button onclick="showPasswordModal()" class="btn btn-sm btn-outline" style="flex: 1;" title="Zmień hasło">
                        <i class="fas fa-key"></i>
                        Hasło
                    </button>
                    
                    @if(isset($manager) && $manager->role == 'supermanager')
                        <a href="{{ url('/admin') }}" class="btn btn-sm btn-secondary" style="flex: 1;">
                            <i class="fas fa-cog"></i>
                            Admin
                        </a>
                    @endif
                </div>
                
                <div style="display: flex; gap: 8px;">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="flex: 1;">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Wyloguj
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="breadcrumb">
                    @yield('breadcrumb')
                </div>
                
                <div class="header-actions">
                    @yield('header-actions')
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Loading overlay -->
    <div id="section-loading" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Ładowanie...</span>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Navigation handling
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();
            
            // Handle navigation clicks
            $('.nav-link[data-section]').on('click', function(e) {
                e.preventDefault();
                const section = $(this).data('section');
                showSection(section);
            });
            
            // Don't set initial active section automatically since it's already set by PHP
            // const urlParams = new URLSearchParams(window.location.search);
            // const initialSection = urlParams.get('section') || 'individual';
            // showSection(initialSection);
        });
        
        function showSection(section) {
            // Update navigation only if not already active
            if (!$(`.nav-link[data-section="${section}"]`).hasClass('active')) {
                $('.nav-link').removeClass('active');
                $(`.nav-link[data-section="${section}"]`).addClass('active');
            }
            
            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('section', section);
            
            // Use replace for instant navigation without reload
            window.location.replace(url.toString());
        }
        
        // Utility functions
        function showLoading() {
            $('#section-loading').show();
        }
        
        function hideLoading() {
            $('#section-loading').hide();
        }
        
        // Loading state for buttons
        $(document).on('click', '.loading-btn', function() {
            const btn = $(this);
            const icon = btn.find('.fas:not(.loading-text .fas)');
            const loadingText = btn.find('.loading-text');
            
            icon.hide();
            loadingText.show();
            btn.prop('disabled', true);
        });
        
        // Loading state for admin panel link
        $(document).on('click', 'a[href*="admin"]', function() {
            // Create loading overlay
            if ($('#admin-loading-overlay').length === 0) {
                $('body').append(`
                    <div id="admin-loading-overlay" style="
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
                    ">
                        <div style="text-align: center;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 32px; margin-bottom: 16px;"></i>
                            <div>Ładowanie panelu admina...</div>
                        </div>
                    </div>
                `);
            }
        });
        
        // Password change functions
        function showPasswordModal() {
            document.getElementById('passwordModal').style.display = 'flex';
        }
        
        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            document.getElementById('passwordForm').reset();
            clearPasswordErrors();
        }
        
        function clearPasswordErrors() {
            document.querySelectorAll('.password-error').forEach(el => el.remove());
        }
        
        function submitPasswordChange() {
            clearPasswordErrors();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (!currentPassword || !newPassword || !confirmPassword) {
                showPasswordError('Wszystkie pola są wymagane');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showPasswordError('Nowe hasła się nie zgadzają');
                return;
            }
            
            if (newPassword.length < 6) {
                showPasswordError('Nowe hasło musi mieć co najmniej 6 znaków');
                return;
            }
            
            showLoading();
            
            fetch('/manager/change-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showToast('Hasło zostało zmienione pomyślnie!', 'success');
                    closePasswordModal();
                } else {
                    showPasswordError(data.message || 'Wystąpił błąd podczas zmiany hasła');
                }
            })
            .catch(error => {
                hideLoading();
                showPasswordError('Wystąpił błąd podczas zmiany hasła');
                console.error('Error:', error);
            });
        }
        
        function showPasswordError(message) {
            clearPasswordErrors();
            const errorDiv = document.createElement('div');
            errorDiv.className = 'password-error';
            errorDiv.style.cssText = 'color: var(--danger); font-size: 14px; margin-top: 8px; text-align: center;';
            errorDiv.textContent = message;
            document.querySelector('.password-modal-body').appendChild(errorDiv);
        }
    </script>

    <!-- Password Change Modal -->
    <div id="passwordModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: var(--card); border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); border: 1px solid var(--border); width: 90%; max-width: 400px;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid var(--border); background: var(--bg); border-radius: 12px 12px 0 0;">
                <h3 style="margin: 0; color: var(--primary); font-size: 18px;">
                    <i class="fas fa-key"></i> Zmiana hasła
                </h3>
                <button onclick="closePasswordModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--muted); transition: color 0.2s ease;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="password-modal-body" style="padding: 20px;">
                <form id="passwordForm" onsubmit="event.preventDefault(); submitPasswordChange();">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; color: var(--text); font-weight: 500;">Obecne hasło</label>
                        <input type="password" id="currentPassword" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; background: var(--input); color: var(--text);">
                    </div>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; color: var(--text); font-weight: 500;">Nowe hasło</label>
                        <input type="password" id="newPassword" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; background: var(--input); color: var(--text);">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 6px; color: var(--text); font-weight: 500;">Potwierdź nowe hasło</label>
                        <input type="password" id="confirmPassword" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 6px; background: var(--input); color: var(--text);">
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button type="button" onclick="closePasswordModal()" class="btn btn-secondary" style="flex: 1;">
                            Anuluj
                        </button>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            <i class="fas fa-save"></i> Zmień hasło
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>