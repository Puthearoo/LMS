@extends('layouts.student')
@section('title', 'Books in ' . $category . ' - Digital Library')

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
    
    /* Page Header */
    .page-header {
        background: var(--gradient);
        color: white;
        padding: 3rem 0;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
    
    .page-header-content {
        position: relative;
        z-index: 1;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 1rem;
    }
    
    .breadcrumb-item a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: color 0.3s;
    }
    
    .breadcrumb-item a:hover {
        color: white;
    }
    
    .breadcrumb-item.active {
        color: white;
    }
    
    .breadcrumb-item+.breadcrumb-item::before {
        color: rgba(255,255,255,0.5);
    }
    
    .page-title {
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        color: white;
    }
    
    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        color: rgba(255,255,255,0.9);
    }
    
    /* Stats Cards */
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        border: 1px solid #e9ecef;
        transition: all 0.3s;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .stats-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--primary);
    }
    
    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }
    
    .stats-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    /* Book Cards - Matching Welcome Page */
    .book-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        height: fit-content;
        min-height: 450px;
        position: relative;
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
    
    /* Availability Badge */
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
    
    /* Price Tag */
    .price-tag {
        position: absolute;
        top: 10px;
        left: 10px;
        border-radius: 20px;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 600;
        background: var(--primary);
        color: white;
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
        height: 2.8em;
        overflow: hidden;
    }
    
    .card-text {
        color: #6c757d;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 0.5rem;
    }
    
    .book-meta {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    /* Book Actions */
    .book-actions {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    
    .checkout-btn, .reserve-btn {
        transition: all 0.3s ease;
    }
    
    .checkout-btn:hover, .reserve-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    /* Read-only book card styles - Matching Welcome Page */
    .book-unavailable {
        opacity: 0.85;
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
    
    /* Days Left Badge */
    .days-left .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Empty State */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }
    
    /* Pagination */
    .pagination-container {
        margin-top: 3rem;
    }
    
    .pagination .page-link {
        color: var(--primary);
        border: 1px solid #dee2e6;
        margin: 0 2px;
        border-radius: 6px;
        transition: all 0.3s;
    }
    
    .pagination .page-link:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    
    .pagination .page-item.active .page-link {
        background: var(--gradient);
        border-color: var(--primary);
        color: white;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .page-header {
            padding: 2rem 0;
        }
        
        .book-card {
            min-height: 400px;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container page-header-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/') }}#categories">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $category }}</li>
            </ol>
        </nav>
        <h1 class="page-title">{{ $category }} Books</h1>
        <p class="page-subtitle">Browse our collection of {{ $category }} books</p>
    </div>
</section>

<!-- Main Content -->
<section class="py-4">
    <div class="container">

        <!-- Books Grid -->
        @if($books->count() > 0)
            <div class="row g-4">
                @foreach($books as $book)
                    @php
                        $daysLeft = null;
                        if ($book->availability_status == 'checked_out') {
                            $activeCheckout = $book->checkouts()
                                ->whereIn('status', ['approved', 'checked_out'])
                                ->whereNull('return_date')
                                ->first();
                            if ($activeCheckout && $activeCheckout->due_date) {
                                $daysLeft = now()->diffInDays($activeCheckout->due_date, false);
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
                                
                                <!-- Price Tag -->
                                @if($book->price)
                                    <span class="badge bg-primary price-tag">
                                        ${{ number_format($book->price, 2) }}
                                    </span>
                                @endif
                                
                                <!-- Availability Badge -->
                                <span class="badge 
                                    @if($book->availability_status == 'available') bg-success
                                    @elseif($book->availability_status == 'reserved') bg-warning
                                    @else bg-danger @endif availability-badge">
                                    {{ ucfirst($book->availability_status) }}
                                </span>
                                
                                @if($book->availability_status != 'available')
                                    <div class="unavailable-overlay">
                                        <div class="unavailable-text">
                                            @if($book->availability_status == 'checked_out')
                                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                <div>Checked Out</div>
                                                @if($daysLeft !== null)
                                                    <div class="days-left mt-2">
                                                        @if($daysLeft > 0)
                                                            <span class="badge bg-info">{{ round($daysLeft) }} days left</span>
                                                        @elseif($daysLeft == 0)
                                                            <span class="badge bg-warning">Due today</span>
                                                        @else
                                                            <span class="badge bg-danger">Overdue by {{ abs(round($daysLeft)) }} days</span>
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
                                <h6 class="card-title">{{ $book->title }}</h6>
                                
                                <p class="card-text text-muted small">
                                    <strong>Author:</strong> {{ $book->author }}<br>
                                    <strong>Category:</strong> {{ $book->category }}<br>
                                    @if($book->genre)
                                        <strong>Genre:</strong> {{ $book->genre }}<br>
                                    @endif
                                    @if($book->publisher)
                                        <strong>Publisher:</strong> {{ $book->publisher }}<br>
                                    @endif
                                    @if($book->published_year)
                                        <strong>Year:</strong> {{ $book->published_year }}
                                    @endif
                                </p>
                                
                                @if($book->isbn)
                                    <p class="card-text small text-muted">
                                        <strong>ISBN:</strong> {{ $book->isbn }}
                                    </p>
                                @endif
                                
                                @if($book->description)
                                    <p class="card-text small">
                                        {{ Str::limit($book->description, 100) }}
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

            <!-- Pagination -->
            @if($books->hasPages())
                <div class="pagination-container">
                    {{ $books->links() }}
                </div>
            @endif

        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3 class="text-muted">No books found</h3>
                <p class="text-muted mb-4">
                    There are no books in the "{{ $category }}" category at the moment.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="{{ url('/') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Home
                    </a>
                    <a href="{{ url('/') }}#categories" class="btn btn-primary">
                        <i class="fas fa-tags me-2"></i> Browse Other Categories
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

<script>
    // Initialize Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection