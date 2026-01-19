@extends('layouts.student')
@section('title', 'Digital Library')

@section('content')
<style>
    :root {
        --primary: #4361ee;
        --secondary: #3f37c9;
        --success: #4cc9f0;
        --light: #f8f9fa;
        --dark: #212529;
        --accent: #7209b7;
        --warning: #f72585;
        --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        --gradient-light: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
    }
    
    /* Hero Section */
    .hero-section {
        background: var(--gradient);
        color: white;
        padding: 120px 0;
        position: relative;
        overflow: hidden;
        margin: 0;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
        padding: 0 15px;
    }
    
    .hero-title {
        font-weight: 800;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        line-height: 1.2;
    }
    
    .hero-lead {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        line-height: 1.6;
    }
    
    .search-container {
        max-width: 600px;
        margin: 0 auto;
        width: 100%;
    }
    
    .search-input {
        border-radius: 50px 0 0 50px;
        border: none;
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        width: 100%;
    }
    
    .search-btn {
        border-radius: 0 50px 50px 0;
        border: none;
        padding: 0.8rem 1.5rem;
        background: var(--accent);
        color: white;
        font-weight: 600;
        transition: all 0.3s;
        white-space: nowrap;
        min-width: 120px;
    }
    
    .search-btn:hover {
        background: #5a08a0;
        transform: translateY(-2px);
    }
    
    .hero-btns {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    
    .hero-btn {
        border-radius: 50px;
        padding: 0.7rem 1.8rem;
        font-weight: 600;
        transition: all 0.3s;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 160px;
    }
    
    .hero-btn-light {
        background: white;
        color: var(--primary);
        border: 2px solid transparent;
    }
    
    .hero-btn-light:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 14px rgba(0,0,0,0.1);
        background: white;
        color: var(--primary);
    }
    
    .hero-btn-outline-light {
        background: transparent;
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .hero-btn-outline-light:hover {
        transform: translateY(-3px);
        background: rgba(255,255,255,0.1);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }

    /* Alert Styles in Hero Section */
    .hero-alerts {
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        z-index: 1000;
        max-width: 800px;
        margin: 0 auto;
        padding: 0 15px;
    }
    
    .hero-alert {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.95);
        animation: slideDown 0.5s ease-out;
        padding: 0.75rem 1rem;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .hero-alert .btn-close {
        filter: brightness(0) invert(1);
        padding: 0.75rem;
    }
    
    /* Section Title */
    .section-title {
        position: relative;
        margin-bottom: 3rem;
        font-weight: 700;
        color: var(--dark);
        text-align: center;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--gradient-light);
        border-radius: 2px;
    }
    
    .book-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        height: fit-content;
        min-height: 450px;
    }
    
    .book-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .book-image {
        height: 220px;
        width: 100%;
        overflow: hidden;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .book-image img {
        width: 100%;
        height: 100%;
        object-fit: contain; 
        transition: transform 0.5s;
        background: white; 
    }

    .book-card:hover .book-image img {
        transform: scale(1.05);
    }
    
    .availability-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        border-radius: 20px;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 5;
    }
    
    .price-tag {
        position: absolute;
        top: 10px;
        left: 10px;
        border-radius: 20px;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 5;
    }
    
    .card-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        height: auto;
    }
    
    .card-title {
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: var(--dark);
        line-height: 1.4;
    }
    
    .card-text {
        color: #6c757d;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .category-card {
        transition: all 0.3s;
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        height: 100%;
        background: white;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        background: var(--gradient-light);
        color: white;
    }
    
    .category-card:hover .card-body i,
    .category-card:hover .card-title,
    .category-card:hover .text-muted {
        color: white !important;
    }
    
    .category-card .card-body {
        padding: 2rem 1rem;
        text-align: center;
    }
    
    .category-card i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--primary);
    }
    
    /* Stats Section */
    .stats-section {
        background: var(--light);
        padding: 4rem 0;
    }
    
    .stat-card {
        text-align: center;
        padding: 2rem 1rem;
        border-radius: 12px;
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--primary);
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }
    
    .stat-text {
        color: #6c757d;
        font-weight: 500;
    }

    /* Checkout Button Styles */
    .checkout-btn {
        transition: all 0.3s ease;
    }

    .checkout-btn:hover {
        transform: translateY(-2px);
    }

    /* Alert Styles */
    .alert {
        border: none;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    /* Read-only book card styles */
    .book-unavailable {
        opacity: 0.8;
    }

    .book-unavailable:hover {
        transform: none !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
    }

    .unavailable-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 3;
    }

    .unavailable-text {
        color: white;
        text-align: center;
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        font-size: 1.1rem;
    }

    .unavailable-text i {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 2rem;
    }

    /* Reduce hover effects for unavailable books */
    .book-unavailable .book-image img {
        transform: none !important;
    }

    /* Make the entire card slightly muted for unavailable books */
    .book-unavailable .card-title,
    .book-unavailable .card-text {
        opacity: 0.8;
    }

    /* Ensure overlay is visible */
    .book-image {
        position: relative;
    }

    /* ========== RESPONSIVE STYLES ========== */
    
    /* Large devices (desktops, 992px and up) */
    @media (max-width: 1199px) {
        .hero-section {
            padding: 100px 0;
        }
        
        .hero-title {
            font-size: 2.8rem;
        }
        
        .hero-lead {
            font-size: 1.1rem;
        }
    }
    
    /* Medium devices (tablets, 768px and up) */
    @media (max-width: 991px) {
        .hero-section {
            padding: 80px 0;
        }
        
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-lead {
            font-size: 1.1rem;
            padding: 0 20px;
        }
        
        .search-container {
            max-width: 500px;
        }
        
        .hero-btns {
            gap: 0.8rem;
        }
        
        .hero-btn {
            min-width: 140px;
            padding: 0.6rem 1.5rem;
            font-size: 0.95rem;
        }
    }
    
    /* Small devices (landscape phones, 576px and up) */
    @media (max-width: 767px) {
        .hero-section {
            padding: 70px 0;
        }
        
        .hero-title {
            font-size: 2.2rem;
            margin-bottom: 1.2rem;
            padding: 0 15px;
        }
        
        .hero-lead {
            font-size: 1rem;
            margin-bottom: 1.5rem;
            padding: 0 15px;
        }
        
        .search-container {
            max-width: 100%;
            padding: 0 15px;
        }
        
        .search-form {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .search-input {
            border-radius: 8px;
            padding: 0.8rem 1.2rem;
            font-size: 0.95rem;
        }
        
        .search-btn {
            border-radius: 8px;
            width: 100%;
            padding: 0.8rem;
            min-width: auto;
        }
        
        .hero-btns {
            flex-direction: column;
            gap: 0.75rem;
            padding: 0 15px;
            margin-top: 1.5rem;
        }
        
        .hero-btn {
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            padding: 0.7rem 1.5rem;
        }
        
        .hero-alerts {
            top: 10px;
            padding: 0 10px;
        }
        
        .hero-alert {
            padding: 0.6rem 0.8rem;
            font-size: 0.9rem;
        }
        
        .hero-alert .btn-close {
            padding: 0.5rem;
        }
        
        /* Book Cards */
        .book-card {
            min-height: 400px;
        }
        
        .book-image {
            height: 180px;
        }
        
        .card-body {
            padding: 1.2rem;
        }
    }
    
    /* Extra small devices (portrait phones, less than 576px) */
    @media (max-width: 575px) {
        .hero-section {
            padding: 60px 0;
        }
        
        .hero-title {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .hero-lead {
            font-size: 0.95rem;
            margin-bottom: 1.2rem;
            line-height: 1.5;
        }
        
        .search-input {
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
        }
        
        .search-btn {
            padding: 0.7rem;
            font-size: 0.9rem;
        }
        
        .hero-btn {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            max-width: 250px;
        }
        
        .hero-btn i {
            margin-right: 0.5rem;
        }
        
        .hero-alerts {
            top: 8px;
        }
        
        .hero-alert {
            padding: 0.5rem 0.7rem;
            font-size: 0.85rem;
        }
        
        /* Book Cards */
        .book-card {
            min-height: 380px;
            margin-bottom: 1.5rem;
        }
        
        .book-image {
            height: 160px;
        }
        
        .card-title {
            font-size: 0.95rem;
        }
        
        .card-text {
            font-size: 0.85rem;
        }
        
        .availability-badge,
        .price-tag {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
        }
        
        .book-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .book-actions .btn {
            width: 100%;
            margin: 2px 0;
            padding: 0.5rem 0.8rem;
            font-size: 0.85rem;
        }
    }
    
    /* Very small devices (phones less than 400px) */
    @media (max-width: 400px) {
        .hero-title {
            font-size: 1.6rem;
        }
        
        .hero-lead {
            font-size: 0.9rem;
        }
        
        .hero-btn {
            max-width: 100%;
        }
        
        .book-card {
            min-height: 360px;
        }
        
        .book-image {
            height: 150px;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <!-- Alerts in Hero Section -->
    <div class="hero-alerts">
        @if(session('success'))
            <div class="alert alert-success hero-alert alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger hero-alert alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger hero-alert alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Please check the form below for errors.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
    
    <div class="container text-center hero-content">
        <h1 class="hero-title display-4 mb-4">Discover Your Next Favorite Book</h1>
        <p class="hero-lead mb-4">Access thousands of books, journals, and resources in our digital library collection</p>
        
        <!-- Search Form -->
        <div class="search-container">
            <form action="{{ route('welcome.search') }}" method="GET" class="d-flex search-form">
                <input type="text" name="query" class="form-control search-input" 
                       placeholder="Search by title, author, ISBN, or category...">
                <button class="btn search-btn" type="submit">
                    <i class="fas fa-search me-2"></i> Search
                </button>
            </form>
        </div>

        <div class="hero-btns">
            @auth
                @if(auth()->user()->role === 'librarian')
                    <a href="{{ route('librarian.dashboard') }}" class="btn hero-btn hero-btn-light">
                        <i class="fas fa-tachometer-alt me-2"></i> Librarian Dashboard
                    </a>
                @elseif(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn hero-btn hero-btn-light">
                        <i class="fas fa-tachometer-alt me-2"></i> Admin Dashboard
                    </a>
                @endif
                <a href="{{ route('my.checkouts') }}" class="btn hero-btn hero-btn-outline-light">
                    <i class="fas fa-history me-2"></i> My Checkouts
                </a>
            @else
                <a href="{{ route('login') }}" class="btn hero-btn hero-btn-light">
                    <i class="fas fa-sign-in-alt me-2"></i> Student Login
                </a>
                <a href="{{ route('register') }}" class="btn hero-btn hero-btn-outline-light">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </a>
            @endauth
        </div>
    </div>
</section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <i class="fas fa-book stat-icon"></i>
                        <div class="stat-number">{{ $totalBooks ?? '0' }}</div>
                        <div class="stat-text">Books Available</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <i class="fas fa-tags stat-icon"></i>
                        <div class="stat-number">{{ $totalCategories ?? '0' }}</div>
                        <div class="stat-text">Categories</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Books Section -->
    <section class="py-5" id="featured-books">
        <div class="container">
            <h2 class="text-center section-title">Featured Books</h2>
            @if($featuredBooks->count() > 0)
                <div class="row g-4">
                    @foreach($featuredBooks as $book)
                        @php
                            $daysLeft = null;
                            if ($book->availability_status == 'checked_out') {
                                $activeCheckout = $book->checkouts()
                                    ->whereIn('status', ['approved', 'checked_out'])
                                    ->whereNull('return_date')
                                    ->first();
                                if ($activeCheckout && $activeCheckout->due_date) {
                                    // FIX: Calculate days left properly
                                    $rawDays = now()->diffInDays($activeCheckout->due_date, false);
                                    
                                    if ($rawDays > 0) {
                                        // Use ceil() to round up for remaining days
                                        // Example: 13.2 days left should show as 14 days left
                                        $daysLeft = ceil($rawDays);
                                    } elseif ($rawDays == 0) {
                                        $daysLeft = 0;
                                    } else {
                                        // For overdue, use floor() to round down
                                        // Example: -1.5 days overdue should show as -1 days
                                        $daysLeft = floor($rawDays);
                                    }
                                }
                            }
                        @endphp
                        
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card book-card @if($book->availability_status != 'available') book-unavailable @endif">
                                <div class="book-image">
                                    @if($book->image)
                                        <img src="{{ asset('storage/' . $book->image) }}" 
                                            alt="{{ $book->title }}" 
                                            class="card-img-top">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-book fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    @if($book->availability_status != 'available')
                                        <div class="unavailable-overlay">
                                            <div class="unavailable-text">
                                                @if($book->availability_status == 'checked_out')
                                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                    <div>Checked Out</div>
                                                    @if($daysLeft !== null)
                                                        <div class="days-left mt-2">
                                                            @if($daysLeft > 0)
                                                                {{-- FIX: Remove round() since we already used ceil() --}}
                                                                <span class="badge bg-info">{{ $daysLeft }} days left</span>
                                                            @elseif($daysLeft == 0)
                                                                <span class="badge bg-warning">Due today</span>
                                                            @else
                                                                {{-- FIX: Remove round() since we already used floor() --}}
                                                                <span class="badge bg-danger">Overdue by {{ abs($daysLeft) }} days</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @elseif($book->availability_status == 'reserved')
                                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                                    <div>Reserved</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <span class="badge 
                                        @if($book->availability_status == 'available') bg-success
                                        @elseif($book->availability_status == 'reserved') bg-warning
                                        @else bg-danger @endif availability-badge">
                                        {{ ucfirst($book->availability_status) }}
                                    </span>
                                    
                                    <span class="badge bg-primary price-tag">
                                        ${{ number_format($book->price, 2) }}
                                    </span>
                                    
                                    <h6 class="card-title">{{ Str::limit($book->title, 50) }}</h6>
                                    <p class="card-text text-muted small">
                                        <strong>Author:</strong> {{ $book->author }}<br>
                                        <strong>Category:</strong> {{ $book->category }}<br>
                                        <strong>Genre:</strong> {{ $book->genre }}
                                    </p>
                                    @if($book->isbn)
                                        <p class="card-text small text-muted">
                                            <strong>ISBN:</strong> {{ $book->isbn }}
                                        </p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-center align-items-center mt-3 book-actions">
                                        @auth
                                            @if($book->availability_status == 'available')
                                                <form action="{{ route('books.checkout', $book) }}" method="POST" class="d-inline w-100">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success checkout-btn w-100" 
                                                            onclick="return confirm('Are you sure you want to checkout {{ $book->title }}?')">
                                                        <i class="fas fa-shopping-cart me-1"></i>Checkout
                                                    </button>
                                                </form>
                                            @else
                                                @if($book->availability_status == 'checked_out')
                                                    <form action="{{ route('books.reserve', $book) }}" method="POST" class="d-inline w-100">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary reserve-btn w-100" 
                                                                onclick="return confirm('Would you like to reserve {{ $book->title }}? You will be notified when it becomes available.')">
                                                            <i class="fas fa-clock me-1"></i>Reserve
                                                        </button>
                                                    </form>
                                                @elseif($book->availability_status == 'reserved')
                                                    <button class="btn btn-warning w-100" disabled>
                                                        <i class="fas fa-clock me-1"></i>Reserved
                                                    </button>
                                                @endif
                                            @endif
                                        @else
                                            <div class="w-100">
                                                <button class="btn btn-success w-100 mb-2" disabled data-bs-toggle="tooltip" title="Please login to checkout">
                                                    <i class="fas fa-shopping-cart me-1"></i>Checkout
                                                </button>
                                                <a href="{{ route('login') }}" class="btn btn-outline-success w-100">
                                                    <i class="fas fa-sign-in-alt me-1"></i>Login to Checkout
                                                </a>
                                            </div>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-5">
                    <a href="#" class="btn btn-primary">View All Books <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No books available at the moment.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Browse by Category Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center section-title">Browse by Category</h2>
            @if($categories->count() > 0)
                <div class="row g-4">
                    @foreach($categories as $category)
                        <div class="col-md-4 col-sm-6">
                            <a href="{{ route('welcome.category', $category->category) }}" 
                            class="text-decoration-none">
                                <div class="card category-card text-center py-4">
                                    <div class="card-body">
                                        <i class="fas fa-folder fa-2x mb-3"></i>
                                        <h5 class="card-title">{{ $category->category }}</h5>
                                        <p class="text-muted">
                                            {{ $category->book_count }} books available
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-folder fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No categories available.</p>
                </div>
            @endif
        </div>
    </section>

<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection