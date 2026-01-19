<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Librarian Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- <!-- jQuery (optional for other scripts, not needed for Bootstrap 5) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

    <style>
        :root {
            --primary-dark: #1a252f;
            --secondary-dark: #2c3e50;
            --accent-blue: #3498db;
            --accent-hover: #2980b9;
            --sidebar-width: 280px;
            --topbar-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow-x: hidden;
            background: #f5f7fa;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1a1f2e 0%, #2c3e50 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1040;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Sidebar Header */
        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(10px);
        }

        .sidebar-brand {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: transform 0.3s ease;
        }

        .sidebar-brand:hover {
            transform: translateX(5px);
        }

        .sidebar-brand i {
            font-size: 1.8rem;
            color: var(--accent-blue);
            text-shadow: 0 0 20px rgba(52, 152, 219, 0.5);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-title {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .brand-subtitle {
            font-size: 0.75rem;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* User Info */
        .user-info {
            padding: 20px;
            background: rgba(0,0,0,0.15);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            position: sticky;
            top: 92px;
            z-index: 10;
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            position: relative;
        }

        .user-avatar::after {
            content: '';
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            background: #2ecc71;
            border: 2px solid #2c3e50;
            border-radius: 50%;
        }

        .user-details {
            flex: 1;
        }

        .user-details h6 {
            margin: 0;
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .user-details small {
            color: #95a5a6;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .user-status {
            width: 8px;
            height: 8px;
            background: #2ecc71;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Navigation */
        .sidebar .nav {
            padding: 15px 0 80px 0;
        }

        .nav-section-title {
            color: #95a5a6;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 25px 20px 10px 20px;
            margin-top: 10px;
        }

        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 13px 20px;
            margin: 3px 12px;
            border-radius: 10px;
            border-left: 3px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, rgba(52, 152, 219, 0.2), transparent);
            transition: width 0.3s ease;
        }

        .sidebar .nav-link:hover::before {
            width: 100%;
        }

        .sidebar .nav-link:hover {
            background: rgba(52, 152, 219, 0.15);
            color: var(--accent-blue);
            border-left-color: var(--accent-blue);
            transform: translateX(8px);
            padding-left: 25px;
        }

        .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.25);
            color: #fff;
            border-left-color: var(--accent-blue);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .sidebar .nav-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.15rem;
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link:hover i {
            transform: scale(1.15);
        }

        .sidebar .nav-link .bi-chevron-down {
            margin-left: auto;
            margin-right: 0;
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }

        /* Submenu */
        .submenu {
            padding-left: 15px;
            margin-top: 5px;
        }

        .submenu .nav-link {
            font-size: 0.88rem;
            padding: 10px 20px;
            margin: 2px 12px;
        }

        .submenu .nav-link i {
            font-size: 1rem;
        }

        /* Logout Link */
        .nav-link.text-danger:hover {
            background: rgba(231, 76, 60, 0.15) !important;
            color: #e74c3c !important;
            border-left-color: #e74c3c !important;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f5f7fa;
            transition: all 0.3s ease;
        }

        /* Top Navigation - Sticky */
        .top-navbar {
            background: #fff;
            padding: 0 30px;
            height: var(--topbar-height);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .search-bar {
            position: relative;
            flex: 1;
            max-width: 500px;
        }

        .search-bar input {
            border-radius: 25px;
            padding: 12px 20px 12px 45px;
            border: 2px solid #e8ecef;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            width: 100%;
        }

        .search-bar input:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
            outline: none;
        }

        .search-bar i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
            font-size: 1.1rem;
        }

        .notification-badge {
            position: relative;
        }

        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 4px 7px;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(231, 76, 60, 0.4);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .topbar-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .topbar-btn:hover {
            background: #e8ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .topbar-btn i {
            font-size: 1.2rem;
            color: #5a6c7d;
        }

        /* User Dropdown */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 15px;
            border-radius: 25px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .user-dropdown:hover {
            background: #e8ecef;
            border-color: var(--accent-blue);
        }

        .user-dropdown-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .user-dropdown-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .user-dropdown-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #2c3e50;
        }

        .user-dropdown-role {
            font-size: 0.75rem;
            color: #95a5a6;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            border-radius: 12px;
            padding: 8px;
            min-width: 220px;
            margin-top: 10px !important;
        }

        .dropdown-item {
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .dropdown-item i {
            font-size: 1.1rem;
            width: 20px;
        }

        /* Content Wrapper */
        .content-wrapper {
            padding: 30px;
            animation: fadeIn 0.5s ease;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 12px;
            border-left: 4px solid;
            padding: 15px 20px;
            backdrop-filter: blur(10px);
            animation: slideInDown 0.5s ease;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            border-left-color: #2ecc71;
            color: #27ae60;
        }

        .alert-danger {
            background: rgba(231, 76, 60, 0.1);
            border-left-color: #e74c3c;
            color: #c0392b;
        }

        .alert i {
            font-size: 1.2rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.6);
                z-index: 1035;
                backdrop-filter: blur(4px);
            }

            .mobile-overlay.show {
                display: block;
                animation: fadeIn 0.3s ease;
            }

            .search-bar {
                display: none;
            }

            .top-navbar {
                padding: 0 15px;
            }

            .content-wrapper {
                padding: 20px 15px;
            }
        }

        @media (max-width: 576px) {
            .user-dropdown-info {
                display: none;
            }
        }

        /* Loading Animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar for Main Content */
        .content-wrapper::-webkit-scrollbar {
            width: 8px;
        }

        .content-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .content-wrapper::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .content-wrapper::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Add this to your existing styles, around line 340 (after the .badge animation) */

    /* Stabilize badges - prevent scaling on active/hover states */
    .sidebar .nav-link .badge {
    min-width: 18px;
    height: 18px;
    font-size: 0.7rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transform: scale(1) !important;
    transition: none !important;
    flex-shrink: 0;
    padding: 2px 5px;
}
    /* Ensure badges don't scale when parent is hovered or active */
    .sidebar .nav-link:hover .badge,
    .sidebar .nav-link.active .badge,
    .sidebar .nav-link[aria-expanded="true"] .badge {
        transform: scale(1) !important;
    }

    /* Stabilize the entire nav-link right section */
    .sidebar .nav-link > .d-flex.align-items-center:last-child {
        flex-shrink: 0;
        min-width: fit-content;
    }
    </style>
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('librarian.dashboard') }}" class="sidebar-brand">
                <i class="bi bi-book-half"></i>
                <div class="brand-text">
                    <span class="brand-title">Digital</span>
                    <span class="brand-subtitle">Library</span>
                </div>
            </a>
        </div>

        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="user-details">
                <h6>{{ Auth::user()->name }}</h6>
                <small><span class="user-status"></span> Online</small>
            </div>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link {{ Request::is('librarian/dashboard') ? 'active' : '' }}" 
            href="{{ route('librarian.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

           <div class="nav-section-title">Library Management</div>

            <!-- Books Dropdown -->
            <a class="nav-link d-flex justify-content-between align-items-center {{ Request::is('librarian/dashboard/books*') ? 'active' : '' }}" 
            data-bs-toggle="collapse" href="#booksMenu" 
            aria-expanded="{{ Request::is('librarian/dashboard/books*') ? 'true' : 'false' }}">
                <div class="d-flex align-items-center">
                    <i class="bi bi-book"></i>
                    <span class="ms-2">Books</span>
                </div>
                <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse submenu {{ Request::is('librarian/dashboard/books*') ? 'show' : '' }}" id="booksMenu">
                <a class="nav-link d-flex align-items-center {{ Request::is('librarian/dashboard/books') && !Request::is('librarian/dashboard/books/create') ? 'active' : '' }}" 
                href="{{ route('librarian.books.index') }}">
                    <i class="bi bi-list-ul"></i>
                    <span class="ms-2 flex-grow-1">View All Books</span>
                </a>
                <a class="nav-link d-flex align-items-center {{ Request::is('librarian/dashboard/books/create') ? 'active' : '' }}" 
                href="{{ route('librarian.books.create') }}">
                    <i class="bi bi-plus-circle"></i>
                    <span class="ms-2 flex-grow-1">Add New Book</span>
                </a>
            </div>

            <!-- Checkouts Dropdown -->
            <a class="nav-link d-flex justify-content-between align-items-center {{ Request::is('librarian/dashboard/checkouts*') ? 'active' : '' }}" 
                data-bs-toggle="collapse" href="#checkoutsMenu" 
                aria-expanded="{{ Request::is('librarian/dashboard/checkouts*') ? 'true' : 'false' }}"
                role="button">
                <div class="d-flex align-items-center">
                    <i class="bi bi-arrow-left-right"></i>
                    <span class="ms-2">Checkouts</span>
                </div>
                <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse submenu {{ Request::is('librarian/dashboard/checkouts*') ? 'show' : '' }}" id="checkoutsMenu">
                <a class="nav-link d-flex align-items-center {{ Request::routeIs('librarian.checkouts.index') ? 'active' : '' }}" 
                    href="{{ route('librarian.checkouts.index') }}">
                    <i class="bi bi-list-ul"></i>
                    <span class="ms-2 flex-grow-1">All Checkouts</span>
                    @php
                        use App\Models\Checkout;
                    @endphp
                    <span class="badge bg-secondary ms-1">{{ Checkout::count() }}</span>
                </a>
                <a class="nav-link d-flex align-items-center {{ Request::routeIs('librarian.checkouts.create') ? 'active' : '' }}" 
                    href="{{ route('librarian.checkouts.create') }}">
                    <i class="bi bi-plus-circle"></i>
                    <span class="ms-2 flex-grow-1">Create Checkout</span>
                </a>
                <a class="nav-link d-flex align-items-center {{ Request::routeIs('librarian.checkouts.pending') ? 'active' : '' }}" 
                    href="{{ route('librarian.checkouts.pending') }}">
                    <i class="bi bi-clock text-warning"></i>
                    <span class="ms-2 flex-grow-1">Pending Requests</span>
                    @php
                        $pendingCount = Checkout::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a class="nav-link d-flex align-items-center {{ Request::routeIs('librarian.checkouts.pending-extensions') ? 'active' : '' }}" 
                    href="{{ route('librarian.checkouts.pending-extensions') }}">
                    <i class="bi bi-calendar-plus text-info"></i>
                    <span class="ms-2 flex-grow-1">Extension Requests</span>
                    @php
                        $pendingExtensionsCount = Checkout::where('extension_requested', true)
                            ->where('status', 'checked_out')
                            ->count();
                    @endphp
                    @if($pendingExtensionsCount > 0)
                        <span class="badge bg-danger ms-1">{{ $pendingExtensionsCount }}</span>
                    @endif
                </a>
            </div>

            <!-- Reservations Dropdown - WITH STATUS COUNTS -->
            <a class="nav-link d-flex justify-content-between align-items-center {{ Request::is('librarian/dashboard/reservations*') ? 'active' : '' }}" 
            data-bs-toggle="collapse" href="#reservationsMenu" 
            aria-expanded="{{ Request::is('librarian/dashboard/reservations*') ? 'true' : 'false' }}"
            role="button">
                <div class="d-flex align-items-center">
                    <i class="bi bi-bookmark"></i>
                    <span class="ms-2">Reservations</span>
                </div>
                <div class="d-flex align-items-center">
                    @php
                    use App\Models\Reservation;
                    $waitingReservationsCount = Reservation::where('status', 'waiting')->count();
                    $readyReservationsCount = Reservation::where('status', 'ready')->count();
                    $totalReservationAlerts = $waitingReservationsCount; // This shows waiting count as alerts
                    @endphp
                    @if($totalReservationAlerts > 0)
                        <span class="badge bg-warning me-2">{{ $totalReservationAlerts }}</span>
                    @endif
                    <i class="bi bi-chevron-down transition-transform"></i>
                </div>
            </a>
            <div class="collapse submenu {{ Request::is('librarian/dashboard/reservations*') ? 'show' : '' }}" id="reservationsMenu">
                <a class="nav-link d-flex align-items-center {{ Request::routeIs('librarian.reservations.index') && !request('status') ? 'active' : '' }}" 
                href="{{ route('librarian.reservations.index') }}">
                    <i class="bi bi-list-ul"></i>
                    <span class="ms-2 flex-grow-1">All Reservations</span>
                    <span class="badge bg-secondary ms-1">{{ Reservation::count() }}</span>
                </a>
                <a class="nav-link d-flex align-items-center {{ Request::is('librarian/dashboard/reservations*') && request('status') == 'waiting' ? 'active' : '' }}" 
                href="{{ route('librarian.reservations.index', ['status' => 'waiting']) }}">
                    <i class="bi bi-clock text-warning"></i>
                    <span class="ms-2 flex-grow-1">Waiting</span>
                    @if($waitingReservationsCount > 0)
                        <span class="badge bg-warning ms-1">{{ $waitingReservationsCount }}</span>
                    @endif
                </a>
                <a class="nav-link d-flex align-items-center {{ Request::is('librarian/dashboard/reservations*') && request('status') == 'ready' ? 'active' : '' }}" 
                href="{{ route('librarian.reservations.index', ['status' => 'ready']) }}">
                    <i class="bi bi-check-circle text-success"></i>
                    <span class="ms-2 flex-grow-1">Ready for Pickup</span>
                    @if($readyReservationsCount > 0)
                        <span class="badge bg-success ms-1">{{ $readyReservationsCount }}</span>
                    @endif
                </a>
                <a class="nav-link d-flex align-items-center {{ Request::is('librarian/dashboard/reservations*') && request('status') == 'expiring' ? 'active' : '' }}" 
                href="{{ route('librarian.reservations.index', ['status' => 'expiring']) }}">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    <span class="ms-2 flex-grow-1">Expiring Soon</span>
                    @php
                        // Get ready reservations expiring within 2 days
                        $expiringSoonCount = Reservation::where('status', 'ready')
                            ->where('expiry_date', '<=', now()->addDays(2))
                            ->where('expiry_date', '>=', now())
                            ->count();
                    @endphp
                    @if($expiringSoonCount > 0)
                        <span class="badge bg-danger ms-1">{{ $expiringSoonCount }}</span>
                    @endif
                </a>
            </div>

            <!-- Fines Dropdown - WITH STATUS COUNTS -->
            <a class="nav-link d-flex justify-content-between align-items-center {{ Request::is('librarian/dashboard/fines*') ? 'active' : '' }}" 
                data-bs-toggle="collapse" href="#finesMenu" 
                aria-expanded="{{ Request::is('librarian/dashboard/fines*') ? 'true' : 'false' }}"
                role="button">
                <div class="d-flex align-items-center">
                    <i class="bi bi-cash-coin"></i>
                    <span class="ms-2">Fines</span>
                </div>
                <div class="d-flex align-items-center">
                    @php
                    use App\Models\Fine;
                    $unpaidFinesCount = Fine::where('status', 'unpaid')->count();
                    @endphp
                    @if($unpaidFinesCount > 0)
                        <span class="badge bg-danger me-2">{{ $unpaidFinesCount }}</span>
                    @endif
                    <i class="bi bi-chevron-down transition-transform"></i>
                </div>
            </a>
            <div class="collapse submenu {{ Request::is('librarian/dashboard/fines*') ? 'show' : '' }}" id="finesMenu">
                <a class="nav-link d-flex align-items-center {{ Request::routeIs('librarian.fines.index') && !request('status') ? 'active' : '' }}" 
                    href="{{ route('librarian.fines.index') }}">
                    <i class="bi bi-list-ul"></i>
                    <span class="ms-2 flex-grow-1">All Fines</span>
                    <span class="badge bg-secondary ms-1">{{ Fine::count() }}</span>
                </a>
                <a class="nav-link d-flex align-items-center {{ request('status') == 'unpaid' ? 'active' : '' }}" 
                    href="{{ route('librarian.fines.index', ['status' => 'unpaid']) }}">
                    <i class="bi bi-exclamation-circle text-danger"></i>
                    <span class="ms-2 flex-grow-1">Unpaid Fines</span>
                    @if($unpaidFinesCount > 0)
                        <span class="badge bg-danger ms-1">{{ $unpaidFinesCount }}</span>
                    @endif
                </a>
                <a class="nav-link d-flex align-items-center {{ request('status') == 'paid' ? 'active' : '' }}" 
                    href="{{ route('librarian.fines.index', ['status' => 'paid']) }}">
                    <i class="bi bi-check-circle text-success"></i>
                    <span class="ms-2 flex-grow-1">Paid Fines</span>
                    @php
                        $paidFinesCount = Fine::where('status', 'paid')->count();
                    @endphp
                    @if($paidFinesCount > 0)
                        <span class="badge bg-success ms-1">{{ $paidFinesCount }}</span>
                    @endif
                </a>
            </div>

            <!-- Users Dropdown -->
            <a class="nav-link d-flex justify-content-between align-items-center {{ Request::is('librarian/dashboard/users*') ? 'active' : '' }}" 
            data-bs-toggle="collapse" href="#usersMenu" 
            aria-expanded="{{ Request::is('librarian/dashboard/users*') ? 'true' : 'false' }}">
                <div class="d-flex align-items-center">
                    <i class="bi bi-people"></i>
                    <span class="ms-2">Users</span>
                </div>
                <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse submenu {{ Request::is('librarian/dashboard/users*') ? 'show' : '' }}" id="usersMenu">
                <a class="nav-link d-flex align-items-center {{ Request::is('librarian/dashboard/users') && !Request::is('librarian/dashboard/users/*') ? 'active' : '' }}" 
                href="{{ route('librarian.users.index') }}">
                    <i class="bi bi-list-ul"></i>
                    <span class="ms-2 flex-grow-1">View All Users</span>
                </a>
                <a class="nav-link d-flex align-items-center {{ Request::is('librarian/dashboard/users/create') ? 'active' : '' }}" 
                href="{{ route('librarian.users.create') }}">
                    <i class="bi bi-person-plus"></i>
                    <span class="ms-2 flex-grow-1">Add New User</span>
                </a>
            </div>
            
            <div class="nav-section-title">System</div>
            
            <a class="nav-link" href="{{ route('welcome') }}">
                <i class="bi bi-house"></i>
                <span>Public Site</span>
            </a>
            
            <a class="nav-link text-danger" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <button class="topbar-btn d-lg-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="search-bar ms-lg-4">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" placeholder="Search books, users, or anything...">
            </div>

            <div class="d-flex align-items-center gap-2 ms-auto">
                <button class="topbar-btn notification-badge">
                    <i class="bi bi-bell"></i>
                    <span class="badge bg-danger">3</span>
                </button>

                <button class="topbar-btn d-none d-md-flex">
                    <i class="bi bi-envelope"></i>
                </button>

                <div class="dropdown">
                    <button class="user-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-dropdown-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="user-dropdown-info">
                            <span class="user-dropdown-name">{{ Auth::user()->name }}</span>
                            <span class="user-dropdown-role">Librarian</span>
                        </div>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i>Settings</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-bell"></i>Notifications</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form-top').submit();">
                                <i class="bi bi-box-arrow-right"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <form id="logout-form-top" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    mobileOverlay.classList.toggle('show');
                });

                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    mobileOverlay.classList.remove('show');
                });
            }

            // Close mobile menu when clicking nav link
            const navLinks = sidebar.querySelectorAll('.nav-link:not([data-bs-toggle="collapse"])');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        sidebar.classList.remove('show');
                        mobileOverlay.classList.remove('show');
                    }
                });
            });

            // Smooth scroll behavior
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
        
    </script>
</body>
</html>