@extends('layouts.librarian')

@section('title', 'Pending Extension Requests')

@section('content')
<div class="container-fluid py-3 py-md-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4">
                <h1 class="h4 h3-md mb-2 mb-sm-0 text-nowrap">
                    <i class="bi bi-clock text-warning me-2"></i>Pending Extension Requests
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

            @if($pendingExtensions->count() > 0)
                <!-- Stats Card -->
                <div class="card bg-warning bg-opacity-10 border-warning mb-3 mb-md-4">
                    <div class="card-body py-2 d-flex align-items-center gap-2">
                        <i class="bi bi-clock text-warning"></i>
                        <div>
                            <h6 class="mb-0">{{ $pendingExtensions->count() }} Pending Extension Request(s)</h6>
                            <small class="text-muted">Awaiting your approval</small>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="card border-0 shadow-sm d-none d-md-block mb-4">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h5 class="mb-0"><i class="bi bi-table me-2 text-primary"></i>Extension Requests Overview</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="px-3 px-lg-4 py-3">Book</th>
                                        <th class="px-2 px-lg-3 py-3">Student</th>
                                        <th class="px-2 px-lg-3 py-3 text-center">Current Due</th>
                                        <th class="px-2 px-lg-3 py-3 text-center">New Due</th>
                                        <th class="px-2 px-lg-3 py-3 text-center">Requested</th>
                                        <th class="px-3 px-lg-4 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingExtensions as $checkout)
                                    @php
                                        $newDueDate = \Carbon\Carbon::parse($checkout->due_date)->addDays(3);
                                        $daysRemaining = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($checkout->due_date)->startOfDay(), false);
                                    @endphp
                                    <tr>
                                        <!-- Book -->
                                        <td class="px-3 px-lg-4 py-3">
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

                                        <!-- Student - NO AVATAR -->
                                        <td class="px-2 px-lg-3 py-3">
                                            <div>
                                                <strong class="d-block fs-6">{{ $checkout->user->name }}</strong>
                                                <small class="text-muted d-block">{{ $checkout->user->email }}</small>
                                                <small class="text-muted">ID: {{ $checkout->user->id }}</small>
                                            </div>
                                        </td>

                                        <!-- Current Due -->
                                        <td class="px-2 px-lg-3 py-3 text-center">
                                            <div class="fw-medium fs-6">{{ \Carbon\Carbon::parse($checkout->due_date)->format('M j, Y') }}</div>
                                            <small class="{{ $daysRemaining < 0 ? 'text-danger' : ($daysRemaining===0?'text-warning':'text-muted') }}">
                                                @if($daysRemaining < 0)
                                                    <i class="bi bi-exclamation-circle me-1"></i> Overdue
                                                @elseif($daysRemaining===0)
                                                    <i class="bi bi-exclamation-triangle me-1"></i> Today
                                                @else
                                                    <i class="bi bi-clock me-1"></i> {{ $daysRemaining }} days
                                                @endif
                                            </small>
                                        </td>

                                        <!-- New Due -->
                                        <td class="px-2 px-lg-3 py-3 text-center">
                                            <div class="fw-medium text-success fs-6">{{ $newDueDate->format('M j, Y') }}</div>
                                            <small class="text-muted"><i class="bi bi-calendar-plus me-1"></i>+3 days</small>
                                        </td>

                                        <!-- Requested -->
                                        <td class="px-2 px-lg-3 py-3 text-center">
                                            @if($checkout->extension_requested_at)
                                                <div class="fw-medium fs-6">{{ $checkout->extension_requested_at->format('M j, Y') }}</div>
                                                <small class="text-muted">{{ $checkout->extension_requested_at->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>

                                        <!-- Actions - Icon Only -->
                                        <td class="px-3 px-lg-4 py-3">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <form action="{{ route('librarian.checkouts.approve-extension', $checkout->id) }}" method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-success btn-sm px-2"
                                                            title="Approve">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('librarian.checkouts.reject-extension', $checkout->id) }}" method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-danger btn-sm px-2"
                                                            onclick="return confirm('Reject extension request for {{ $checkout->user->name }}?\nBook: {{ $checkout->book->title }}')"
                                                            title="Reject">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
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
                    @foreach($pendingExtensions as $checkout)
                    @php
                        $newDueDate = \Carbon\Carbon::parse($checkout->due_date)->addDays(3);
                        $daysRemaining = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($checkout->due_date)->startOfDay(), false);
                    @endphp
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-3">
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

                            <!-- Student Info - NO AVATAR -->
                            <div class="mb-3 pb-2 border-bottom">
                                <strong class="d-block fs-6">{{ $checkout->user->name }}</strong>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $checkout->user->email }}</small>
                                <small class="text-muted" style="font-size: 0.7rem;">ID: {{ $checkout->user->id }}</small>
                            </div>

                            <!-- Dates -->
                            <div class="row mb-3">
                                <div class="col-4">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Current Due</small>
                                    <strong class="fs-6">{{ \Carbon\Carbon::parse($checkout->due_date)->format('M j') }}</strong>
                                    <br>
                                    <small class="{{ $daysRemaining<0?'text-danger':($daysRemaining===0?'text-warning':'text-muted') }}" style="font-size: 0.7rem;">
                                        @if($daysRemaining<0) Overdue
                                        @elseif($daysRemaining===0) Today
                                        @else {{ $daysRemaining }}d @endif
                                    </small>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">New Due</small>
                                    <strong class="text-success fs-6">{{ $newDueDate->format('M j') }}</strong>
                                    <br>
                                    <small class="text-muted" style="font-size: 0.7rem;">+3 days</small>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Requested</small>
                                    @if($checkout->extension_requested_at)
                                        <strong class="fs-6">{{ $checkout->extension_requested_at->format('M j') }}</strong>
                                        <br>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $checkout->extension_requested_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted" style="font-size: 0.7rem;">N/A</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions - Icon Only -->
                            <div class="d-flex justify-content-center gap-2">
                                <form action="{{ route('librarian.checkouts.approve-extension', $checkout->id) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-success btn-sm px-3 py-2"
                                            title="Approve Extension">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('librarian.checkouts.reject-extension', $checkout->id) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-danger btn-sm px-3 py-2"
                                            title="Reject Extension">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Guidelines Card -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <h6 class="card-title mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Extension Guidelines</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="small mb-2 mb-md-0">
                                            <li>Maximum 3-day extension per request</li>
                                            <li>Check for previous extensions on the same book</li>
                                            <li>Verify book availability for other students</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="small mb-0">
                                            <li>Ensure student has no outstanding fines</li>
                                            <li>One extension allowed per checkout</li>
                                            <li>Due date updates automatically upon approval</li>
                                        </ul>
                                    </div>
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
                    <h3 class="text-success h4 mb-2">No Pending Extension Requests</h3>
                    <p class="text-muted mb-3">There are no extension requests waiting for approval at the moment.</p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <a href="{{ route('librarian.checkouts.index') }}" class="btn btn-primary btn-sm flex-fill flex-sm-grow-0">
                            <i class="bi bi-list me-2"></i> View All Checkouts
                        </a>
                        <a href="{{ route('librarian.dashboard') }}" class="btn btn-outline-primary btn-sm flex-fill flex-sm-grow-0">
                            <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
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