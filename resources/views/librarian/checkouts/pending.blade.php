@extends('layouts.librarian')

@section('title', 'Pending Checkout Requests')

@section('content')
<div class="container-fluid py-3 py-md-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4">
                <h1 class="h4 h3-md mb-2 mb-sm-0 text-nowrap">
                    <i class="bi bi-clock text-warning me-2"></i>Pending Checkout Requests
                </h1>
                <div class="d-flex flex-wrap gap-2 justify-content-end w-100 w-sm-auto">
                    <a href="{{ route('librarian.checkouts.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-list me-1"></i> All Checkouts
                    </a>
                    <a href="{{ route('librarian.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </div>
            </div>

            @if($pendingCheckouts->count() > 0)
                <!-- Desktop Table View -->
                <div class="card border-0 shadow-sm d-none d-md-block">
                    <div class="card-header bg-warning text-dark py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fs-6">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ $pendingCheckouts->total() }} Pending Requests Awaiting Approval
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="20%">Borrowed by</th>
                                        <th width="30%">Book Details</th>
                                        <th width="15%">Request Date</th>
                                        <th width="15%">Due Date</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingCheckouts as $checkout)
                                        <tr class="align-middle">
                                            <td>
                                                <!-- Student Info - NO AVATAR -->
                                                <div>
                                                    <strong class="d-block fs-6">{{ $checkout->user->name }}</strong>
                                                    <small class="text-muted d-block">{{ $checkout->user->email }}</small>
                                                    <small class="text-muted">ID: {{ $checkout->user->id }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-start">
                                                    @if($checkout->book->image)
                                                        <img src="{{ asset('storage/' . $checkout->book->image) }}" 
                                                            alt="{{ $checkout->book->title }}" 
                                                            class="img-thumbnail me-3" style="width: 50px; height: 70px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center me-3 border" 
                                                            style="width: 50px; height: 70px;">
                                                            <i class="bi bi-book text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div class="flex-grow-1">
                                                        <strong class="d-block fs-6">{{ $checkout->book->title }}</strong>
                                                        <small class="text-muted d-block">by {{ $checkout->book->author }}</small>
                                                        <small class="text-muted d-block">ISBN: {{ $checkout->book->isbn }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $createdAt = \Carbon\Carbon::parse($checkout->created_at)->setTimezone('Asia/Phnom_Penh');
                                                @endphp
                                                    <strong class="fs-6">{{ $createdAt->format('M j, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $createdAt->format('g:i A') }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $createdAt->diffForHumans() }}
                                                </small>
                                            </td>
                                            <td>
    @if($checkout->due_date)
        <strong class="fs-6 {{ \Carbon\Carbon::parse($checkout->due_date)->isPast() ? 'text-danger' : 'text-dark' }}">
            {{ \Carbon\Carbon::parse($checkout->due_date)->format('M j, Y') }}
        </strong>
        <br>
        <small class="text-muted">
            @php
                $today = \Carbon\Carbon::now('Asia/Phnom_Penh')->startOfDay();
                $dueDate = \Carbon\Carbon::parse($checkout->due_date)->startOfDay();
                $daysUntilDue = $today->diffInDays($dueDate, false);
            @endphp
            @if($daysUntilDue < 0)
                <span class="text-danger">Overdue by {{ abs(round($daysUntilDue)) }} days</span>
            @elseif($daysUntilDue == 0)
                <span class="text-warning">Due today</span>
            @else
                Due in {{ round($daysUntilDue) }} days
            @endif
        </small>
    @else
        <strong class="fs-6 text-muted">—</strong>
        <br>
        <small class="text-muted">Not set yet</small>
    @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <form action="{{ route('librarian.checkouts.approve', $checkout) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm px-2" 
                                                                {{-- onclick="return confirm('Approve checkout request for {{ $checkout->user->name }}?\nBook: {{ $checkout->book->title }}')" --}}
                                                                title="Approve">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('librarian.checkouts.reject', $checkout) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm px-2"
                                                                onclick="return confirm('Reject checkout request for {{ $checkout->user->name }}?\nBook: {{ $checkout->book->title }}')"
                                                                title="Reject">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="text-center mt-1">
                                                    <small class="text-muted">
                                                        #{{ $checkout->id }}
                                                    </small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Mobile & Tablet Card View -->
                <div class="d-md-none">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-warning text-dark py-3">
                            <h6 class="mb-0">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ $pendingCheckouts->total() }} Pending Requests
                            </h6>
                        </div>
                    </div>

                    @foreach($pendingCheckouts as $checkout)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-3">
                            <!-- Student Info - NO AVATAR -->
                            <div class="mb-3 pb-2 border-bottom">
                                <strong class="d-block fs-6">{{ $checkout->user->name }}</strong>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $checkout->user->email }}</small>
                                <small class="text-muted" style="font-size: 0.7rem;">ID: {{ $checkout->user->id }}</small>
                            </div>

                            <!-- Book Info -->
                            <div class="d-flex align-items-start mb-3 pb-2 border-bottom">
                                @if($checkout->book->image)
                                    <img src="{{ asset('storage/' . $checkout->book->image) }}" 
                                        alt="{{ $checkout->book->title }}" 
                                        class="img-thumbnail me-3" style="width: 45px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center me-3 border" 
                                        style="width: 45px; height: 60px;">
                                        <i class="bi bi-book text-muted" style="font-size: 0.9rem;"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <strong class="d-block fs-6 mb-1">{{ $checkout->book->title }}</strong>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">by {{ $checkout->book->author }}</small>
                                    <small class="text-muted" style="font-size: 0.7rem;">ISBN: {{ $checkout->book->isbn }}</small>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Request Date</small>
                                    @php
                                        $createdAt = \Carbon\Carbon::parse($checkout->created_at)->setTimezone('Asia/Phnom_Penh');
                                    @endphp
                                    <strong class="fs-6">{{ $createdAt->format('M j, Y') }}</strong>
                                    <br>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        {{ $createdAt->format('g:i A') }}
                                    </small>
                                </div>
                                <div class="col-6">
    <small class="text-muted d-block" style="font-size: 0.75rem;">Due Date</small>
    @if($checkout->due_date)
        <strong class="fs-6 {{ \Carbon\Carbon::parse($checkout->due_date)->isPast() ? 'text-danger' : 'text-dark' }}">
            {{ \Carbon\Carbon::parse($checkout->due_date)->format('M j, Y') }}
        </strong>
        <br>
        <small class="text-muted" style="font-size: 0.7rem;">
            @php
                $today = \Carbon\Carbon::now('Asia/Phnom_Penh')->startOfDay();
                $dueDate = \Carbon\Carbon::parse($checkout->due_date)->startOfDay();
                $daysUntilDue = $today->diffInDays($dueDate, false);
            @endphp
            @if($daysUntilDue < 0)
                <span class="text-danger">Overdue</span>
            @elseif($daysUntilDue == 0)
                <span class="text-warning">Today</span>
            @else
                {{ round($daysUntilDue) }} days
            @endif
        </small>
    @else
        <strong class="fs-6 text-muted">—</strong>
        <br>
        <small class="text-muted" style="font-size: 0.7rem;">Not set yet</small>
    @endif
                                </div>
                            </div>

                            <!-- Actions - Icon Only -->
                            <div class="d-flex justify-content-center gap-2 mb-2">
                                <form action="{{ route('librarian.checkouts.approve', $checkout) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm px-3 py-2" 
                                            onclick="return confirm('Approve checkout request for {{ $checkout->user->name }}?\nBook: {{ $checkout->book->title }}')"
                                            title="Approve Request">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('librarian.checkouts.reject', $checkout) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm px-3 py-2"
                                            onclick="return confirm('Reject checkout request for {{ $checkout->user->name }}?\nBook: {{ $checkout->book->title }}')"
                                            title="Reject Request">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="text-center">
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    Request #{{ $checkout->id }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($pendingCheckouts->hasPages())
                    <div class="mt-3 mt-md-4">
                        <nav aria-label="Pending requests pagination">
                            <ul class="pagination justify-content-center flex-wrap mb-0">
                                {{-- Previous Page Link --}}
                                @if($pendingCheckouts->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link px-2 px-sm-3">Prev</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link px-2 px-sm-3" href="{{ $pendingCheckouts->previousPageUrl() }}" rel="prev">Prev</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach($pendingCheckouts->getUrlRange(1, $pendingCheckouts->lastPage()) as $page => $url)
                                    @if($page == $pendingCheckouts->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link px-2 px-sm-3">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link px-2 px-sm-3" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if($pendingCheckouts->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link px-2 px-sm-3" href="{{ $pendingCheckouts->nextPageUrl() }}" rel="next">Next</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link px-2 px-sm-3">Next</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif

                <!-- Info Cards -->
                <div class="row mt-3 mt-md-4">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body p-3">
                                <h6 class="card-title fs-6 mb-2">
                                    <i class="bi bi-info-circle text-primary me-2"></i>
                                    Approval Guidelines
                                </h6>
                                <ul class="small mb-0 ps-3" style="font-size: 0.8rem;">
                                    <li class="mb-1">Check student checkout limit (5 books)</li>
                                    <li class="mb-1">Verify book availability</li>
                                    <li class="mb-1">Check for outstanding fines</li>
                                    <li class="mb-0">Approval updates availability automatically</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body p-3">
                                <h6 class="card-title fs-6 mb-2">
                                    <i class="bi bi-clock-history text-warning me-2"></i>
                                    Quick Actions
                                </h6>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('librarian.checkouts.create') }}" class="btn btn-outline-primary btn-sm py-2">
                                        <i class="bi bi-plus-circle me-1"></i> Direct Checkout
                                    </a>
                                    <a href="{{ route('librarian.books.index') }}" class="btn btn-outline-secondary btn-sm py-2">
                                        <i class="bi bi-book me-1"></i> Manage Books
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- Empty State -->
                <div class="text-center py-4 py-md-5">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="text-success h4 mb-2">All Caught Up!</h3>
                    <p class="text-muted mb-3">There are no pending checkout requests at the moment.</p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <a href="{{ route('librarian.checkouts.index') }}" class="btn btn-primary btn-sm flex-fill flex-sm-grow-0">
                            <i class="bi bi-list me-2"></i> View All Checkouts
                        </a>
                        <a href="{{ route('librarian.books.index') }}" class="btn btn-outline-primary btn-sm flex-fill flex-sm-grow-0">
                            <i class="bi bi-book me-2"></i> Browse Books
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.card {
    border-radius: 8px;
}

/* Hover effects */
.table tbody tr:hover {
    background-color: rgba(52, 152, 219, 0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Mobile card hover */
.d-md-none .card:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

/* Icon button styles */
.btn-sm.px-2 {
    min-width: 36px;
}

.btn-sm.px-3 {
    min-width: 42px;
}

/* Tooltip for better UX */
[title] {
    position: relative;
}

[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
}

/* iPad Mini specific responsive adjustments */
@media (max-width: 991px) and (min-width: 768px) {
    .container-fluid {
        padding-left: 20px;
        padding-right: 20px;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .table > :not(caption) > * > * {
        padding: 0.75rem 0.5rem;
    }
    
    .fs-6 {
        font-size: 0.9rem !important;
    }
    
    .btn-sm {
        padding: 0.3rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .img-thumbnail {
        width: 45px !important;
        height: 63px !important;
    }
    
    .bg-light.border {
        width: 45px !important;
        height: 63px !important;
    }
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 12px;
        padding-right: 12px;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }

    .card-body {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding-left: 8px;
        padding-right: 8px;
    }
    
    .btn {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
    }
    
    /* Better text handling for small screens */
    .flex-grow-1 strong {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }
    
    .flex-grow-1 small {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }
}

@media (max-width: 430px) {
    .container-fluid {
        padding-left: 6px;
        padding-right: 6px;
    }
    
    .btn {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
    }
    
    .card-body {
        padding: 0.5rem;
    }
    
    .h4 {
        font-size: 1.1rem;
    }
    
    .fs-6 {
        font-size: 0.85rem !important;
    }
    
    /* iPhone specific image adjustments */
    .img-thumbnail {
        width: 40px !important;
        height: 56px !important;
    }
    
    .bg-light.border {
        width: 40px !important;
        height: 56px !important;
    }
}

@media (max-width: 375px) {
    .container-fluid {
        padding-left: 4px;
        padding-right: 4px;
    }
    
    .card-body {
        padding: 0.4rem;
    }
    
    .fs-6 {
        font-size: 0.8rem !important;
    }
    
    .btn {
        font-size: 0.7rem;
        padding: 0.35rem 0.5rem;
    }
    
    .d-flex.justify-content-center.gap-2 {
        gap: 0.5rem !important;
    }
}
</style>

@endsection