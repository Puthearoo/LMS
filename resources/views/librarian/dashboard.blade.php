@extends('layouts.librarian')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 mb-1 fw-bold text-dark">Dashboard Overview</h1>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Here's what's happening in your library.</p>
        </div>
        <div class="badge bg-light text-dark px-3 py-2 border">
            <i class="bi bi-calendar3 me-1"></i>
            <span id="currentTime">{{ now()->format('F j, Y • h:i A') }}</span>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Total Books -->
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-center">
                        <h2 class="fw-bold mb-1">{{ number_format($stats['total_books']) }}</h2>
                        <h6 class="text-muted text-uppercase small mb-2">Total Books</h6>
                        @php
                            $availableCount = \App\Models\Book::where('availability_status', 'available')->count();
                        @endphp
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            <small class="text-muted">{{ $availableCount }} available</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Checkouts -->
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-center">
                        <h2 class="fw-bold mb-1">{{ number_format($stats['today_checkouts']) }}</h2>
                        <h6 class="text-muted text-uppercase small mb-2">Today's Checkouts</h6>
                        @php
                            $yesterdayCheckouts = \App\Models\Checkout::whereDate('created_at', \Carbon\Carbon::yesterday())->count();
                            $checkoutChange = $yesterdayCheckouts > 0 ? round((($stats['today_checkouts'] - $yesterdayCheckouts) / $yesterdayCheckouts) * 100) : 100;
                        @endphp
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-arrow-{{ $checkoutChange >= 0 ? 'up' : 'down' }} text-{{ $checkoutChange >= 0 ? 'success' : 'danger' }} me-1"></i>
                            <small class="text-{{ $checkoutChange >= 0 ? 'success' : 'danger' }}">
                                {{ abs($checkoutChange) }}% {{ $checkoutChange >= 0 ? 'increase' : 'decrease' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Books -->
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-center">
                        <h2 class="fw-bold mb-1">{{ number_format($stats['pending_returns']) }}</h2>
                        <h6 class="text-muted text-uppercase small mb-2">Overdue Books</h6>
                        @php
                            $overdueFines = \App\Models\Fine::where('status', 'unpaid')
                                ->where('reason', \App\Models\Fine::REASON_OVERDUE)
                                ->sum('amount');
                        @endphp
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-cash-coin text-danger me-1"></i>
                            <small class="text-danger">${{ number_format($overdueFines, 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Reservations -->
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-center">
                        <h2 class="fw-bold mb-1">{{ number_format($stats['active_reservations']) }}</h2>
                        <h6 class="text-muted text-uppercase small mb-2">Active Reservations</h6>
                        @php
                            $expiringSoon = \App\Models\Reservation::where('status', 'ready')
                                ->where('expiry_date', '<=', now()->addDays(2))
                                ->where('expiry_date', '>=', now())
                                ->count();
                        @endphp
                        <div class="d-flex align-items-center justify-content-center">
                            @if($expiringSoon > 0)
                                <i class="bi bi-clock text-warning me-1"></i>
                                <small class="text-warning">{{ $expiringSoon }} expiring soon</small>
                            @else
                                <i class="bi bi-check-circle text-success me-1"></i>
                                <small class="text-success">All on track</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unpaid Fines -->
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-center">
                        <h2 class="fw-bold mb-1">${{ number_format($stats['unpaid_fines'], 2) }}</h2>
                        <h6 class="text-muted text-uppercase small mb-2">Unpaid Fines</h6>
                        @php
                            $unpaidCount = \App\Models\Fine::where('status', 'unpaid')->count();
                        @endphp
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-exclamation-circle text-warning me-1"></i>
                            <small class="text-warning">{{ $unpaidCount }} outstanding</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Extension Requests -->
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-center">
                        <h2 class="fw-bold mb-1">{{ number_format($stats['pending_extensions']) }}</h2>
                        <h6 class="text-muted text-uppercase small mb-2">Extension Requests</h6>
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-hourglass-split text-info me-1"></i>
                            <small class="text-info">Waiting approval</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2 g-md-3">
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <a href="{{ route('librarian.books.create') }}" class="card action-card border-0 text-decoration-none h-100">
                                <div class="card-body text-center p-3">
                                    <div class="action-icon bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-2 p-2">
                                        <i class="bi bi-plus-circle fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 small">Add New Book</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <a href="{{ route('librarian.books.index') }}" class="card action-card border-0 text-decoration-none h-100">
                                <div class="card-body text-center p-3">
                                    <div class="action-icon bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-2 p-2">
                                        <i class="bi bi-search fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 small">Browse Books</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <a href="{{ route('librarian.checkouts.pending') }}" class="card action-card border-0 text-decoration-none h-100">
                                <div class="card-body text-center p-3 position-relative">
                                    <div class="action-icon bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-2 p-2">
                                        <i class="bi bi-clock fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 small">Pending Requests</h6>
                                    @php
                                        $pendingCount = \App\Models\Checkout::where('status', 'pending')->count();
                                    @endphp
                                    @if($pendingCount > 0)
                                        <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger">
                                            {{ $pendingCount }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <a href="{{ route('librarian.fines.index') }}" class="card action-card border-0 text-decoration-none h-100">
                                <div class="card-body text-center p-3 position-relative">
                                    <div class="action-icon bg-danger bg-opacity-10 text-danger rounded-circle mx-auto mb-2 p-2">
                                        <i class="bi bi-cash-coin fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 small">Manage Fines</h6>
                                    @if($stats['unpaid_fines'] > 0)
                                        <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger">
                                            ${{ number_format($stats['unpaid_fines'], 0) }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <a href="{{ route('librarian.checkouts.create') }}" class="card action-card border-0 text-decoration-none h-100">
                                <div class="card-body text-center p-3">
                                    <div class="action-icon bg-secondary bg-opacity-10 text-secondary rounded-circle mx-auto mb-2 p-2">
                                        <i class="bi bi-bookmark-plus fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 small">Direct Checkout</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <a href="{{ route('librarian.reservations.index') }}" class="card action-card border-0 text-decoration-none h-100">
                                <div class="card-body text-center p-3 position-relative">
                                    <div class="action-icon bg-purple-subtle text-purple rounded-circle mx-auto mb-2 p-2 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                        <i class="bi bi-bookmark-star fs-3"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 small">Reservations</h6>
                                    @if($expiringSoon > 0)
                                        <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-warning">
                                            {{ $expiringSoon }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-3 g-md-4">
    <!-- Recent Checkouts -->
    <div class="col-xl-6 col-lg-12 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3">
                <div>
                    <h5 class="mb-0 fw-bold">Recent Checkouts</h5>
                    <small class="text-muted">Preview ({{ $todayCheckouts->count() }} shown)</small>
                </div>
                <a href="{{ route('librarian.checkouts.index') }}" class="btn btn-sm btn-outline-primary mt-2 mt-sm-0">
                    View All ({{ $stats['today_checkouts'] }}) →
                </a>
            </div>
            <div class="card-body p-0">
                @if($todayCheckouts && $todayCheckouts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($todayCheckouts as $checkout)
                        <div class="list-group-item border-0 px-3 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    @if($checkout->book->image)
                                        <img src="{{ asset('storage/' . $checkout->book->image) }}" 
                                             alt="{{ $checkout->book->title }}" 
                                             class="rounded" width="45" height="65" style="object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 45px; height: 65px;">
                                            <i class="bi bi-book text-muted fs-5"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ Str::limit($checkout->book->title, 35) }}</h6>
                                            <p class="small text-muted mb-0">
                                                <i class="bi bi-person me-1"></i>{{ $checkout->user->name }}
                                                <span class="mx-1 d-none d-sm-inline">•</span>
                                                <br class="d-sm-none">
                                                <i class="bi bi-clock me-1"></i>{{ $checkout->created_at->format('h:i A') }}
                                            </p>
                                        </div>
                                        <span class="badge bg-{{ $checkout->status == 'checked_out' ? 'success' : 'warning' }}-subtle text-{{ $checkout->status == 'checked_out' ? 'success' : 'warning' }} border border-{{ $checkout->status == 'checked_out' ? 'success' : 'warning' }}-subtle mt-2 mt-sm-0">
                                            {{ ucfirst(str_replace('_', ' ', $checkout->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($stats['today_checkouts'] > 5)
                    <div class="text-center py-2 border-top">
                        <small class="text-muted">
                            Showing 5 most recent • <a href="{{ route('librarian.checkouts.index') }}" class="text-decoration-none">View all {{ $stats['today_checkouts'] }} today</a>
                        </small>
                    </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle display-4 text-muted opacity-25"></i>
                        <p class="mt-3 text-muted">No checkouts today</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Overdue Books -->
    <div class="col-xl-6 col-lg-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3">
                <div>
                    <h5 class="mb-0 fw-bold">Overdue Books</h5>
                    <small class="text-muted">Preview ({{ $pendingReturns->count() }} shown)</small>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                    <!-- Generate Fines Button -->
                    <form action="{{ route('librarian.fines.generate-overdue') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" 
                                title="Generate Overdue Fines"
                                onclick="return confirm('Generate fines for all overdue books?')">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Generate
                        </button>
                    </form>
                    <a href="{{ route('librarian.fines.index') }}" class="btn btn-sm btn-outline-primary">
                        View All ({{ $stats['pending_returns'] }}) →
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($pendingReturns && $pendingReturns->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pendingReturns as $checkout)
                        @php
                            // Use your model's daysOverdue() method for accurate calculation
                            $daysOverdue = $checkout->daysOverdue();
                            $fineAmount = $daysOverdue * 0.50;
                            $urgency = $daysOverdue > 14 ? 'high' : ($daysOverdue > 7 ? 'medium' : 'low');
                        @endphp
                        <div class="list-group-item border-0 px-3 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="urgency-indicator urgency-{{ $urgency }} rounded-circle me-3"></div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ Str::limit($checkout->book->title, 35) }}</h6>
                                            <p class="small text-muted mb-0">
                                                <i class="bi bi-person me-1"></i>{{ $checkout->user->name }}
                                                <span class="mx-1 d-none d-sm-inline">•</span>
                                                <br class="d-sm-none">
                                                <i class="bi bi-calendar-x me-1"></i>Due: {{ $checkout->due_date->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="text-sm-end mt-2 mt-sm-0">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-block d-sm-inline-block mb-1 mb-sm-0">
                                                {{ $daysOverdue }} day{{ $daysOverdue != 1 ? 's' : '' }}
                                            </span>
                                            <div>
                                                <small class="text-warning fw-bold">
                                                    <i class="bi bi-cash-coin me-1"></i>${{ number_format($fineAmount, 2) }}
                                                </small>
                                            </div>
                                            <!-- Check if fine already exists -->
                                            @if($checkout->hasUnpaidFine())
                                                <div class="mt-1">
                                                    <small class="text-danger">
                                                        <i class="bi bi-exclamation-circle me-1"></i> Fine pending
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($stats['pending_returns'] > 5)
                    <div class="text-center py-2 border-top">
                        <small class="text-muted">
                            Showing 5 most overdue • <a href="{{ route('librarian.fines.index') }}" class="text-decoration-none">View all {{ $stats['pending_returns'] }} overdue</a>
                        </small>
                    </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle display-4 text-success opacity-25"></i>
                        <p class="mt-3 text-success">No overdue books! Great job!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section -->
<div class="row mt-3 mt-md-4">
    <!-- Extension Requests -->
    <div class="col-xl-6 col-lg-12 mb-3 mb-xl-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3">
                <div>
                    <h5 class="mb-0 fw-bold">Extension Requests</h5>
                    <small class="text-muted">Preview ({{ $pendingExtensions->count() }} shown)</small>
                </div>
                <a href="{{ route('librarian.checkouts.pending-extensions') }}" class="btn btn-sm btn-outline-info mt-2 mt-sm-0">
                    View All ({{ $stats['pending_extensions'] }}) →
                </a>
            </div>
            <div class="card-body p-0">
                @if($pendingExtensions && $pendingExtensions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pendingExtensions as $checkout)
                        <div class="list-group-item border-0 px-3 py-3">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ Str::limit($checkout->book->title, 35) }}</h6>
                                    <p class="small text-muted mb-2">
                                        <i class="bi bi-person me-1"></i> {{ $checkout->user->name }}
                                        <span class="mx-1 d-none d-sm-inline">•</span>
                                        <br class="d-sm-none">
                                        <i class="bi bi-calendar me-1"></i> Due: {{ $checkout->due_date->format('M d, Y') }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i> Requested {{ $checkout->extension_requested_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="d-flex gap-1 mt-3 mt-sm-0">
                                    <form action="{{ route('librarian.checkouts.approve-extension', $checkout) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-circle p-2" 
                                                title="Approve extension">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('librarian.checkouts.reject-extension', $checkout) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2"
                                                title="Reject extension">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($stats['pending_extensions'] > 5)
                    <div class="text-center py-2 border-top">
                        <small class="text-muted">
                            Showing 5 oldest • <a href="{{ route('librarian.checkouts.pending-extensions') }}" class="text-decoration-none">View all {{ $stats['pending_extensions'] }} pending</a>
                        </small>
                    </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle display-4 text-muted opacity-25"></i>
                        <p class="mt-3 text-muted">No pending extension requests</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Checkouts -->
    <div class="col-xl-6 col-lg-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center py-3">
                <div>
                    <h5 class="mb-0 fw-bold">Active Checkouts</h5>
                    <small class="text-muted">Preview ({{ $activeCheckouts->count() }} shown)</small>
                </div>
                <a href="{{ route('librarian.checkouts.index') }}" class="btn btn-sm btn-outline-success mt-2 mt-sm-0">
                    View All →
                </a>
            </div>
            <div class="card-body p-0">
                @if($activeCheckouts && $activeCheckouts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($activeCheckouts as $checkout)
                        @php
                            // Use the model's days_remaining attribute which already uses startOfDay()
                            $daysLeft = $checkout->days_remaining;
                            
                            // Determine status class based on your model logic
                            if ($daysLeft <= 0) {
                                $dueClass = 'danger';
                                $daysText = abs($daysLeft) . ' day' . (abs($daysLeft) != 1 ? 's' : '') . ' overdue';
                            } elseif ($daysLeft <= 3) {
                                $dueClass = 'warning';
                                $daysText = $daysLeft . ' day' . ($daysLeft != 1 ? 's' : '') . ' left';
                            } else {
                                $dueClass = 'success';
                                $daysText = $daysLeft . ' day' . ($daysLeft != 1 ? 's' : '') . ' left';
                            }
                            
                            // Use model methods to check extend status
                            $isExtended = $checkout->is_extended; // Uses getIsExtendedAttribute()
                            $canExtend = $checkout->canShowExtendButton(); // Uses canShowExtendButton() method
                            $hasPendingExtension = $checkout->hasPendingExtension();
                        @endphp
                        <div class="list-group-item border-0 px-3 py-3">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ Str::limit($checkout->book->title, 35) }}</h6>
                                    <div class="d-flex align-items-center flex-wrap gap-2 small text-muted mb-1">
                                        <span class="d-flex align-items-center">
                                            <i class="bi bi-person me-1"></i> {{ $checkout->user->name }}
                                        </span>
                                        <span class="badge bg-{{ $dueClass }}-subtle text-{{ $dueClass }} border border-{{ $dueClass }}-subtle">
                                            {{ $daysText }}
                                        </span>
                                        @if($isExtended)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                <i class="bi bi-arrow-clockwise me-1"></i> Extended
                                            </span>
                                        @elseif($hasPendingExtension)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                <i class="bi bi-clock me-1"></i> Pending
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Show due date -->
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Due: {{ $checkout->due_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="mt-2 mt-sm-0">
                                    @if($canExtend)
                                        <form action="{{ route('librarian.checkouts.extend', $checkout) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="bi bi-calendar-plus me-1"></i> <span class="d-none d-md-inline">Extend</span>
                                            </button>
                                        </form>
                                    @elseif($isExtended)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i> Extended
                                        </span>
                                    @elseif($hasPendingExtension)
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('librarian.checkouts.approve-extension', $checkout) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-circle p-1" 
                                                        title="Approve extension">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('librarian.checkouts.reject-extension', $checkout) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1"
                                                        title="Reject extension">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($daysLeft <= 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Overdue
                                        </span>
                                    @else
                                        <span class="text-muted small">
                                            @if($checkout->extension_status === 'rejected')
                                                <i class="bi bi-x-circle me-1"></i> <span class="d-none d-sm-inline">Rejected</span>
                                            @else
                                                <i class="bi bi-lock me-1"></i> <span class="d-none d-sm-inline">Not extendable</span>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Progress bar showing time remaining -->
                            @if($daysLeft > 0)
                            <div class="progress mt-2" style="height: 4px;">
                                @php
                                    // Calculate percentage of time used (14-day checkout period)
                                    $totalDays = 14; // Standard checkout period
                                    $daysUsed = $totalDays - $daysLeft;
                                    $percentUsed = min(100, max(0, ($daysUsed / $totalDays) * 100));
                                @endphp
                                <div class="progress-bar bg-{{ $dueClass }}" 
                                     style="width: {{ $percentUsed }}%"
                                     title="{{ $daysUsed }} of {{ $totalDays }} days used"></div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center py-2 border-top">
                        <small class="text-muted">
                            Showing 10 soonest due checkouts
                        </small>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-book display-4 text-muted opacity-25"></i>
                        <p class="mt-3 text-muted">No active checkouts</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="row mt-3 mt-md-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body p-3">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between text-center text-md-start">
                    <div class="mb-2 mb-md-0">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Library System v1.0 • Last updated: {{ now()->format('g:i A') }}
                        </small>
                    </div>
                    <div>
                        <small class="text-muted">
                            <i class="bi bi-people me-1"></i>
                            Total Users: {{ \App\Models\User::count() }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        const date = now.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        const time = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        document.getElementById('currentTime').textContent = date + ' • ' + time;
    }
    
    updateTime();
    setInterval(updateTime, 60000);
    
    // Add hover effects
    document.addEventListener('DOMContentLoaded', function() {
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'all 0.3s ease';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        const actionCards = document.querySelectorAll('.action-card');
        actionCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
                this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.1)';
                this.style.transition = 'all 0.3s ease';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.05)';
            });
        });
    });
</script>

<style>
    /* Base styles */
    .stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        background: white;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    .stat-card h2 {
        font-size: 2rem;
        color: #333;
    }

    .stat-card h6 {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .action-card {
        border-radius: 8px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        text-decoration: none;
    }

    .action-card:hover {
        text-decoration: none;
        background: #f8f9fa;
    }

    .action-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .urgency-indicator {
        width: 10px;
        height: 10px;
        min-width: 10px;
    }

    .urgency-high { background-color: #e74a3b; }
    .urgency-medium { background-color: #f6c23e; }
    .urgency-low { background-color: #1cc88a; }

    .list-group-item {
        border-left: none;
        border-right: none;
        border-color: rgba(0,0,0,0.05);
    }

    .list-group-item:first-child {
        border-top: none;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    /* Desktop: 1200px and up */
    @media (min-width: 1200px) {
        .stat-card h2 {
            font-size: 2.25rem;
        }
        
        .action-icon {
            width: 60px;
            height: 60px;
        }
    }

    /* Tablet: 768px to 1199px */
    @media (min-width: 768px) and (max-width: 1199.98px) {
        .stat-card h2 {
            font-size: 1.75rem;
        }
        
        .stat-card .card-body {
            padding: 1.25rem !important;
        }
        
        .action-icon {
            width: 45px;
            height: 45px;
        }
    }

    /* Mobile: Below 768px */
    @media (max-width: 767.98px) {
        /* Statistics cards */
        .stat-card h2 {
            font-size: 1.5rem;
        }
        
        .stat-card h6 {
            font-size: 0.7rem;
        }
        
        .stat-card small {
            font-size: 0.75rem;
        }
        
        .stat-card .card-body {
            padding: 1rem 0.5rem !important;
        }
        
        /* Action cards */
        .action-card .card-body {
            padding: 0.75rem !important;
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
        }
        
        .action-icon i {
            font-size: 1.25rem !important;
        }
        
        /* Card headers */
        .card-header .d-flex {
            align-items: flex-start !important;
        }
        
        .card-header .btn {
            margin-top: 0.5rem;
            align-self: flex-start;
        }
        
        /* List items */
        .list-group-item {
            padding: 0.75rem !important;
        }
        
        /* Badge positioning */
        .position-absolute.badge {
            font-size: 0.6rem;
            padding: 0.25rem 0.4rem;
        }
    }

    /* Extra small mobile: Below 576px */
    @media (max-width: 575.98px) {
        /* Statistics grid - 2 cards per row */
        .col-6 {
            padding: 0.25rem !important;
        }
        
        .stat-card .card-body {
            padding: 0.75rem 0.25rem !important;
        }
        
        .stat-card h2 {
            font-size: 1.25rem;
        }
        
        /* Quick actions - 2 cards per row */
        .col-6.col-sm-4 {
            padding: 0.25rem !important;
        }
        
        .action-card h6 {
            font-size: 0.7rem;
            line-height: 1.2;
        }
        
        /* List items - compact view */
        .list-group-item .d-flex {
            align-items: flex-start !important;
        }
        
        .list-group-item img {
            width: 35px !important;
            height: 50px !important;
        }
        
        /* Buttons - full width on mobile */
        .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        /* Hide unnecessary elements on mobile */
        .d-none.d-sm-inline {
            display: none !important;
        }
        
        /* Show mobile-only elements */
        .d-sm-none {
            display: block !important;
        }
    }
   /* Responsive improvements */
    @media (max-width: 576px) {
        .card-header .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .list-group-item {
            padding: 0.75rem !important;
        }
        
        .rounded-circle.p-2 {
            padding: 0.5rem !important;
        }
        
        .badge {
            font-size: 0.7rem;
        }
    }
    /* Landscape orientation */
    @media (max-width: 767.98px) and (orientation: landscape) {
        .stat-card h2 {
            font-size: 1.75rem;
        }
        
        .stat-card .card-body {
            padding: 0.75rem !important;
        }
        
        .action-card .card-body {
            padding: 0.5rem !important;
        }
    }
</style>
@endsection