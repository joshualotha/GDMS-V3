<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --primary:        #1E40AF;
            --primary-hover:  #1D3EA0;
            --primary-light:  #EFF6FF;
            --sidebar-bg:    #0F172A;
            --sidebar-hover: #1E293B;
            --sidebar-active:#1E3A5F;
            --sidebar-text:  #94A3B8;
            --sidebar-active-text:#FFFFFF;
            --sidebar-icon:  #64748B;
            --bg-base:       #F8FAFC;
            --bg-surface:    #FFFFFF;
            --bg-subtle:     #F1F5F9;
            --text-primary:  #0F172A;
            --text-secondary:#475569;
            --text-muted:    #94A3B8;
            --border:       #E2E8F0;
            --border-strong:#CBD5E1;
            --success:      #059669;
            --success-bg:   #ECFDF5;
            --warning:      #D97706;
            --warning-bg:   #FFFBEB;
            --danger:       #DC2626;
            --danger-bg:    #FEF2F2;
            --info:         #0284C7;
            --info-bg:      #F0F9FF;
            --neutral:      #64748B;
            --neutral-bg:   #F8FAFC;
            --shadow-sm:    0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md:   0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.07);
            --shadow-lg:   0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.08);
            --radius-sm:   6px;
            --radius-md:    8px;
            --radius-lg:   12px;
            --radius-xl:    16px;
            --font-sans:    'Inter', system-ui, sans-serif;
            --font-mono:    'JetBrains Mono', monospace;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-sans); background: var(--bg-base); color: var(--text-secondary); font-size: 14px; line-height: 1.6; }
        
        /* Typography */
        h1 { font-size: 22px; font-weight: 600; color: var(--text-primary); letter-spacing: -0.02em; }
        h2 { font-size: 18px; font-weight: 600; color: var(--text-primary); }
        h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); }
        
        /* Layout */
        .app-container { display: flex; min-height: 100vh; overflow: hidden; }
        
        /* Sidebar */
        .sidebar { width: 256px; min-height: 100vh; background: var(--sidebar-bg); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 50; }
        .sidebar-logo { height: 64px; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar-logo-text { font-size: 18px; font-weight: 700; color: #fff; }
        .sidebar-logo-sub { font-size: 11px; color: var(--sidebar-text); }
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 8px 0; }
        .sidebar-group-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--sidebar-icon); padding: 16px 20px 6px; }
        .sidebar-nav-item { height: 38px; padding: 0 12px; margin: 1px 8px; border-radius: var(--radius-md); display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 150ms ease; color: var(--sidebar-text); text-decoration: none; }
        .sidebar-nav-item:hover { background: var(--sidebar-hover); color: #CBD5E1; }
        .sidebar-nav-item.active { background: var(--sidebar-active); color: var(--sidebar-active-text); border-left: 3px solid var(--primary); }
        .sidebar-nav-item-icon { width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; }
        .sidebar-nav-item-text { font-size: 14px; font-weight: 500; }
        
        /* Main Area */
        .main-area { margin-left: 256px; flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        
        /* Topbar */
        .topbar { height: 64px; background: var(--bg-surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 40; }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-title-wrap { display: flex; flex-direction: column; gap: 2px; }
        .topbar-title { font-size: 18px; font-weight: 600; color: var(--text-primary); }
        .topbar-breadcrumb { font-size: 13px; color: var(--text-muted); }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-divider { width: 1px; height: 24px; background: var(--border); }
        .topbar-user { display: flex; align-items: center; gap: 10px; }
        .topbar-user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; }
        .topbar-user-info { display: flex; flex-direction: column; }
        .topbar-user-name { font-size: 13px; font-weight: 500; color: var(--text-primary); }
        .topbar-user-role { font-size: 11px; color: var(--text-muted); }
        .btn-logout { font-size: 13px; color: var(--danger); background: none; border: none; cursor: pointer; }
        .btn-logout:hover { text-decoration: underline; }
        
        /* Page Content */
        .page-content { flex: 1; overflow-y: auto; padding: 24px 32px; }
        
        /* Cards */
        .card { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
        .card-header { padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .card-body { padding: 24px; }
        
        /* Stat Cards */
        .stat-card { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); padding: 20px 24px; }
        .stat-label { font-size: 13px; font-weight: 500; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
        .stat-value { font-size: 28px; font-weight: 700; color: var(--text-primary); font-family: var(--font-mono); letter-spacing: -0.03em; line-height: 1; }
        .stat-sublabel { font-size: 13px; color: var(--text-muted); margin-top: 6px; }
        
        /* Grid */
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        @media (max-width: 1200px) { .grid-4 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .grid-4, .grid-3 { grid-template-columns: 1fr; } }
        
        /* Tables */
        .table-container { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
        .table-filters { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--bg-subtle); }
        th { padding: 11px 16px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; text-align: left; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 13px 16px; font-size: 14px; color: var(--text-secondary); border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:hover td { background: var(--bg-subtle); }
        td:first-child { font-weight: 500; color: var(--text-primary); }
        .text-right { text-align: right; }
        .table-footer { padding: 14px 20px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: var(--bg-subtle); font-size: 13px; color: var(--text-muted); }
        
        /* Forms */
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 14px; font-weight: 600; color: var(--text-primary); }
        .form-label-required { color: var(--danger); margin-left: 2px; }
        .form-input { width: 100%; height: 48px; padding: 0 16px; font-size: 15px; color: var(--text-primary); background: var(--bg-surface); border: 1.5px solid var(--border); border-radius: var(--radius-lg); font-family: var(--font-sans); transition: all 200ms ease; outline: none; }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(30,64,175,0.1); }
        .form-input:disabled { background: var(--bg-subtle); color: var(--text-muted); cursor: not-allowed; }
        .form-input.error { border-color: var(--danger); box-shadow: 0 0 0 4px rgba(220,38,38,0.1); }
        .form-select { width: 100%; height: 48px; padding: 0 40px 0 16px; font-size: 15px; color: var(--text-primary); background: var(--bg-surface); border: 1.5px solid var(--border); border-radius: var(--radius-lg); font-family: var(--font-sans); appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; }
        .form-hint { font-size: 13px; color: var(--text-muted); margin-top: 6px; }
        .form-error { font-size: 13px; color: var(--danger); margin-top: 6px; display: flex; align-items: center; gap: 6px; }
        textarea.form-input { height: auto; min-height: 120px; padding: 14px 16px; resize: vertical; }
        
        /* Buttons */
        .btn { height: 48px; padding: 0 24px; font-size: 15px; font-weight: 600; font-family: var(--font-sans); border-radius: var(--radius-lg); cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 200ms ease; border: none; outline: none; white-space: nowrap; text-decoration: none; }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 2px 4px rgba(30,64,175,0.2); }
        .btn-primary:hover { background: var(--primary-hover); box-shadow: 0 4px 12px rgba(30,64,175,0.3); transform: translateY(-1px); }
        .btn-secondary { background: var(--bg-surface); color: var(--text-primary); border: 1.5px solid var(--border); }
        .btn-secondary:hover { background: var(--bg-subtle); border-color: var(--border-strong); }
        .btn-danger { background: var(--danger-bg); color: var(--danger); border: 1.5px solid #FECACA; }
        .btn-danger:hover { background: #FEE2E2; }
        .btn-ghost { background: transparent; color: var(--text-secondary); }
        .btn-ghost:hover { background: var(--bg-subtle); color: var(--text-primary); }
        .btn-sm { height: 36px; padding: 0 16px; font-size: 13px; }
        .btn-lg { height: 52px; padding: 0 28px; font-size: 16px; }
        
        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: 0.02em; }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-warning { background: var(--warning-bg); color: var(--warning); }
        .badge-danger { background: var(--danger-bg); color: var(--danger); }
        .badge-info { background: var(--info-bg); color: var(--info); }
        .badge-neutral { background: var(--neutral-bg); color: var(--neutral); }
        .badge-primary { background: var(--primary-light); color: var(--primary); }
        
        /* Alerts */
        .alert { padding: 14px 16px; border-radius: var(--radius-md); font-size: 14px; display: flex; align-items: flex-start; gap: 12px; border-left: 4px solid; }
        .alert-success { background: var(--success-bg); border-color: var(--success); color: var(--text-primary); }
        .alert-danger { background: var(--danger-bg); border-color: var(--danger); color: var(--text-primary); }
        .alert-warning { background: var(--warning-bg); border-color: var(--warning); color: #92400E; }
        .alert-info { background: var(--info-bg); border-color: var(--info); color: var(--text-primary); }
        
        /* Page Header */
        .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .page-header-left { display: flex; flex-direction: column; gap: 4px; }
        .page-header-title { font-size: 22px; font-weight: 600; color: var(--text-primary); }
        .page-header-desc { font-size: 14px; color: var(--text-muted); }
        
        /* Empty State */
        .empty-state { padding: 60px 24px; text-align: center; }
        .empty-state-icon { width: 40px; height: 40px; margin: 0 auto 16px; color: var(--text-muted); }
        .empty-state-title { font-size: 15px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
        .empty-state-desc { font-size: 14px; color: var(--text-muted); }
        
        /* Modal */
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); backdrop-filter: blur(2px); z-index: 100; display: none; align-items: center; justify-content: center; }
        .modal-backdrop.open { display: flex; }
        .modal { background: var(--bg-surface); border-radius: var(--radius-xl); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); max-width: 520px; width: calc(100% - 48px); }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .modal-title { font-size: 16px; font-weight: 600; color: var(--text-primary); }
        .modal-close { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-muted); }
        .modal-body { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; }
        
        /* Utility */
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-end { justify-content: flex-end; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .gap-4 { gap: 16px; }
        .gap-6 { gap: 24px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }
        .mr-2 { margin-right: 8px; }
        .mr-3 { margin-right: 12px; }
        .ml-2 { margin-left: 8px; }
        .p-4 { padding: 16px; }
        .p-6 { padding: 24px; }
        .py-2 { padding-top: 8px; padding-bottom: 8px; }
        .py-3 { padding-top: 12px; padding-bottom: 12px; }
        .py-4 { padding-top: 16px; padding-bottom: 16px; }
        .px-4 { padding-left: 16px; padding-right: 16px; }
        .px-6 { padding-left: 24px; padding-right: 24px; }
        .w-full { width: 100%; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        
        /* Toggle Switch */
        .toggle { width: 36px; height: 20px; border-radius: 10px; background: var(--border-strong); position: relative; cursor: pointer; transition: background 150ms; }
        .toggle.on { background: var(--primary); }
        .toggle::after { content: ''; position: absolute; width: 16px; height: 16px; border-radius: 50%; background: #fff; top: 2px; left: 2px; transition: transform 150ms; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
        .toggle.on::after { transform: translateX(16px); }
        
        /* Mobile Menu Toggle */
        .mobile-menu-btn { display: none; width: 40px; height: 40px; align-items: center; justify-content: center; background: none; border: none; cursor: pointer; color: var(--text-primary); border-radius: var(--radius-md); }
        .mobile-menu-btn:hover { background: var(--bg-subtle); }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 45; }
        
        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .sidebar { width: 220px; }
            .main-area { margin-left: 220px; }
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); width: 260px; transition: transform 0.3s ease; }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay { display: block; }
            .sidebar-overlay.open { display: block; }
            .main-area { margin-left: 0; }
            .topbar { padding: 0 16px; }
            .page-content { padding: 16px; }
            .mobile-menu-btn { display: flex; }
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
            .topbar-user-info, .topbar-divider { display: none; }
            .card-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .card-header .btn { width: 100%; }
            table { font-size: 13px; }
            th, td { padding: 10px 12px; }
            .form-input, .form-select { height: 44px; }
            .page-header { flex-direction: column; gap: 16px; }
            .page-header .btn { width: 100%; }
        }
        
        @media (max-width: 480px) {
            .stat-value { font-size: 24px; }
            .topbar-title { font-size: 16px; }
            h1 { font-size: 18px; }
            h2 { font-size: 16px; }
            .alert { padding: 12px; font-size: 13px; }
        }
        
        /* Mobile table scroll */
        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        
        /* Mobile form adjustments */
        @media (max-width: 768px) {
            .form-grid { display: flex; flex-direction: column; }
            .form-grid > div { width: 100% !important; }
            .btn-full-mobile { width: 100%; }
            .modal { max-width: calc(100% - 32px); }
            .modal-body { padding: 16px; }
            .modal-footer { flex-direction: column; }
            .modal-footer .btn { width: 100%; justify-content: center; }
        }
        
        /* Hide on mobile */
        .hide-mobile { }
        @media (max-width: 768px) {
            .hide-mobile { display: none !important; }
        }
        
        /* Show on mobile only */
        .show-mobile { display: none !important; }
        @media (max-width: 768px) {
            .show-mobile { display: flex !important; }
        }
        
        </style>
</head>
<body style="background: var(--bg-base);">
    <div class="app-container">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <div>
                    <div class="sidebar-logo-text">GDMS</div>
                    <div class="sidebar-logo-sub">Gas Distribution</div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <!-- OPERATIONS -->
                <div class="sidebar-group-label" style="cursor: default;">Operations</div>
                <a href="{{ url('dashboard') }}" class="sidebar-nav-item {{ request()->is('dashboard') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Dashboard</span></a>
                <a href="{{ url('sales') }}" class="sidebar-nav-item {{ request()->is('sales*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Sales</span></a>

                <!-- WAREHOUSE -->
                <div class="sidebar-group-label" style="cursor: default;">Warehouse</div>
                <a href="{{ url('warehouse/stock-ledger') }}" class="sidebar-nav-item {{ request()->is('warehouse/stock-ledger*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Stock Ledger</span></a>
                <a href="{{ url('sales/outlet-stock') }}" class="sidebar-nav-item {{ request()->is('sales/outlet-stock*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Stock</span></a>
                <a href="{{ url('warehouse/opening-stock') }}" class="sidebar-nav-item {{ request()->is('warehouse/opening-stock*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Opening Stock</span></a>
                <a href="{{ url('warehouse/movements') }}" class="sidebar-nav-item {{ request()->is('warehouse/movements*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Movements</span></a>
                <a href="{{ url('warehouse/adjustments') }}" class="sidebar-nav-item {{ request()->is('warehouse/adjustments*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Adjustments</span></a>
                <a href="{{ url('warehouse/procurement') }}" class="sidebar-nav-item {{ request()->is('warehouse/procurement*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Procurement</span></a>

                <!-- FUEL -->
                <div class="sidebar-group-label" style="cursor: default;">Fuel</div>
                <a href="{{ url('fuel/purchases') }}" class="sidebar-nav-item {{ request()->is('fuel/purchases*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Purchases</span></a>
                <a href="{{ url('fuel/issues') }}" class="sidebar-nav-item {{ request()->is('fuel/issues*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Issues</span></a>
                <a href="{{ url('fuel/stock') }}" class="sidebar-nav-item {{ request()->is('fuel/stock*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Stock</span></a>

                <!-- ASSETS -->
                <div class="sidebar-group-label" style="cursor: default;">Assets</div>
                <a href="{{ url('assets') }}" class="sidebar-nav-item {{ request()->is('assets*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Register</span></a>
                <a href="{{ url('assets?status=under_maintenance') }}" class="sidebar-nav-item"><span class="sidebar-nav-item-text">Maintenance</span></a>

                <!-- HR -->
                <div class="sidebar-group-label" style="cursor: default;">HR</div>
                <a href="{{ url('hr/employees') }}" class="sidebar-nav-item {{ request()->is('hr/employees*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Employees</span></a>
                <a href="{{ url('payroll') }}" class="sidebar-nav-item {{ request()->is('payroll*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Payroll</span></a>

                <!-- FINANCE -->
                <div class="sidebar-group-label" style="cursor: default;">Finance</div>
                <a href="{{ url('expenses') }}" class="sidebar-nav-item {{ request()->is('expenses*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Expenses</span></a>

                <!-- CONFIG -->
                <div class="sidebar-group-label" style="cursor: default;">Configuration</div>
                <a href="{{ url('settings/cylinder-types') }}" class="sidebar-nav-item {{ request()->is('settings/cylinder-types*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Cylinder Types</span></a>
                <a href="{{ url('settings/outlets') }}" class="sidebar-nav-item {{ request()->is('settings/outlets*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Outlets</span></a>
                <a href="{{ url('settings/suppliers') }}" class="sidebar-nav-item {{ request()->is('settings/suppliers*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Suppliers</span></a>
                <a href="{{ url('settings/general') }}" class="sidebar-nav-item {{ request()->is('settings/general*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Settings</span></a>
                <a href="{{ url('settings/asset-categories') }}" class="sidebar-nav-item {{ request()->is('settings/asset-categories*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Asset Categories</span></a>
                <a href="{{ url('settings/expense-categories') }}" class="sidebar-nav-item {{ request()->is('settings/expense-categories*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Expense Categories</span></a>

                <!-- REPORTS -->
                <div class="sidebar-group-label" style="cursor: default;">Reports</div>
                <a href="{{ url('reports/stock') }}" class="sidebar-nav-item {{ request()->is('reports/stock*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Stock Report</span></a>
                <a href="{{ url('reports/sales') }}" class="sidebar-nav-item {{ request()->is('reports/sales*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Sales Report</span></a>
                <a href="{{ url('reports/procurement') }}" class="sidebar-nav-item {{ request()->is('reports/procurement*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Procurement</span></a>
                <a href="{{ url('reports/cash') }}" class="sidebar-nav-item {{ request()->is('reports/cash*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Cash Reconcile</span></a>
                <a href="{{ url('reports/fuel') }}" class="sidebar-nav-item {{ request()->is('reports/fuel*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Fuel Report</span></a>
                <a href="{{ url('reports/asset') }}" class="sidebar-nav-item {{ request()->is('reports/asset*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Asset Register</span></a>
                <a href="{{ url('reports/depreciation') }}" class="sidebar-nav-item {{ request()->is('reports/depreciation*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Depreciation</span></a>
                <a href="{{ url('reports/payroll') }}" class="sidebar-nav-item {{ request()->is('reports/payroll*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Payroll</span></a>
                <a href="{{ url('reports/profit-loss') }}" class="sidebar-nav-item {{ request()->is('reports/profit-loss*') ? 'active' : '' }}"><span class="sidebar-nav-item-text">Profit & Loss</span></a>
            </nav>
        </aside>

        <!-- Main Area -->
        <div class="main-area">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" onclick="toggleSidebar()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M3 6h18M3 18h18"/>
                        </svg>
                    </button>
                    <div class="topbar-title">@yield('header', 'Dashboard')</div>
                    <div class="topbar-breadcrumb">@yield('breadcrumb', 'Welcome back')</div>
                </div>
                <div class="topbar-right">
                    <div class="topbar-divider"></div>
                    <div class="topbar-user">
                        <div class="topbar-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                        <div class="topbar-user-info">
                            <div class="topbar-user-name">{{ auth()->user()->name }}</div>
                            <div class="topbar-user-role">Administrator</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-logout">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('open');
        }
    </script>
</body>
</html>