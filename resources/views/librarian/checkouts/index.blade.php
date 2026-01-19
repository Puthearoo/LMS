@extends('layouts.librarian')

@section('title', 'My Checkouts')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">My Checked Out Books</h1>
            <p class="text-muted">Manage your current loans and track due dates</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2">Currently Checked Out</h6>
                            <h4 class="mb-0 fw-bold text-primary">{{ $checkouts->where('status', 'checked_out')->count() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded p-2">
                            <i class="fas fa-book text-primary fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2">Pending Approval</h6>
                            <h4 class="mb-0 fw-bold text-warning">{{ $checkouts->where('status', 'pending')->count() }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded p-2">
                            <i class="fas fa-clock text-warning fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2">Ready for Pickup</h6>
                            <h4 class="mb-0 fw-bold text-info">{{ $checkouts->where('status', 'approved')->count() }}</h4>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded p-2">
                            <i class="fas fa-check-circle text-info fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2">Overdue</h6>
                            <h4 class="mb-0 fw-bold text-danger">{{ $checkouts->where('status', 'checked_out')->where('due_date', '<', now())->count() }}</h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded p-2">
                            <i class="fas fa-exclamation-triangle text-danger fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkouts Table -->
    @if($checkouts->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                    <h5 class="mb-0">Your Checkouts</h5>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i>Filter by Status
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All Checkouts</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending Approval</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}">Ready for Pickup</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'checked_out']) }}">Checked Out</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'overdue']) }}">Overdue</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'returned']) }}">Returned</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'cancelled']) }}">Cancelled</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Rejected</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Desktop Table -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 px-md-4 py-3" style="width: 320px;">Book</th>
                                    <th style="width: 120px;">Checkout Date</th>
                                    <th style="width: 120px;">Due Date</th>
                                    <th style="width: 150px;">Status</th>
                                    <th style="width: 150px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checkouts as $checkout)
                                    @php
                                        // Parse dates safely using Carbon
                                        $dueDate = $checkout->due_date ? \Carbon\Carbon::parse($checkout->due_date) : null;
                                        $checkoutDate = $checkout->checkout_date ? \Carbon\Carbon::parse($checkout->checkout_date) : null;
                                        $returnDate = $checkout->return_date ? \Carbon\Carbon::parse($checkout->return_date) : null;
                                        
                                        // Calculate whole days remaining (ignore time component)
                                        $daysRemaining = $dueDate ? now()->startOfDay()->diffInDays($dueDate->startOfDay(), false) : null;
                                        
                                        // Determine status based on your business logic
                                        $statusClass = 'success';
                                        $statusText = '';
                                        $statusIcon = 'fa-check-circle';
                                        $showActions = false;
                                        $canChangeStatus = false;
                                        
                                        if ($checkout->status === 'pending') {
                                            $statusClass = 'warning';
                                            $statusText = 'Pending Approval';
                                            $statusIcon = 'fa-clock';
                                            $canChangeStatus = true;
                                        } elseif ($checkout->status === 'approved') {
                                            $statusClass = 'info';
                                            $statusText = 'Approved - Ready for Pickup';
                                            $statusIcon = 'fa-check-circle';
                                            $canChangeStatus = true;
                                        } elseif ($checkout->status === 'checked_out') {
                                            $showActions = true;
                                            if ($daysRemaining < 0) {
                                                $statusClass = 'danger';
                                                $statusText = 'Overdue';
                                                $statusIcon = 'fa-exclamation-triangle';
                                            } elseif ($daysRemaining === 0) {
                                                $statusClass = 'warning';
                                                $statusText = 'Due Today';
                                                $statusIcon = 'fa-exclamation-circle';
                                            } else {
                                                $statusClass = 'success';
                                                $statusText = 'On Track';
                                                $statusIcon = 'fa-check-circle';
                                            }
                                        } elseif ($checkout->status === 'returned') {
                                            $statusClass = 'secondary';
                                            $statusText = 'Returned';
                                            $statusIcon = 'fa-check-double';
                                        } elseif ($checkout->status === 'rejected') {
                                            $statusClass = 'danger';
                                            $statusText = 'Rejected';
                                            $statusIcon = 'fa-ban';
                                        } elseif ($checkout->status === 'cancelled') {
                                            $statusClass = 'secondary';
                                            $statusText = 'Cancelled';
                                            $statusIcon = 'fa-times-circle';
                                        } elseif ($checkout->status === 'overdue') {
                                            $statusClass = 'danger';
                                            $statusText = 'Overdue';
                                            $statusIcon = 'fa-exclamation-triangle';
                                            $showActions = true;
                                        }
                                        
                                        // Check if extended (if due date is more than 14 days from checkout)
                                        $originalDueDate = $checkoutDate ? $checkoutDate->copy()->addDays(14) : null;
                                        $isExtended = $dueDate && $originalDueDate ? $dueDate->gt($originalDueDate) : false;
                                        
                                        // FIXED: Correct logic for $canExtend
                                        // Check if checkout can be extended by librarian
                                        $canExtend = false;
                                        if ($checkout->status === 'checked_out' && 
                                            $dueDate && 
                                            !$checkout->return_date &&
                                            !$checkout->extension_requested &&
                                            $checkout->extension_status !== 'approved') {
                                            // Librarians can extend if:
                                            // 1. Book is checked out
                                            // 2. Has a due date
                                            // 3. Not returned
                                            // 4. No pending extension request
                                            // 5. Not already extended
                                            $canExtend = true;
                                        }
                                    @endphp
                                    
                                    <tr class="checkout-row">
                                        <td class="px-3 px-md-4">
                                            <div class="d-flex align-items-center">
                                                <!-- Enhanced Book Cover Card -->
                                                <div class="book-cover-card me-3">
                                                    @if($checkout->book->image)
                                                        <div class="book-cover-image-container">
                                                            <img src="{{ asset('storage/' . $checkout->book->image) }}" 
                                                                alt="{{ $checkout->book->title }}" 
                                                                class="img-thumbnail me-3" style="width: 150px; height: 200px; object-fit: cover;">
                                                        </div>
                                                    @else
                                                        <div class="book-cover-placeholder">
                                                            <i class="fas fa-book"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="book-info">
                                                    <h6 class="mb-1 fw-semibold">{{ $checkout->book->title }}</h6>
                                                    <small class="text-muted d-block">by {{ $checkout->book->author }}</small>
                                                    <small class="text-muted d-block mb-1">ISBN: {{ $checkout->book->isbn }}</small>
                                                    
                                                    @if($checkout->user)
                                                        <div class="borrower-info mt-2 pt-2 border-top border-light">
                                                        <p class="mb-1">Borrowed By</p>
                                                        <div class="mb-2">
                                                            <div class="text-primary fw-semibold">
                                                                <i class="fas fa-user fa-xs me-1"></i> {{ $checkout->user->name }}
                                                            </div>
                                                            <div class="text-muted small">
                                                                <i class="fas fa-phone fa-xs me-1"></i> {{ $checkout->user->contact }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($isExtended)
                                                        <span class="badge bg-info bg-opacity-10 text-info mt-1 d-inline-block">
                                                            <i class="fas fa-redo fa-xs"></i> Extended
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            {{-- Date --}}
                                            <div class="d-flex flex-column justify-content-center" style="min-height: 70px;">
                                                @if($checkoutDate)
                                                    <div class="fw-semibold">{{ $checkoutDate->format('M d, Y') }}</div>
                                                    <div class="text-muted">{{ $checkoutDate->format('h:i A') }}</div>
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column justify-content-center" style="min-height: 70px;">
                                                @if($dueDate)
                                                    <div class="fw-semibold">{{ $dueDate->format('M d, Y') }}</div>

                                                    @if($showActions)
                                                        <small class="text-{{ $statusClass }}">
                                                            @if($daysRemaining < 0)
                                                                <i class="fas fa-exclamation-circle"></i>
                                                                {{ abs($daysRemaining) }} day{{ abs($daysRemaining) != 1 ? 's' : '' }}
                                                            @elseif($daysRemaining === 0)
                                                                <i class="fas fa-exclamation-triangle"></i> Due today
                                                            @else
                                                                <i class="fas fa-clock"></i>
                                                                {{ $daysRemaining }} day{{ $daysRemaining != 1 ? 's' : '' }} left
                                                            @endif
                                                        </small>
                                                    @endif

                                                    @if($isExtended)
                                                        <small class="text-muted">
                                                            <span class="d-block">Extended from</span>
                                                            <span class="d-block">{{ $originalDueDate->format('M d, Y') }}</span>
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </div>
                                        </td>                                        
                                        <td>
                                            @if($canChangeStatus)
                                                <div class="dropdown">
                                                    <button class="btn btn-sm dropdown-toggle status-dropdown badge rounded-pill bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-3 py-2 border-0" 
                                                            type="button" 
                                                            id="statusDropdown{{ $checkout->id }}" 
                                                            data-bs-toggle="dropdown" 
                                                            aria-expanded="false">
                                                        <i class="fas {{ $statusIcon }} me-1"></i>{{ $statusText }}
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown{{ $checkout->id }}">
                                                        @if($checkout->status === 'pending')
                                                            <li>
                                                                <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="approved">
                                                                    <button type="submit" class="dropdown-item text-info">
                                                                        <i class="fas fa-check-circle me-2"></i>Approve for Pickup
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="rejected">
                                                                    <button type="submit" class="dropdown-item text-danger" 
                                                                            onclick="return confirm('Are you sure you want to reject this checkout request?')">
                                                                        <i class="fas fa-ban me-2"></i>Reject Request
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="cancelled">
                                                                    <button type="submit" class="dropdown-item text-secondary" 
                                                                            onclick="return confirm('Are you sure you want to cancel this checkout?')">
                                                                        <i class="fas fa-times-circle me-2"></i>Cancel Checkout
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @elseif($checkout->status === 'approved')
                                                            <li>
                                                                <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="checked_out">
                                                                    <button type="submit" class="dropdown-item text-success">
                                                                        <i class="fas fa-book me-2"></i>Mark as Checked Out
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="pending">
                                                                    <button type="submit" class="dropdown-item text-warning"
                                                                            onclick="return confirm('Revert to pending?\nBook: {{ $checkout->book->title }}\nUser: {{ $checkout->user->name }}')">
                                                                        <i class="fas fa-undo me-2"></i>Revert to Pending
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="cancelled">
                                                                    <button type="submit" class="dropdown-item text-secondary" 
                                                                            onclick="return confirm('Are you sure you want to cancel this checkout?')">
                                                                        <i class="fas fa-times-circle me-2"></i>Cancel Checkout
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            @else
                                                <span class="badge rounded-pill bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-3 py-2">
                                                    <i class="fas {{ $statusIcon }} me-1"></i>{{ $statusText }}
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-3 px-md-4">
                                            @if($showActions)
                                                <div class="d-flex gap-2 justify-content-end flex-wrap">
                                                    <form action="{{ route('librarian.checkouts.return', $checkout->id) }}" 
                                                        method="POST" 
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to return \"{{ $checkout->book->title }}\"?')">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-undo me-1"></i>Return Book
                                                        </button>
                                                    </form>
                                                    
                                                    @if($canExtend)
                                                        <form action="{{ route('librarian.checkouts.extend', $checkout->id) }}" 
                                                            method="POST" 
                                                            class="d-inline"
                                                            onsubmit="return confirm('Extend loan for \"{{ $checkout->book->title }}\" by 3 days?')">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-clock me-1"></i>Extend Loan
                                                            </button>
                                                        </form>
                                                    @elseif($isExtended || $checkout->extension_status === 'approved')
                                                        <button class="btn btn-sm btn-secondary" disabled>
                                                            <i class="fas fa-check me-1"></i> Extended
                                                        </button>
                                                    @elseif($checkout->extension_requested && $checkout->extension_status === 'pending')
                                                        <button class="btn btn-sm btn-warning" disabled>
                                                            <i class="fas fa-clock me-1"></i> Pending 
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-center">
                                                    @if($checkout->status === 'pending')
                                                        <span class="text-muted small">
                                                            <i class="fas fa-hourglass-half me-1"></i>Waiting
                                                        </span>
                                                    @elseif($checkout->status === 'approved')
                                                        <span class="text-muted small">
                                                            <i class="fas fa-map-marker-alt me-1"></i>Library
                                                        </span>
                                                    @elseif($checkout->status === 'returned')
                                                        <span class="text-muted small">
                                                            <i class="fas fa-check-double me-2"></i>Returned on 
                                                            <div class="d-block fw-semibold">
                                                                @if($returnDate)
                                                                    {{ $returnDate->format('M d, Y') }}<br>
                                                                    {{ $returnDate->format('h:i A') }}
                                                                @elseif($checkout->updated_at)
                                                                    {{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y') }}<br>
                                                                    {{ \Carbon\Carbon::parse($checkout->updated_at)->format('h:i A') }}
                                                                @endif
                                                            </div>
                                                        </span>
                                                    @elseif($checkout->status === 'rejected')
                                                        <span class="text-muted small">
                                                            <i class="fas fa-ban me-2"></i>Rejected on 
                                                            <div class="d-block fw-semibold">
                                                                @if($checkout->updated_at)
                                                                    {{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y') }}<br>
                                                                    {{ \Carbon\Carbon::parse($checkout->updated_at)->format('h:i A') }}
                                                                @else
                                                                    {{ \Carbon\Carbon::now()->format('M d, Y') }}<br>
                                                                    {{ \Carbon\Carbon::now()->format('h:i A') }}
                                                                @endif
                                                            </div>
                                                        </span>
                                                    @elseif($checkout->status === 'cancelled')
                                                        <span class="text-muted small">
                                                            <i class="fas fa-times-circle me-2"></i>Cancelled on 
                                                            <div class="d-block fw-semibold">
                                                                @if($checkout->updated_at)
                                                                    {{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y') }}<br>
                                                                    {{ \Carbon\Carbon::parse($checkout->updated_at)->format('h:i A') }}
                                                                @else
                                                                    {{ \Carbon\Carbon::now()->format('M d, Y') }}<br>
                                                                    {{ \Carbon\Carbon::now()->format('h:i A') }}
                                                                @endif
                                                            </div>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($checkouts->hasPages())
                        <div class="card-footer bg-white py-3 border-top">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <!-- Results Count -->
                                <div class="mb-2 mb-md-0">
                                    <p class="text-muted small mb-0">
                                        Showing {{ $checkouts->firstItem() ?? 0 }} to {{ $checkouts->lastItem() ?? 0 }} 
                                        of {{ $checkouts->total() }} checkouts
                                    </p>
                                </div>
                                
                                <!-- Pagination Links -->
                                <nav aria-label="Checkouts navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        {{-- Previous Page Link --}}
                                        <li class="page-item {{ $checkouts->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $checkouts->previousPageUrl() }}" 
                                            aria-label="Previous" {{ $checkouts->onFirstPage() ? 'tabindex="-1"' : '' }}>
                                                <i class="fas fa-chevron-left"></i>
                                                <span class="d-none d-md-inline"> Previous</span>
                                            </a>
                                        </li>

                                        {{-- Page Numbers --}}
                                        @foreach ($checkouts->getUrlRange(1, $checkouts->lastPage()) as $page => $url)
                                            @if ($page == $checkouts->currentPage())
                                                <li class="page-item active" aria-current="page">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @elseif (
                                                $page == 1 || 
                                                $page == $checkouts->lastPage() || 
                                                ($page >= $checkouts->currentPage() - 2 && $page <= $checkouts->currentPage() + 2)
                                            )
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @elseif (
                                                $page == $checkouts->currentPage() - 3 || 
                                                $page == $checkouts->currentPage() + 3
                                            )
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        <li class="page-item {{ $checkouts->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $checkouts->nextPageUrl() }}" 
                                            aria-label="Next" {{ $checkouts->hasMorePages() ? '' : 'tabindex="-1"' }}>
                                                <span class="d-none d-md-inline">Next </span>
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                                
                                <!-- Items Per Page Selector -->
                                <div class="mt-2 mt-md-0">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                                id="perPageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $checkouts->perPage() }} per page
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="perPageDropdown">
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}">10 per page</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 20]) }}">20 per page</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}">50 per page</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 100]) }}">100 per page</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>

                <!-- Mobile Cards -->
                <div class="d-lg-none">
                    @foreach($checkouts as $checkout)
                        @php
                            $dueDate = $checkout->due_date ? \Carbon\Carbon::parse($checkout->due_date) : null;
                            $checkoutDate = $checkout->checkout_date ? \Carbon\Carbon::parse($checkout->checkout_date) : null;
                            $returnDate = $checkout->return_date ? \Carbon\Carbon::parse($checkout->return_date) : null;
                            
                            $originalDueDate = $checkoutDate ? $checkoutDate->copy()->addDays(14) : null;
                            $isExtended = $dueDate && $originalDueDate ? $dueDate->gt($originalDueDate) : false;
                            
                            $daysRemaining = $dueDate ? now()->startOfDay()->diffInDays($dueDate->startOfDay(), false) : null;
                            
                            // FIXED: Mobile logic for $canExtend
                            $mobileCanExtend = false;
                            if ($checkout->status === 'checked_out' && 
                                $dueDate && 
                                !$checkout->return_date &&
                                !$checkout->extension_requested &&
                                $checkout->extension_status !== 'approved') {
                                $mobileCanExtend = true;
                            }
                            
                            // Mobile status determination
                            $mobileStatusClass = 'secondary';
                            $mobileStatusText = '';
                            $mobileStatusIcon = 'fa-circle';
                            
                            if ($checkout->status === 'pending') {
                                $mobileStatusClass = 'warning';
                                $mobileStatusText = 'Pending Approval';
                                $mobileStatusIcon = 'fa-clock';
                            } elseif ($checkout->status === 'approved') {
                                $mobileStatusClass = 'info';
                                $mobileStatusText = 'Ready for Pickup';
                                $mobileStatusIcon = 'fa-check-circle';
                            } elseif ($checkout->status === 'checked_out') {
                                if ($daysRemaining < 0) {
                                    $mobileStatusClass = 'danger';
                                    $mobileStatusText = 'Overdue';
                                    $mobileStatusIcon = 'fa-exclamation-triangle';
                                } elseif ($daysRemaining === 0) {
                                    $mobileStatusClass = 'warning';
                                    $mobileStatusText = 'Due Today';
                                    $mobileStatusIcon = 'fa-exclamation-circle';
                                } else {
                                    $mobileStatusClass = 'success';
                                    $mobileStatusText = 'Checked Out';
                                    $mobileStatusIcon = 'fa-book';
                                }
                            } elseif ($checkout->status === 'returned') {
                                $mobileStatusClass = 'secondary';
                                $mobileStatusText = 'Returned';
                                $mobileStatusIcon = 'fa-check-double';
                            } elseif ($checkout->status === 'rejected') {
                                $mobileStatusClass = 'danger';
                                $mobileStatusText = 'Rejected';
                                $mobileStatusIcon = 'fa-ban';
                            } elseif ($checkout->status === 'cancelled') {
                                $mobileStatusClass = 'secondary';
                                $mobileStatusText = 'Cancelled';
                                $mobileStatusIcon = 'fa-times-circle';
                            } elseif ($checkout->status === 'overdue') {
                                $mobileStatusClass = 'danger';
                                $mobileStatusText = 'Overdue';
                                $mobileStatusIcon = 'fa-exclamation-triangle';
                            }
                        @endphp
                        
                        <div class="checkout-card">
                            <div class="card m-3 border shadow-sm">
                                <div class="card-body">
                                    <!-- Book Info with Enhanced Cover Card -->
                                    <div class="d-flex align-items-start mb-3">
                                        <!-- Enhanced Book Cover Card -->
                                        <div class="book-cover-card me-3">
                                            @if($checkout->book->image)
                                                <div class="book-cover-image-container">
                                                    <img src="{{ asset('storage/' . $checkout->book->image) }}" 
                                                        alt="{{ $checkout->book->title }}" 
                                                            class="img-thumbnail me-3" style="width: 150px; height: 200px; object-fit: cover;">
                                                </div>
                                            @else
                                                <div class="book-cover-placeholder">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="book-info flex-grow-1">
                                            <h6 class="fw-bold mb-1 text-dark">{{ $checkout->book->title }}</h6>
                                            <p class="text-muted mb-1">by {{ $checkout->book->author }}</p>
                                            <small class="text-muted d-block mb-1">ISBN: {{ $checkout->book->isbn }}</small>
                                            
                                            <!-- Borrower Information Added Here -->
                                            @if($checkout->user)
                                                <div class="mt-3">
                                                    <p class="mb-1">Borrowed By</p>
                                                    <div class="fw-semibold">
                                                        <i class="fas fa-user fa-xs me-1 text-primary"></i> {{ $checkout->user->name }}
                                                    </div>
                                                    @if($checkout->user->contact)
                                                        <div class="text-muted small">
                                                            <i class="fas fa-phone fa-xs me-1"></i> {{ $checkout->user->contact }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            @if($isExtended)
                                                <span class="badge bg-info bg-opacity-10 text-info mt-1">
                                                    <i class="fas fa-redo fa-xs me-1"></i> Extended
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Dates -->
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Checkout Date</small>
                                                @if($checkoutDate)
                                                    <strong class="d-block text-dark">{{ $checkoutDate->format('M d, Y') }}</strong>
                                                    <small class="text-muted">{{ $checkoutDate->format('h:i A') }}</small>
                                                @else
                                                    <strong class="d-block text-dark">Not set</strong>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Due Date</small>
                                                @if($dueDate)
                                                    <strong class="d-block text-dark">{{ $dueDate->format('M d, Y') }}</strong>
                                                @else
                                                    <strong class="d-block text-dark">Not set</strong>
                                                @endif
                                                @if($checkout->status === 'checked_out' && $dueDate)
                                                    <small class="text-{{ $daysRemaining < 0 ? 'danger' : ($daysRemaining === 0 ? 'warning' : 'success') }} d-block">
                                                        @if($daysRemaining < 0)
                                                            <i class="fas fa-exclamation-circle"></i> Overdue
                                                        @elseif($daysRemaining === 0)
                                                            <i class="fas fa-exclamation-triangle"></i> Due today
                                                        @else
                                                            <i class="fas fa-clock"></i> {{ $daysRemaining }} days left
                                                        @endif
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Badge with Mobile Actions -->
                                    <div class="mb-3">
                                        @if(in_array($checkout->status, ['pending', 'approved']))
                                            <div class="dropdown">
                                                <button class="btn btn-sm w-100 dropdown-toggle status-dropdown badge rounded-pill bg-{{ $mobileStatusClass }} bg-opacity-10 text-{{ $mobileStatusClass }} px-3 py-2 border-0" 
                                                        type="button" 
                                                        id="mobileStatusDropdown{{ $checkout->id }}" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                    <i class="fas {{ $mobileStatusIcon }} me-1"></i>{{ $mobileStatusText }}
                                                </button>
                                                <ul class="dropdown-menu w-100" aria-labelledby="mobileStatusDropdown{{ $checkout->id }}">
                                                    @if($checkout->status === 'pending')
                                                        <li>
                                                            <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="approved">
                                                                <button type="submit" class="dropdown-item text-info">
                                                                    <i class="fas fa-check-circle me-2"></i>Approve for Pickup
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="rejected">
                                                                <button type="submit" class="dropdown-item text-danger" 
                                                                        onclick="return confirm('Are you sure you want to reject this checkout request?')">
                                                                    <i class="fas fa-ban me-2"></i>Reject Request
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" class="dropdown-item text-secondary" 
                                                                        onclick="return confirm('Are you sure you want to cancel this checkout?')">
                                                                    <i class="fas fa-times-circle me-2"></i>Cancel Checkout
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @elseif($checkout->status === 'approved')
                                                        <li>
                                                            <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="checked_out">
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="fas fa-book me-2"></i>Mark as Checked Out
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="pending">
                                                                <button type="submit" class="dropdown-item text-warning"
                                                                        onclick="return confirm('Revert to pending?\nBook: {{ $checkout->book->title }}\nUser: {{ $checkout->user->name }}')">
                                                                    <i class="fas fa-undo me-2"></i>Revert to Pending
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('librarian.checkouts.update-status', $checkout->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="cancelled">
                                                                <button type="submit" class="dropdown-item text-secondary" 
                                                                        onclick="return confirm('Are you sure you want to cancel this checkout?')">
                                                                    <i class="fas fa-times-circle me-2"></i>Cancel Checkout
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @else
                                            <span class="badge bg-{{ $mobileStatusClass }} bg-opacity-10 text-{{ $mobileStatusClass }} px-3 py-2">
                                                <i class="fas {{ $mobileStatusIcon }} me-1"></i>{{ $mobileStatusText }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Mobile Actions for Checked Out Status -->
                                    @if($checkout->status === 'checked_out')
                                        <div class="d-grid gap-2">
                                            <form action="{{ route('librarian.checkouts.return', $checkout->id) }}" 
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to return \"{{ $checkout->book->title }}\"?')">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                                    <i class="fas fa-undo me-1"></i>Return Book
                                                </button>
                                            </form>
                                            
                                            @if($mobileCanExtend)
                                                <form action="{{ route('librarian.checkouts.extend', $checkout->id) }}" 
                                                    method="POST"
                                                    onsubmit="return confirm('Extend loan for \"{{ $checkout->book->title }}\" by 3 days?')">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                                        <i class="fas fa-clock me-1"></i>Extend Loan
                                                    </button>
                                                </form>
                                            @elseif($isExtended || $checkout->extension_status === 'approved')
                                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                                    <i class="fas fa-check me-1"></i> Extended
                                                </button>
                                            @elseif($checkout->extension_requested && $checkout->extension_status === 'pending')
                                                <button class="btn btn-warning btn-sm w-100" disabled>
                                                    <i class="fas fa-clock me-1"></i> Extension Requested
                                                </button>
                                            @endif
                                        </div>
                                    @elseif(!in_array($checkout->status, ['pending', 'approved']))
                                        <!-- Info messages for returned, rejected, cancelled statuses -->
                                        <div class="text-center py-2">
                                            @if($checkout->status === 'pending')
                                                <small class="text-muted">
                                                    <i class="fas fa-hourglass-half me-1"></i>Waiting for librarian approval
                                                </small>
                                            @elseif($checkout->status === 'approved')
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>Ready for pickup at library
                                                </small>
                                           @elseif($checkout->status === 'returned')
                                                <small class="text-muted">
                                                    <i class="fas fa-check-double me-1"></i>Returned on 
                                                    @if($returnDate)
                                                        <span class="d-none d-md-inline">{{ $returnDate->format('M d, Y h:i A') }}</span>
                                                        <span class="d-inline d-md-none">
                                                            {{ $returnDate->format('M d, Y') }}<br>
                                                            {{ $returnDate->format('h:i A') }}
                                                        </span>
                                                    @elseif($checkout->updated_at)
                                                        <span class="d-none d-md-inline">{{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y h:i A') }}</span>
                                                        <span class="d-inline d-md-none">
                                                            {{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y') }}<br>
                                                            {{ \Carbon\Carbon::parse($checkout->updated_at)->format('h:i A') }}
                                                        </span>
                                                    @endif
                                                </small>
                                            @elseif($checkout->status === 'rejected')
                                                <small class="text-muted">
                                                    <i class="fas fa-ban me-2"></i>Rejected on 
                                                    @if($checkout->updated_at)
                                                        <span class="d-none d-md-inline">{{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y h:i A') }}</span>
                                                        <span class="d-inline d-md-none">
                                                            {{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y') }}<br>
                                                            {{ \Carbon\Carbon::parse($checkout->updated_at)->format('h:i A') }}
                                                        </span>
                                                    @else
                                                        <span class="d-none d-md-inline">{{ \Carbon\Carbon::now()->format('M d, Y h:i A') }}</span>
                                                        <span class="d-inline d-md-none">
                                                            {{ \Carbon\Carbon::now()->format('M d, Y') }}<br>
                                                            {{ \Carbon\Carbon::now()->format('h:i A') }}
                                                        </span>
                                                    @endif
                                                </small>
                                            @elseif($checkout->status === 'cancelled')
                                                <small class="text-muted">
                                                    <i class="fas fa-times-circle me-2"></i>Cancelled on 
                                                    @if($checkout->updated_at)
                                                        <span class="d-none d-md-inline">{{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y h:i A') }}</span>
                                                        <span class="d-inline d-md-none">
                                                            {{ \Carbon\Carbon::parse($checkout->updated_at)->format('M d, Y') }}<br>
                                                            {{ \Carbon\Carbon::parse($checkout->updated_at)->format('h:i A') }}
                                                        </span>
                                                    @else
                                                        <span class="d-none d-md-inline">{{ \Carbon\Carbon::now()->format('M d, Y h:i A') }}</span>
                                                        <span class="d-inline d-md-none">
                                                            {{ \Carbon\Carbon::now()->format('M d, Y') }}<br>
                                                            {{ \Carbon\Carbon::now()->format('h:i A') }}
                                                        </span>
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div> {{-- Close d-lg-none for mobile cards --}}
                {{-- Mobile Pagination (show only on mobile) --}}
                @if($checkouts->hasPages())
                    <div class="d-lg-none">
                            <div class="card m-3 border shadow-sm">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-start">
                                            @if($checkouts->onFirstPage())
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="fas fa-chevron-left me-1"></i> Prev
                                                </button>
                                            @else
                                                <a href="{{ $checkouts->previousPageUrl() }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-chevron-left me-1"></i> Prev
                                                </a>
                                            @endif
                                        </div>
                                        
                                        <div class="col-4 text-center">
                                            <small class="text-muted">
                                                Page {{ $checkouts->currentPage() }} of {{ $checkouts->lastPage() }}
                                            </small>
                                        </div>
                                        
                                        <div class="col-4 text-end">
                                            @if($checkouts->hasMorePages())
                                                <a href="{{ $checkouts->nextPageUrl() }}" class="btn btn-sm btn-outline-primary">
                                                    Next <i class="fas fa-chevron-right ms-1"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    Next <i class="fas fa-chevron-right ms-1"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Results Count for Mobile -->
                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            Showing {{ $checkouts->firstItem() ?? 0 }}-{{ $checkouts->lastItem() ?? 0 }} of {{ $checkouts->total() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endif
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-book-open text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
                <h4 class="mb-2">No Books Checked Out</h4>
                <p class="text-muted mb-4">You don't have any books checked out at the moment.</p>
                <a href="{{ route('librarian.books.index') }}" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Browse Books
                </a>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    /* Enhanced Book Cover Card Styles - Matching Reference Design */
    .book-cover-card {
        min-width: 60px;
        width: 60px;
        height: 90px;
        flex-shrink: 0;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 
            0 2px 4px rgba(0,0,0,0.1),
            0 4px 8px rgba(0,0,0,0.08),
            0 0 0 1px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: none;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .book-cover-image-container {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f8f9fa;
    }
    
    .book-cover-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }
    
    .book-cover-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }
    
    .book-info {
        max-width: 220px;
        white-space: normal;
    }
    
    /* Table row hover effect */
    .checkout-row:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        
        /* Larger book cards on mobile for better visibility */
        .book-cover-card {
            width: 80px;
            height: 110px;
        }
        
        .book-cover-placeholder {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 576px) {
        .book-cover-card {
            width: 70px;
            height: 100px;
        }
        
        .book-cover-placeholder {
            font-size: 1.8rem;
        }
    }
    /* Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        border-color: #dee2e6;
        color: #495057;
        min-width: 38px;
        text-align: center;
    }

    .page-link:hover {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
    }

    /* Mobile pagination */
    @media (max-width: 768px) {
        .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .pagination-sm .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>
@endpush
@endsection