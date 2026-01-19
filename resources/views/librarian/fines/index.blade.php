@extends('layouts.librarian')

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h1 class="h4 h3-md mb-1">Fines Management</h1>
            <p class="text-muted mb-0 small d-none d-md-block">Manage library overdue fines</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Generate Fines Button -->
            <form action="{{ route('librarian.fines.generate-overdue') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm" 
                        title="Generate Overdue Fines"
                        onclick="return confirm('Generate fines for all overdue books?')">
                    <i class="bi bi-arrow-clockwise me-1"></i> Generate
                </button>
            </form>
            
            <!-- Recalculate Button -->
            <form action="{{ route('librarian.fines.recalculate') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-info btn-sm" 
                        title="Recalculate existing fines"
                        onclick="return confirm('Recalculate all unpaid fines based on current overdue days?')">
                    <i class="bi bi-calculator"></i>
                </button>
            </form>
        </div>
    </div>


    <!-- Debug Stats -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-3">
            <div class="card bg-light border h-100">
                <div class="card-body p-2 p-md-3">
                    <h6 class="card-title mb-1 small fw-semibold text-muted">Overdue Checkouts</h6>
                    <h4 class="mb-0 fs-5 fs-md-4">
                        {{ \App\Models\Checkout::where('due_date', '<', now())->whereNull('return_date')->where('status', 'checked_out')->count() }}
                    </h4>
                    <small class="text-muted">Ready for fines</small>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card bg-light border h-100">
                <div class="card-body p-2 p-md-3">
                    <h6 class="card-title mb-1 small fw-semibold text-muted">Current Date</h6>
                    <h4 class="mb-0 fs-5 fs-md-4">{{ now()->format('M d, Y') }}</h4>
                    <small class="text-muted">System date</small>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card bg-light border h-100">
                <div class="card-body p-2 p-md-3">
                    <h6 class="card-title mb-1 small fw-semibold text-muted">Checkouts with Fines</h6>
                    <h4 class="mb-0 fs-5 fs-md-4">
                        {{ \App\Models\Checkout::whereNull('return_date')
                            ->whereHas('fines', function($q) {
                                $q->where('status', 'unpaid');
                            })->count() }}
                    </h4>
                    <small class="text-muted">Already fined</small>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card bg-light border h-100">
                <div class="card-body p-2 p-md-3">
                    <h6 class="card-title mb-1 small fw-semibold text-muted">Checkouts Without Fines</h6>
                    <h4 class="mb-0 fs-5 fs-md-4">
                        {{ \App\Models\Checkout::where('due_date', '<', now())
                            ->whereNull('return_date')
                            ->where('status', 'checked_out')
                            ->whereDoesntHave('fines', function($q) {
                                $q->where('status', 'unpaid');
                            })->count() }}
                    </h4>
                    <small class="text-muted">Need fines</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Stats -->
    <div class="row g-2 mb-4">
        <div class="col-4 col-md-4">
            <div class="card bg-danger text-white h-100 stat-card">
                <div class="card-body p-2 p-md-3 d-flex flex-column flex-md-row justify-content-between align-items-start">
                    <div class="w-100">
                        <h6 class="card-title mb-1 small fw-semibold">Unpaid</h6>
                        <h4 class="mb-0 fs-5 fs-md-4">${{ number_format($totalUnpaid, 2) }}</h4>
                        <small class="opacity-75">{{ $unpaidFinesCount }} outstanding</small>
                    </div>
                    <i class="bi bi-exclamation-circle mt-1 mt-md-0 ms-auto" style="font-size: 1.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="card bg-success text-white h-100 stat-card">
                <div class="card-body p-2 p-md-3 d-flex flex-column flex-md-row justify-content-between align-items-start">
                    <div class="w-100">
                        <h6 class="card-title mb-1 small fw-semibold">Paid</h6>
                        <h4 class="mb-0 fs-5 fs-md-4">${{ number_format($totalPaid, 2) }}</h4>
                        <small class="opacity-75">{{ $paidFinesCount }} collected</small>
                    </div>
                    <i class="bi bi-check-circle mt-1 mt-md-0 ms-auto" style="font-size: 1.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-4">
            <div class="card bg-secondary text-white h-100 stat-card">
                <div class="card-body p-2 p-md-3 d-flex flex-column flex-md-row justify-content-between align-items-start">
                    <div class="w-100">
                        <h6 class="card-title mb-1 small fw-semibold">Waived</h6>
                        <h4 class="mb-0 fs-5 fs-md-4">${{ number_format($totalWaived, 2) }}</h4>
                        <small class="opacity-75">{{ $waivedCount }} waived</small>
                    </div>
                    <i class="bi bi-x-circle mt-1 mt-md-0 ms-auto" style="font-size: 1.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Table View (lg and above) -->
    <div class="card shadow-sm d-none d-lg-block">
        <div class="card-body p-3">
            @if($fines->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-receipt text-muted" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-3 h6">No fines found</h5>
                    <p class="text-muted small">There are no fines in the system.</p>
                    @if(\App\Models\Checkout::where('due_date', '<', now())->whereNull('return_date')->where('status', 'checked_out')->count() > 0)
                        <p class="text-muted small mt-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            There are {{ \App\Models\Checkout::where('due_date', '<', now())->whereNull('return_date')->where('status', 'checked_out')->count() }} overdue checkouts that need fines.
                        </p>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Issued Date</th>
                                <th>Paid Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fines as $fine)
                                <tr>
                                    <!-- ID -->
                                    <td class="text-muted small">#{{ $fine->id }}</td>
                                    
                                    <!-- User -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-primary me-2"></i>
                                            <div>
                                                <div class="fw-medium text-truncate">{{ $fine->user->name ?? 'N/A' }}</div>
                                                <small class="text-muted text-truncate">{{ $fine->user->email ?? '' }}</small>
                                                @if($fine->checkout && $fine->checkout->book)
                                                    <div class="text-truncate" style="max-width: 150px; font-size: 0.8em;">
                                                        <i class="bi bi-book me-1"></i>{{ $fine->checkout->book->title }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Amount -->
                                    <td>
                                        <span class="fw-bold {{ $fine->isUnpaid() ? 'text-danger' : 'text-success' }}">
                                            ${{ number_format($fine->amount, 2) }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td>
                                        @if($fine->status == 'unpaid')
                                            <span class="badge bg-danger">Unpaid</span>
                                        @elseif($fine->status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-secondary">Waived</span>
                                        @endif
                                    </td>

                                    <!-- Reason -->
                                    <td>{{ $fine->reason }}</td>

                                    <!-- Issued Date -->
                                    <td>{{ $fine->fine_date->format('M d, Y') }}</td>

                                    <!-- Paid Date -->
                                    <td>
                                        @if($fine->paid_date)
                                            <div class="text-success">
                                                <div>{{ $fine->paid_date->format('M d, Y') }}</div>
                                                <small>{{ $fine->paid_date->format('h:i A') }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            @if($fine->isUnpaid())
                                                <form action="{{ route('librarian.fines.mark-paid', $fine) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" 
                                                            title="Mark as Paid"
                                                            onclick="return confirm('Mark this fine as paid?')">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('librarian.fines.waive', $fine) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning btn-sm" 
                                                            title="Waive Fine"
                                                            onclick="return confirm('Waive this fine?')">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">Paid/Waived</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $fines->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Mobile/Tablet Card View (lg and below) -->
    <div class="d-lg-none">
        @if($fines->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 2.5rem;"></i>
                <h5 class="mt-3 h6">No fines found</h5>
                <p class="text-muted small">There are no fines in the system.</p>
                @if(\App\Models\Checkout::where('due_date', '<', now())->whereNull('return_date')->where('status', 'checked_out')->count() > 0)
                    <p class="text-muted small mt-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        There are {{ \App\Models\Checkout::where('due_date', '<', now())->whereNull('return_date')->where('status', 'checked_out')->count() }} overdue checkouts that need fines.
                    </p>
                @endif
            </div>
        @else
            @foreach($fines as $fine)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <!-- Header Row -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <!-- User Info -->
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle text-primary me-2"></i>
                                <div>
                                    <div class="fw-medium text-truncate">{{ $fine->user->name ?? 'N/A' }}</div>
                                    <small class="text-muted text-truncate">{{ $fine->user->email ?? '' }}</small>
                                    <div class="text-muted small">ID: #{{ $fine->id }}</div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                @if($fine->status == 'unpaid')
                                    <span class="badge bg-danger">Unpaid</span>
                                @elseif($fine->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-secondary">Waived</span>
                                @endif
                            </div>
                        </div>

                        <!-- Book Info -->
                        @if($fine->checkout && $fine->checkout->book)
                        <div class="mb-3">
                            <div class="small text-muted">Book</div>
                            <div class="fw-medium">
                                <i class="bi bi-book text-primary me-1"></i>
                                {{ $fine->checkout->book->title }}
                            </div>
                        </div>
                        @endif

                        <!-- Amount & Reason -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="small text-muted">Amount</div>
                                <div class="fw-bold {{ $fine->isUnpaid() ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($fine->amount, 2) }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted">Reason</div>
                                <div>{{ $fine->reason }}</div>
                            </div>
                        </div>

                        <!-- Dates Row -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="small text-muted">Issued</div>
                                <div>{{ $fine->fine_date->format('M d, Y') }}</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted">Paid</div>
                                @if($fine->paid_date)
                                    <div class="text-success">
                                        <div>{{ $fine->paid_date->format('M d, Y') }}</div>
                                        <small>{{ $fine->paid_date->format('h:i A') }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 pt-2 border-top flex-wrap">
                            @if($fine->isUnpaid())
                                <form action="{{ route('librarian.fines.mark-paid', $fine) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100" 
                                            title="Mark as Paid"
                                            onclick="return confirm('Mark this fine as paid?')">
                                        <i class="bi bi-check-lg me-1"></i> Pay
                                    </button>
                                </form>
                                <form action="{{ route('librarian.fines.waive', $fine) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100" 
                                            title="Waive Fine"
                                            onclick="return confirm('Waive this fine?')">
                                        <i class="bi bi-x-lg me-1"></i> Waive
                                    </button>
                                </form>
                            @else
                                <div class="text-center w-100">
                                    <span class="text-muted small">Already {{ $fine->status }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $fines->links() }}
            </div>
        @endif
    </div>
</div>

<style>
/* Mobile-first base styles */
.card {
    border-radius: 0.75rem;
    transition: all 0.2s ease;
    border: 1px solid rgba(0,0,0,.125);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.1) !important;
}

.btn {
    min-height: 44px;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.btn-group .btn {
    margin: 0 2px;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

.text-truncate {
    max-width: 100%;
}

/* Tablet (sm/lg) */
@media (min-width: 576px) and (max-width: 991.98px) {
    .card-body {
        padding: 1rem;
    }
}

/* Tablet & Desktop adjustments */
@media (min-width: 768px) {
    .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.85rem;
    }

    .table td, .table th {
        padding: 0.75rem;
    }
}

/* Desktop (lg and above) */
@media (min-width: 992px) {
    .stat-card {
        border-radius: 0.5rem;
    }

    .badge {
        font-size: 0.85rem;
    }

    .card-body {
        padding: 1.25rem;
    }
}
</style>

<!-- Initialize Bootstrap tooltips -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover'
            })
        });
    });
</script>
@endsection