@extends('layouts.student')
@section('title', 'Search Results - Digital Library')

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
        padding: 1.5rem 0;
        position: relative;
        overflow: hidden;
        margin-bottom: 1rem;
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
    
    .page-title {
        font-weight: 700;
        margin-bottom: 0.25rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        color: white;
        font-size: 1.5rem;
    }
    
    .page-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
        color: rgba(255,255,255,0.9);
        margin-bottom: 0;
    }
    
    /* Compact Search Section with Results */
    .compact-search-section {
        background: white;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin: 1rem auto 2rem;
        max-width: 800px;
        border: 1px solid #e2e8f0;
    }
    
    .search-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .search-title-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .search-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .search-title i {
        color: var(--primary);
    }
    
    /* Results Badge - Integrated with Search Title */
    .results-badge {
        background: #f1f5f9;
        border-radius: 20px;
        padding: 0.3rem 0.75rem;
        font-size: 0.85rem;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        border: 1px solid #e2e8f0;
    }
    
    .results-badge .number {
        font-weight: 700;
        color: var(--primary);
    }
    
    .results-badge .query {
        font-style: italic;
        color: var(--primary);
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .back-link {
        color: #64748b;
        text-decoration: none;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        transition: color 0.3s;
        white-space: nowrap;
    }
    
    .back-link:hover {
        color: var(--primary);
    }
    
    /* Compact Search Form */
    .compact-search-form {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .search-input-wrapper {
        flex: 1;
        position: relative;
    }
    
    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
    }
    
    .compact-search-input {
        width: 100%;
        padding: 0.6rem 0.75rem 0.6rem 2.25rem;
        font-size: 0.95rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: white;
        transition: all 0.3s;
        color: var(--dark);
    }
    
    .compact-search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
    
    .compact-search-input::placeholder {
        color: #94a3b8;
        font-size: 0.9rem;
    }
    
    .compact-search-btn {
        background: var(--gradient);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 0.6rem 1.25rem;
        font-size: 0.95rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s;
        white-space: nowrap;
    }
    
    .compact-search-btn:hover {
        background: var(--secondary);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(67, 97, 238, 0.2);
    }
    
    /* Book Cards - Same as other pages */
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
    
    .book-unavailable {
        opacity: 0.85;
    }
    
    .book-unavailable:hover {
        transform: none !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
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
        background: var(--primary);
        color: white;
        z-index: 5;
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
    
    .days-left .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
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
    
    /* Empty State */
    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin: 1rem 0;
        border: 1px solid #e2e8f0;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }
    
    .empty-state-title {
        color: #475569;
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
    }
    
    .empty-state-text {
        color: #64748b;
        margin-bottom: 1.5rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
        font-size: 0.9rem;
    }
    
    .empty-state-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-width: 300px;
        margin: 0 auto;
    }
    
    /* Pagination */
    .pagination-container {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }
    
    .pagination .page-link {
        color: var(--primary);
        border: 1px solid #e2e8f0;
        margin: 0 2px;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
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
    
    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem 0;
        }
        
        .page-title {
            font-size: 1.25rem;
        }
        
        .compact-search-section {
            padding: 1rem;
            margin: 0.5rem auto 1.5rem;
        }
        
        .search-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .search-title-wrapper {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .results-badge {
            align-self: flex-start;
        }
        
        .compact-search-form {
            flex-direction: column;
            width: 100%;
        }
        
        .compact-search-btn {
            width: 100%;
            justify-content: center;
        }
        
        .book-card {
            min-height: 400px;
        }
    }
    
    @media (max-width: 576px) {
        .search-header {
            align-items: flex-start;
        }
        
        .back-link {
            align-self: flex-start;
        }
    }
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container page-header-content">
        <h1 class="page-title">Search Results</h1>
        <p class="page-subtitle">Found {{ $books->total() }} results</p>
    </div>
</section>

<!-- Main Content -->
<section class="py-3">
    <div class="container">
        <!-- Compact Search Section with Integrated Results -->
        <div class="compact-search-section">
            <div class="search-header">
            <div class="search-title-wrapper">
                <h2 class="search-title">
                    <i class="fas fa-search"></i> Search Books
                </h2>
                
                @if($books->count() > 0)
                    <div class="results-badge">
                        @if($books->total() == 1)
                            <span class="number">1 result</span>
                            <span>found for</span>
                            <span class="query">"{{ $query }}"</span>
                        @else
                            <span class="number">{{ $books->firstItem() }}-{{ $books->lastItem() }}</span>
                            <span>of</span>
                            <span class="number">{{ $books->total() }}</span>
                            <span>results for</span>
                            <span class="query">"{{ $query }}"</span>
                        @endif
                    </div>
                @endif
            </div>
            
            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
            
            <form action="{{ route('welcome.search') }}" method="GET" class="compact-search-form">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           name="query" 
                           class="compact-search-input" 
                           placeholder="Search by title, author, category, or genre..." 
                           value="{{ $query }}" 
                           required
                           autocomplete="off">
                </div>
                <button type="submit" class="compact-search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

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
                    {{ $books->appends(['query' => $query])->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-search fa-3x"></i>
                </div>
                <h3 class="empty-state-title">No results found for "{{ $query }}"</h3>
                <p class="empty-state-text">
                    We couldn't find any books matching your search. Try using different keywords or check for typos.
                </p>
                <div class="empty-state-actions">
                    <button onclick="document.querySelector('input[name=\"query\"]').focus()" 
                            class="btn btn-primary btn-sm">
                        <i class="fas fa-redo me-2"></i> Try New Search
                    </button>
                    <a href="{{ url('/') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-home me-2"></i> Back to Home
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Auto-focus and select search text
        const searchInput = document.querySelector('input[name="query"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    });
</script>
@endsection