@extends('layouts.student')

@section('title', 'My Checkout History')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
                <h1 class="h3 mb-0">
                    <i class="fas fa-history text-primary me-2"></i>My Checkout History
                </h1>
                <a href="{{ route('welcome') }}" class="btn btn-outline-primary">
                    <i class="fas fa-book me-1"></i>Browse More Books
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($checkouts->count() > 0)
                <!-- Quick Stats Cards -->
                <div class="row g-2 g-md-3 mb-4 row-cols-2 row-cols-md-4">
                    <!-- Returned On Time -->
                    <div class="col">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-2 p-md-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted text-uppercase small mb-1 d-block">Returned On Time</small>
                                        <h4 class="mb-0 fw-bold text-success fs-5 fs-md-4">
                                            {{ $checkouts->where('status', 'returned')
                                                ->filter(fn($c) => !$c->wasReturnedOverdue())
                                                ->count() }}
                                        </h4>
                                    </div>
                                    <div class="bg-success bg-opacity-10 rounded p-1 p-md-2">
                                        <i class="fas fa-check-circle text-success fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Returned Overdue -->
                    <div class="col">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-2 p-md-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted text-uppercase small mb-1 d-block">Returned Late</small>
                                        <h4 class="mb-0 fw-bold text-danger fs-5 fs-md-4">
                                            {{ $checkouts->where('status', 'returned')
                                                ->filter(fn($c) => $c->wasReturnedOverdue())
                                                ->count() }}
                                        </h4>
                                    </div>
                                    <div class="bg-danger bg-opacity-10 rounded p-1 p-md-2">
                                        <i class="fas fa-exclamation-triangle text-danger fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Currently Overdue -->
                    <div class="col">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-2 p-md-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted text-uppercase small mb-1 d-block">Currently Overdue</small>
                                        <h4 class="mb-0 fw-bold text-warning fs-5 fs-md-4">
                                            {{ $checkouts->where('status', 'checked_out')
                                                ->filter(fn($c) => $c->isOverdue())
                                                ->count() }}
                                        </h4>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 rounded p-1 p-md-2">
                                        <i class="fas fa-clock text-warning fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected/Cancelled -->
                    <div class="col">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-2 p-md-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted text-uppercase small mb-1 d-block">Rejected</small>
                                        <h4 class="mb-0 fw-bold text-secondary fs-5 fs-md-4">
                                            {{ $checkouts->where('status', 'rejected')->count() + 
                                               $checkouts->where('status', 'cancelled')->count() }}
                                        </h4>
                                    </div>
                                    <div class="bg-secondary bg-opacity-10 rounded p-1 p-md-2">
                                        <i class="fas fa-ban text-secondary fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="card border-0 shadow-sm d-none d-md-block mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">Book Details</th>
                                        <th class="py-3">Checkout Date</th>
                                        <th class="py-3">Due Date</th>
                                        <th class="py-3">Return Date</th>
                                        <th class="py-3">Status</th>
                                        <th class="text-end px-4 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $processedCheckouts = [];
                                    @endphp
                                    
                                    @foreach($checkouts as $checkout)
                                        @php
                                            // Check for duplicates
                                            $checkoutKey = $checkout->book_id . '-' . $checkout->status . '-' . $checkout->created_at->timestamp;
                                            if(in_array($checkoutKey, $processedCheckouts)) {
                                                continue;
                                            }
                                            $processedCheckouts[] = $checkoutKey;
                                            
                                            // Determine row class
                                            $rowClass = '';
                                            if ($checkout->status === 'rejected' || $checkout->status === 'cancelled') {
                                                $rowClass = 'table-danger';
                                            } elseif ($checkout->wasReturnedOverdue()) {
                                                $rowClass = 'table-warning';
                                            } elseif ($checkout->status === 'returned' && !$checkout->wasReturnedOverdue()) {
                                                $rowClass = 'table-success';
                                            } elseif ($checkout->isOverdue() && $checkout->status === 'checked_out') {
                                                $rowClass = 'table-danger';
                                            }
                                        @endphp
                                        <tr class="{{ $rowClass }} checkout-row">
                                            <td class="px-4">
                                                <div class="d-flex align-items-center">
                                                    @if($checkout->book->image)
                                                        <img src="{{ asset('storage/' . $checkout->book->image) }}" 
                                                             alt="{{ $checkout->book->title }}"
                                                             class="rounded me-3" 
                                                             style="width: 50px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                             style="width: 50px; height: 60px;">
                                                            <i class="fas fa-book text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-1 fw-bold text-dark">{{ $checkout->book->title ?? 'N/A' }}</h6>
                                                        <small class="text-muted d-block">by {{ $checkout->book->author ?? 'Unknown Author' }}</small>
                                                        <small class="text-muted">ISBN: {{ $checkout->book->isbn ?? 'N/A' }}</small>
                                                        @if($checkout->rejection_reason)
                                                            <br>
                                                            <small class="text-danger mt-1 d-block">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                {{ $checkout->rejection_reason }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($checkout->checkout_date)
                                                    <div class="fw-semibold">{{ $checkout->checkout_date->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $checkout->checkout_date->format('g:i A') }}</small>
                                                @elseif($checkout->created_at)
                                                    <div class="fw-semibold">{{ $checkout->created_at->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $checkout->created_at->format('g:i A') }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($checkout->due_date)
                                                    <div class="fw-semibold {{ $checkout->wasReturnedOverdue() ? 'text-danger' : '' }}">
                                                        {{ $checkout->due_date->format('M d, Y') }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($checkout->return_date)
                                                    <div class="fw-semibold {{ $checkout->wasReturnedOverdue() ? 'text-danger' : 'text-success' }}">
                                                        {{ $checkout->return_date->format('M d, Y') }}
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $checkout->return_date->format('g:i A') }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">Not returned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($checkout->status === 'checked_out')
                                                    @if($checkout->isOverdue())
                                                        <!-- Currently Overdue: Red badge -->
                                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                                                        </span>
                                                        <small class="text-danger d-block mt-1">
                                                            {{ $checkout->daysOverdue() }} days overdue
                                                        </small>
                                                    @else
                                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                                            <i class="fas fa-clock me-1"></i>Checked Out
                                                        </span>
                                                    @endif
                                                @elseif($checkout->status === 'returned')
                                                    @if($checkout->wasReturnedOverdue())
                                                        <!-- Returned Overdue: Orange/Warning badge -->
                                                        <div>
                                                            <span class="badge bg-warning rounded-pill px-3 py-2">
                                                                <i class="fas fa-exclamation-circle me-1"></i>Returned Late
                                                            </span>
                                                            <small class="text-danger d-block mt-1">
                                                                {{ $checkout->daysReturnedOverdue() }} days overdue
                                                            </small>
                                                        </div>
                                                    @else
                                                        <!-- Returned On Time: Green badge -->
                                                        <div>
                                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                                <i class="fas fa-check me-1"></i>Returned
                                                            </span>
                                                            <small class="text-muted d-block mt-1">
                                                                On time
                                                            </small>
                                                        </div>
                                                    @endif
                                                @elseif($checkout->status === 'approved')
                                                    <div>
                                                        <span class="badge bg-info rounded-pill px-3 py-2">
                                                            <i class="fas fa-check-circle me-1"></i>Approved
                                                        </span>
                                                        <small class="text-muted d-block mt-1">Ready for pickup</small>
                                                    </div>
                                                @elseif($checkout->status === 'pending')
                                                    <div>
                                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                            <i class="fas fa-clock me-1"></i>Pending
                                                        </span>
                                                        <small class="text-muted d-block mt-1">Waiting approval</small>
                                                    </div>
                                                @elseif($checkout->status === 'rejected')
                                                    <div>
                                                        <!-- Rejected: Red badge -->
                                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                                            <i class="fas fa-ban me-1"></i>Rejected
                                                        </span>
                                                        @if($checkout->updated_at)
                                                            <small class="text-muted d-block mt-1">
                                                                {{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y h:i A') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @elseif($checkout->status === 'cancelled')
                                                    <div>
                                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                            <i class="fas fa-times me-1"></i>Cancelled
                                                        </span>
                                                        @if($checkout->updated_at)
                                                            <small class="text-muted d-block mt-1">
                                                                {{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y h:i A') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-secondary rounded-pill px-3 py-2">{{ ucfirst($checkout->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end px-4">
                                                @if($checkout->status === 'checked_out')
                                                    @php
                                                        $daysRemaining = now()->startOfDay()->diffInDays($checkout->due_date->startOfDay(), false);
                                                        $isExtended = $checkout->is_extended ?? false;
                                                        $hasPendingExtension = $checkout->hasPendingExtension();
                                                        $wasEverExtended = $checkout->is_extended;
                                                        $canRequestExtension = !$isExtended && !$hasPendingExtension && !$wasEverExtended && $daysRemaining > 1;
                                                    @endphp

                                                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                                                        <!-- Return Book Button -->
                                                        <form action="{{ route('checkouts.return', $checkout) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-primary" 
                                                                    onclick="return confirm('Are you sure you want to return {{ $checkout->book->title ?? 'this book' }}?')">
                                                                <i class="fas fa-undo me-1"></i>Return
                                                            </button>
                                                        </form>
                                                        
                                                        <!-- Extension Request Button -->
                                                        @if($canRequestExtension)
                                                            <form action="{{ route('checkouts.request-extension', $checkout) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                                        onclick="return confirm('Request 3-day extension for \"{{ $checkout->book->title ?? 'this book' }}\"? Librarian approval required.')">
                                                                    <i class="fas fa-calendar-plus me-1"></i>Extend
                                                                </button>
                                                            </form>
                                                        @elseif($hasPendingExtension)
                                                            <button class="btn btn-sm btn-outline-warning" disabled>
                                                                <i class="fas fa-clock me-1"></i>Pending
                                                            </button>
                                                        @elseif($isExtended)
                                                            <button class="btn btn-sm btn-outline-success" disabled>
                                                                <i class="fas fa-calendar-check me-1"></i>Extended
                                                            </button>
                                                        @endif
                                                    </div>
                                                @elseif($checkout->status === 'rejected')
                                                    <a href="{{ route('welcome') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-book me-1"></i>Browse Again
                                                    </a>
                                                    @if($checkout->hasUnpaidFine())
                                                        <small class="text-danger d-block mt-1">
                                                            <i class="fas fa-money-bill-wave me-1"></i>
                                                            Fine: ${{ number_format($checkout->totalFines(), 2) }}
                                                        </small>
                                                    @endif
                                                @elseif($checkout->status === 'approved')
                                                    <small class="text-info d-block">
                                                        <i class="fas fa-map-marker-alt me-1"></i>Pick up at library
                                                    </small>
                                                @elseif($checkout->status === 'pending')
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-hourglass-half me-1"></i>Waiting for approval
                                                    </small>
                                                @else
                                                    <span class="text-muted small">No actions</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @php
                        $processedCheckoutsMobile = [];
                    @endphp
                    
                    @foreach($checkouts as $checkout)
                        @php
                            // Duplicate check for mobile view
                            $checkoutKey = $checkout->book_id . '-' . $checkout->status . '-' . $checkout->created_at->timestamp;
                            if(in_array($checkoutKey, $processedCheckoutsMobile)) {
                                continue;
                            }
                            $processedCheckoutsMobile[] = $checkoutKey;
                        @endphp
                        
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-3">
                                <!-- Book Info -->
                                <div class="d-flex align-items-start mb-3">
                                    @if($checkout->book->image)
                                        <img src="{{ asset('storage/' . $checkout->book->image) }}" 
                                             alt="{{ $checkout->book->title }}"
                                             class="rounded me-3" 
                                             style="width: 60px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                             style="width: 60px; height: 80px;">
                                            <i class="fas fa-book text-muted fa-lg"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold text-dark">{{ $checkout->book->title ?? 'N/A' }}</h6>
                                        <small class="text-muted d-block">by {{ $checkout->book->author ?? 'Unknown Author' }}</small>
                                        <small class="text-muted">ISBN: {{ $checkout->book->isbn ?? 'N/A' }}</small>
                                    </div>
                                </div>

                                <!-- Dates -->
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Checkout Date</small>
                                        @if($checkout->checkout_date)
                                            <div class="fw-semibold">{{ $checkout->checkout_date->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $checkout->checkout_date->format('g:i A') }}</small>
                                        @elseif($checkout->created_at)
                                            <div class="fw-semibold">{{ $checkout->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $checkout->created_at->format('g:i A') }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Due Date</small>
                                        @if($checkout->due_date)
                                            <div class="fw-semibold {{ $checkout->wasReturnedOverdue() ? 'text-danger' : '' }}">
                                                {{ $checkout->due_date->format('M d, Y') }}
                                            </div>
                                            <small class="text-muted">{{ $checkout->due_date->format('g:i A') }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Return Date -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <small class="text-muted d-block">Return Date</small>
                                        @if($checkout->return_date)
                                            <div class="fw-semibold {{ $checkout->wasReturnedOverdue() ? 'text-danger' : 'text-success' }}">
                                                {{ $checkout->return_date->format('M d, Y') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $checkout->return_date->format('g:i A') }}
                                                @if($checkout->wasReturnedOverdue())
                                                    <br>
                                                    <span class="text-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        {{ $checkout->daysReturnedOverdue() }} days late
                                                    </span>
                                                @endif
                                            </small>
                                        @else
                                            <span class="text-muted">Not returned</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    @if($checkout->status === 'checked_out')
                                        @if($checkout->isOverdue())
                                            <!-- Currently Overdue: Red badge -->
                                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Overdue
                                            </span>
                                            <small class="text-danger d-block mt-1">
                                                {{ $checkout->daysOverdue() }} days overdue
                                            </small>
                                        @else
                                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                                <i class="fas fa-clock me-1"></i>Checked Out
                                            </span>
                                        @endif
                                    @elseif($checkout->status === 'returned')
                                        @if($checkout->wasReturnedOverdue())
                                            <!-- Returned Overdue: Orange/Warning badge -->
                                            <span class="badge bg-warning rounded-pill px-3 py-2">
                                                <i class="fas fa-exclamation-circle me-1"></i>Returned Late
                                            </span>
                                            <small class="text-danger d-block mt-1">
                                                {{ $checkout->daysReturnedOverdue() }} days overdue
                                            </small>
                                        @else
                                            <!-- Returned On Time: Green badge -->
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-check me-1"></i>Returned
                                            </span>
                                            <small class="text-muted d-block mt-1">
                                                On time
                                            </small>
                                        @endif
                                    @elseif($checkout->status === 'approved')
                                        <span class="badge bg-info rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Approved
                                        </span>
                                        <small class="text-muted d-block mt-1">Ready for pickup</small>
                                    @elseif($checkout->status === 'pending')
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                        <small class="text-muted d-block mt-1">Waiting approval</small>
                                    @elseif($checkout->status === 'rejected')
                                        <!-- Rejected: Red badge -->
                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                            <i class="fas fa-ban me-1"></i>Rejected
                                        </span>
                                        @if($checkout->rejection_reason)
                                            <small class="text-muted d-block mt-1">{{ Str::limit($checkout->rejection_reason, 30) }}</small>
                                        @endif
                                    @elseif($checkout->status === 'cancelled')
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="fas fa-times me-1"></i>Cancelled
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">{{ ucfirst($checkout->status) }}</span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                @if($checkout->status === 'checked_out')
                                    @php
                                        $daysRemaining = now()->startOfDay()->diffInDays($checkout->due_date->startOfDay(), false);
                                        $isExtended = $checkout->is_extended ?? false;
                                        $hasPendingExtension = $checkout->hasPendingExtension();
                                        $wasEverExtended = $checkout->is_extended;
                                        $canRequestExtension = !$isExtended && !$hasPendingExtension && !$wasEverExtended && $daysRemaining > 1;
                                    @endphp

                                    <div class="d-flex gap-2">
                                        <!-- Return Book Button -->
                                        <form action="{{ route('checkouts.return', $checkout) }}" method="POST" class="flex-fill">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary btn-sm w-100" 
                                                    onclick="return confirm('Are you sure you want to return {{ $checkout->book->title ?? 'this book' }}?')">
                                                <i class="fas fa-undo me-1"></i>Return
                                            </button>
                                        </form>
                                        
                                        <!-- Extension Request Button -->
                                        @if($canRequestExtension)
                                            <form action="{{ route('checkouts.request-extension', $checkout) }}" method="POST" class="flex-fill">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning btn-sm w-100"
                                                        onclick="return confirm('Request 3-day extension for \"{{ $checkout->book->title ?? 'this book' }}\"? Librarian approval required.')">
                                                    <i class="fas fa-calendar-plus me-1"></i>Extend
                                                </button>
                                            </form>
                                        @elseif($hasPendingExtension)
                                            <button class="btn btn-outline-warning btn-sm w-100" disabled>
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </button>
                                        @elseif($isExtended)
                                            <button class="btn btn-outline-success btn-sm w-100" disabled>
                                                <i class="fas fa-calendar-check me-1"></i>Extended
                                            </button>
                                        @endif
                                    </div>
                                @elseif($checkout->status === 'rejected')
                                    <a href="{{ route('welcome') }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-book me-1"></i>Browse Again
                                    </a>
                                @elseif($checkout->wasReturnedOverdue())
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Returned {{ $checkout->daysReturnedOverdue() }} days late
                                        @if($checkout->hasUnpaidFine())
                                            <br>
                                            <i class="fas fa-money-bill-wave me-1"></i>
                                            Fine: ${{ number_format($checkout->totalFines(), 2) }}
                                        @endif
                                    </div>
                                @elseif($checkout->status === 'approved')
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-map-marker-alt me-1"></i>Pick up at library
                                    </div>
                                @elseif($checkout->status === 'pending')
                                    <div class="alert alert-light mb-0">
                                        <i class="fas fa-hourglass-half me-1"></i>Waiting for approval
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINATION -->
                @if($checkouts->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <!-- Pagination -->
                    <nav aria-label="Checkout history pagination">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            <li class="page-item {{ $checkouts->onFirstPage() ? 'disabled' : '' }}">
                                @if($checkouts->onFirstPage())
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a class="page-link" href="{{ $checkouts->previousPageUrl() }}" aria-label="Previous">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif
                            </li>
                            
                            {{-- Page Numbers --}}
                            @php
                                $current = $checkouts->currentPage();
                                $last = $checkouts->lastPage();
                                $start = max(1, $current - 2);
                                $end = min($last, $start + 4);
                                
                                if ($end - $start < 4 && $start > 1) {
                                    $start = max(1, $end - 4);
                                }
                            @endphp
                            
                            {{-- First Page --}}
                            @if($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $checkouts->url(1) }}">1</a>
                                </li>
                                @if($start > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif
                            
                            {{-- Page Numbers --}}
                            @for($i = $start; $i <= $end; $i++)
                                <li class="page-item {{ $i == $current ? 'active' : '' }}">
                                    @if($i == $current)
                                        <span class="page-link">{{ $i }}</span>
                                    @else
                                        <a class="page-link" href="{{ $checkouts->url($i) }}">{{ $i }}</a>
                                    @endif
                                </li>
                            @endfor
                            
                            {{-- Last Page --}}
                            @if($end < $last)
                                @if($end < $last - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $checkouts->url($last) }}">{{ $last }}</a>
                                </li>
                            @endif
                            
                            {{-- Next Page Link --}}
                            <li class="page-item {{ !$checkouts->hasMorePages() ? 'disabled' : '' }}">
                                @if($checkouts->hasMorePages())
                                    <a class="page-link" href="{{ $checkouts->nextPageUrl() }}" aria-label="Next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="page-link">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                @endif
                            </li>
                        </ul>
                    </nav>
                </div>
                @endif

            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-history fa-4x text-muted opacity-50"></i>
                        </div>
                        <h3 class="text-muted mb-3">No Checkout History</h3>
                        <p class="text-muted mb-4">You haven't checked out any books yet.</p>
                        <a href="{{ route('welcome') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-book me-2"></i>Browse Books
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .checkout-row:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .card {
        border-radius: 12px;
    }
    
    /* Badge Colors */
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    
    .badge.bg-success {
        background-color: #198754 !important;
    }
    
    .badge.bg-info {
        background-color: #0dcaf0 !important;
        color: #000 !important;
    }
    
    /* Pagination Styles */
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    
    .pagination .page-link {
        color: #0d6efd;
        border-radius: 6px;
        margin: 0 2px;
        min-width: 36px;
        text-align: center;
        padding: 0.375rem 0.75rem;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        color: #0a58ca;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #f8f9fa;
    }
    
    .pagination .page-item:first-child .page-link {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
    }
    
    .pagination .page-item:last-child .page-link {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .btn-sm {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
        }
        
        .pagination .page-link {
            min-width: 32px;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    }
</style>
@endpush