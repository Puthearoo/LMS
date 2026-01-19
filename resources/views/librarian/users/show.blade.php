@extends('layouts.librarian')

@section('title', 'Borrower Details')

@section('content')
<div class="container-fluid px-2 px-md-3 px-lg-4 py-3 py-md-4">
    
    <!-- Invoice Popup -->
    <div id="invoicePopup" style="display:none;">
        <div id="invoiceOverlay" onclick="closeInvoice()"></div>
        <div id="invoiceBox">
            <button onclick="closeInvoice()" class="close-popup">✕</button>
            <iframe id="invoiceFrame"></iframe>
        </div>
    </div>

    <!-- Header with Breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-2">
                <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('librarian.users.index') }}">Borrowers</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <div class="mb-2 mb-md-0">
                <h1 class="h3 h2-md mb-1 font-weight-bold">Borrower Details</h1>
                <p class="text-muted mb-0">View and manage borrower information, loans, and fines</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('librarian.users.edit', $user) }}" 
                   class="btn btn-outline-primary btn-sm d-md-none">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="{{ route('librarian.users.edit', $user) }}" 
                   class="btn btn-outline-primary d-none d-md-inline-flex align-items-center">
                    <i class="fas fa-edit me-1"></i> Edit Profile
                </a>
                
                <a href="{{ route('librarian.users.index') }}" 
                   class="btn btn-outline-secondary d-none d-md-inline-flex align-items-center">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                
                <a href="{{ route('librarian.users.index') }}" 
                   class="btn btn-outline-secondary btn-sm d-md-none">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    @if($user->status !== 'active')
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Account Status: {{ ucfirst($user->status) }}</strong>
            @if($user->status === 'suspended')
                - This account is currently suspended and cannot borrow books.
            @elseif($user->status === 'inactive')
                - This account is marked as inactive.
            @endif
        </div>
    @endif

    <div class="row">
        <!-- Left Column: User Information -->
        <div class="col-lg-4 mb-4">
            <!-- User Profile Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <div class="profile-avatar mx-auto mb-3 bg-primary-soft text-primary">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <h3 class="h4 mb-1">{{ $user->name }}</h3>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <span class="badge bg-primary-soft text-primary">
                                    Student
                                </span>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'suspended' ? 'danger' : 'warning') }}-soft text-{{ $user->status === 'active' ? 'success' : ($user->status === 'suspended' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                                <span class="badge bg-light text-dark border">
                                    ID: {{ $user->id }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between px-0 py-2 border-0">
                            <span class="text-muted">
                                <i class="fas fa-phone me-2"></i>Contact
                            </span>
                            <span class="font-weight-medium">{{ $user->contact ?? 'Not provided' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0 py-2 border-0">
                            <span class="text-muted">
                                <i class="fas fa-calendar me-2"></i>Joined
                            </span>
                            <span class="font-weight-medium">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0 py-2 border-0">
                            <span class="text-muted">
                                <i class="fas fa-clock me-2"></i>Last Updated
                            </span>
                            <span class="font-weight-medium">{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Quick Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3 col-lg-6">
                            <div class="text-center p-3 border rounded">
                                <div class="h4 font-weight-bold text-primary mb-1">
                                    {{ $user->checkouts()->whereIn('status', ['approved', 'checked_out'])->count() }}
                                </div>
                                <div class="small text-muted">Active Loans</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-6">
                            <div class="text-center p-3 border rounded">
                                <div class="h4 font-weight-bold text-danger mb-1">
                                    {{ $user->checkouts()->whereIn('status', ['approved', 'checked_out'])->where('due_date', '<', now())->count() }}
                                </div>
                                <div class="small text-muted">Overdue</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-6">
                            <div class="text-center p-3 border rounded">
                                <div class="h4 font-weight-bold text-warning mb-1">
                                    {{ $user->reservations()->whereIn('status', ['waiting', 'ready'])->count() }}
                                </div>
                                <div class="small text-muted">Reservations</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-6">
                            <div class="text-center p-3 border rounded">
                                @php
                                    $unpaidFines = $user->fines()->where('status', 'unpaid')->sum('amount');
                                @endphp
                                <div class="h4 font-weight-bold text-danger mb-1">
                                    ${{ number_format($unpaidFines, 2) }}
                                </div>
                                <div class="small text-muted">Unpaid Fines</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Loans & Fines -->
        <div class="col-lg-8">
            <!-- Tabs Navigation -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom p-0">
                    <ul class="nav nav-tabs nav-tabs-custom border-0" id="userTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="loans-tab" data-bs-toggle="tab" data-bs-target="#loans" 
                                    type="button" role="tab">
                                <i class="fas fa-book me-2 d-none d-md-inline"></i>
                                <span class="d-inline d-md-none">Loans</span>
                                <span class="d-none d-md-inline">Active Loans</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reservations-tab" data-bs-toggle="tab" data-bs-target="#reservations" 
                                    type="button" role="tab">
                                <i class="fas fa-bookmark me-2 d-none d-md-inline"></i>
                                <span>Reservations</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fines-tab" data-bs-toggle="tab" data-bs-target="#fines" 
                                    type="button" role="tab">
                                <i class="fas fa-coins me-2 d-none d-md-inline"></i>
                                <span>Fines</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" 
                                    type="button" role="tab">
                                <i class="fas fa-history me-2 d-none d-md-inline"></i>
                                <span>History</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="card-body p-0">
                    <div class="tab-content" id="userTabsContent">
                    
                        <!-- Active Loans Tab -->
                        <div class="tab-pane fade show active p-3 p-md-4" id="loans" role="tabpanel">
                            @php
                                $activeLoans = $user->checkouts()
                                    ->whereIn('status', ['approved', 'checked_out'])
                                    ->with('book')
                                    ->orderBy('due_date', 'asc')
                                    ->get();
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
                                <h6 class="mb-0 font-weight-bold h5">
                                    <i class="fas fa-book text-primary me-2"></i>Active Loans
                                    <span class="badge bg-primary ms-2">{{ $activeLoans->count() }}</span>
                                </h6>
                            </div>

                            @if($activeLoans->isEmpty())
                                <div class="text-center py-4 py-md-5">
                                    <div class="mb-3">
                                        <i class="fas fa-book fa-3x text-muted opacity-50"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">No Active Loans</h5>
                                    <p class="text-muted small">This borrower has no active loans at the moment.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3">Book</th>
                                                <th class="d-none d-md-table-cell">Borrowed</th>
                                                <th class="text-nowrap">Due Date</th>
                                                <th class="text-center pe-3">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activeLoans as $loan)
                                                <tr>
                                                    <td class="ps-3">
                                                        <div class="font-weight-bold mb-1">{{ $loan->book->title ?? 'Unknown Book' }}</div>
                                                        <div class="text-muted small">
                                                            <div class="mb-1">ISBN: {{ $loan->book->isbn ?? 'N/A' }}</div>
                                                            <div class="d-md-none">
                                                                <small class="text-muted">Borrowed: {{ $loan->checkout_date ? $loan->checkout_date->format('M d, Y') : '—' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        @if($loan->checkout_date)
                                                            <div>{{ $loan->checkout_date->format('M d, Y') }}</div>
                                                            <small class="text-muted">{{ $loan->checkout_date->format('h:i A') }}</small>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-nowrap">
                                                        @if($loan->due_date)
                                                            <div class="{{ $loan->due_date->isPast() ? 'text-danger fw-bold' : 'text-dark' }}">
                                                                {{ $loan->due_date->format('M d, Y') }}
                                                            </div>
                                                            @if($loan->due_date->isPast())
                                                                <small class="text-danger">Overdue</small>
                                                            @else
                                                                <small class="text-muted">{{ $loan->due_date->diffForHumans() }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center pe-3">
                                                        <span class="badge bg-{{ $loan->status === 'checked_out' ? 'secondary' : 'info' }} text-white px-3 py-2">
                                                            {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Reservations Tab -->
                        <div class="tab-pane fade p-3 p-md-4" id="reservations" role="tabpanel">
                            @php
                                $reservations = $user->reservations()
                                    ->whereIn('status', ['waiting', 'ready'])
                                    ->with('book')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
                                <h6 class="mb-0 font-weight-bold h5">
                                    <i class="fas fa-bookmark text-warning me-2"></i>Reservations
                                    <span class="badge bg-warning ms-2">{{ $reservations->count() }}</span>
                                </h6>
                            </div>

                            @if($reservations->isEmpty())
                                <div class="text-center py-4 py-md-5">
                                    <div class="mb-3">
                                        <i class="fas fa-bookmark fa-3x text-muted opacity-50"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">No Active Reservations</h5>
                                    <p class="text-muted small">This borrower has no pending reservations.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3">Book</th>
                                                <th class="d-none d-md-table-cell">Reserved On</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-nowrap pe-3">Expires</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reservations as $reservation)
                                                @php
                                                    // Get the correct expiry date field for this reservation
                                                    $expiryDate = $reservation->expires_at ?? $reservation->expiry_date;
                                                @endphp
                                                <tr>
                                                    <td class="ps-3">
                                                        <div class="font-weight-bold mb-1">{{ $reservation->book->title ?? 'Unknown Book' }}</div>
                                                        <div class="text-muted small">
                                                            <div class="mb-1">ISBN: {{ $reservation->book->isbn ?? 'N/A' }}</div>
                                                            <div class="d-md-none">
                                                                <small class="text-muted">Reserved: {{ $reservation->created_at->format('M d, Y') }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        @if($reservation->created_at)
                                                            <div>{{ $reservation->created_at->format('M d, Y') }}</div>
                                                            <small class="text-muted">{{ $reservation->created_at->format('h:i A') }}</small>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $reservation->status === 'ready' ? 'success' : 'warning' }}-soft text-{{ $reservation->status === 'ready' ? 'success' : 'warning' }} px-3 py-2">
                                                            {{ ucfirst($reservation->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-nowrap pe-3">
                                                        @if($reservation->status === 'ready' && $expiryDate)
                                                            <div class="{{ $expiryDate->isPast() ? 'text-danger fw-bold' : 'text-dark' }}">
                                                                {{ $expiryDate->format('M d, Y') }}
                                                            </div>
                                                            @if($expiryDate->isPast())
                                                                <small class="text-danger">Expired</small>
                                                            @endif
                                                        @elseif($reservation->status === 'waiting')
                                                            <span class="text-muted">—</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Fines Tab -->
                        <div class="tab-pane fade p-3 p-md-4" id="fines" role="tabpanel">
                            @php
                                $fines = $user->fines()
                                    ->with('checkout.book')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                                    
                                $unpaidFines = $fines->where('status', 'unpaid');
                                $paidFines = $fines->where('status', 'paid');
                                $waivedFines = $fines->where('status', 'waived');
                                $totalUnpaid = $unpaidFines->sum('amount');
                                $totalPaid = $paidFines->sum('amount');
                                $totalWaived = $waivedFines->sum('amount');
                                $totalAll = $fines->sum('amount');
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
                                <div>
                                    <h6 class="mb-0 font-weight-bold">
                                        <i class="fas fa-coins text-danger me-2"></i>Fines
                                        <span class="badge bg-danger ms-2">{{ $fines->count() }}</span>
                                    </h6>
                                    <div class="h5 font-weight-bold text-danger mt-1">
                                        ${{ number_format($totalUnpaid, 2) }} <small class="text-muted font-weight-normal">unpaid</small>
                                    </div>
                                </div>
                                @if($unpaidFines->isNotEmpty())
                                    <button class="btn btn-outline-danger d-flex align-items-center" 
                                            onclick="generateInvoice()">
                                        <i class="fas fa-file-invoice-dollar me-1"></i>
                                        <span class="d-none d-md-inline">Print Invoice</span>
                                        <span class="d-md-none">Invoice</span>
                                    </button>
                                @endif
                            </div>

                            @if($fines->isEmpty())
                                <div class="text-center py-4 py-md-5">
                                    <div class="mb-3">
                                        <i class="fas fa-coins fa-3x text-muted opacity-50"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">No Fines Recorded</h5>
                                    <p class="text-muted small">This borrower has no fine history.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="d-none d-sm-table-cell">Reason</th>
                                                <th class="d-sm-none">Details</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th class="d-none d-md-table-cell">Date</th>
                                                <th class="d-none d-sm-table-cell">Book</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($fines as $fine)
                                                <tr>
                                                    <td>
                                                        <div class="font-weight-bold d-sm-none small">{{ $fine->reason }}</div>
                                                        <div class="d-none d-sm-block">{{ $fine->reason }}</div>
                                                        <div class="d-sm-none small mt-1">
                                                            <div>Book: {{ $fine->checkout->book->title ?? 'N/A' }}</div>
                                                            <div>Date: {{ $fine->created_at->format('M d, Y') }}</div>
                                                        </div>
                                                    </td>
                                                    <td class="font-weight-bold">
                                                        ${{ number_format($fine->amount, 2) }}
                                                    </td>
                                                    <td>
                                                        @if($fine->status === 'paid')
                                                            <span class="badge bg-success-soft text-success">Paid</span>
                                                        @elseif($fine->status === 'waived')
                                                            <span class="badge bg-secondary text-white">Waived</span>
                                                        @elseif($fine->status === 'unpaid')
                                                            <span class="badge bg-danger-soft text-danger">Unpaid</span>
                                                        @else
                                                            <span class="badge bg-info-soft text-info">{{ ucfirst($fine->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        {{ $fine->created_at->format('M d, Y') }}
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        {{ $fine->checkout->book->title ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane fade p-3 p-md-4" id="history" role="tabpanel">
    @php
        $history = $user->checkouts()
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
        <h6 class="mb-0 font-weight-bold h5">
            <i class="fas fa-history text-info me-2"></i>Borrowing History
            <span class="badge bg-info ms-2">{{ $history->count() }}</span>
        </h6>
    </div>

    @if($history->isEmpty())
        <div class="text-center py-4 py-md-5">
            <div class="mb-3">
                <i class="fas fa-history fa-3x text-muted opacity-50"></i>
            </div>
            <h5 class="text-muted mb-2">No Borrowing History</h5>
            <p class="text-muted small">This borrower has no borrowing history yet.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Book</th>
                        <th class="d-none d-md-table-cell">Checkout Date</th>
                        <th class="d-none d-md-table-cell">Return Date</th>
                        <th class="text-center pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $record)
                        <tr>
                            <td class="ps-3">
                                <div class="font-weight-bold mb-1">{{ $record->book->title ?? 'Unknown Book' }}</div>
                                <div class="text-muted small">
                                    <div class="mb-1">ISBN: {{ $record->book->isbn ?? 'N/A' }}</div>
                                    <div class="d-md-none">
                                        @if($record->checkout_date)
                                            <small class="text-muted d-block">Checkout: {{ $record->checkout_date->format('M d, Y') }}</small>
                                        @endif
                                        @if($record->return_date)
                                            <small class="text-muted d-block">Return: {{ $record->return_date->format('M d, Y') }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($record->checkout_date)
                                    <div>{{ $record->checkout_date->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $record->checkout_date->format('h:i A') }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($record->return_date)
                                    <div>{{ $record->return_date->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $record->return_date->format('h:i A') }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                @if($record->status === 'returned')
                                    <span class="badge bg-success-soft text-success px-3 py-2">Returned</span>
                                @elseif($record->status === 'overdue')
                                    <span class="badge bg-danger-soft text-danger px-3 py-2">Overdue</span>
                                @elseif($record->status === 'rejected')
                                    <span class="badge bg-warning-soft text-warning px-3 py-2">Rejected</span>
                                @elseif($record->status === 'checked_out')
                                    <span class="badge bg-secondary text-white px-3 py-2">Checked Out</span>
                                @elseif($record->status === 'approved')
                                    <span class="badge bg-primary-soft text-primary px-3 py-2">Approved</span>
                                @else
                                    <span class="badge bg-info-soft text-info px-3 py-2">{{ ucfirst(str_replace('_', ' ', $record->status)) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Invoice Popup Styles */
#invoiceOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 9998;
}

#invoiceBox {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 85%;
    height: 90%;
    background: #fff;
    z-index: 9999;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

#invoiceFrame {
    width: 100%;
    height: 100%;
    border: none;
}

.close-popup {
    position: absolute;
    top: 10px;
    right: 15px;
    background: #dc3545;
    color: #fff;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
    z-index: 10000;
    font-size: 16px;
    line-height: 1;
}

.close-popup:hover {
    background: #c82333;
}

/* Profile Avatar */
.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.1);
}

/* Custom Tabs */
.nav-tabs-custom .nav-link {
    border: none;
    padding: 0.75rem 1rem;
    color: #6c757d;
    font-weight: 500;
    border-bottom: 3px solid transparent;
    font-size: 0.875rem;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.nav-tabs-custom .nav-link:hover {
    color: #0d6efd;
    border-bottom-color: rgba(13, 110, 253, 0.2);
    background-color: rgba(13, 110, 253, 0.05);
}

.nav-tabs-custom .nav-link.active {
    color: #0d6efd;
    background: none;
    border-bottom-color: #0d6efd;
    font-weight: 600;
}

/* Soft backgrounds */
.bg-primary-soft {
    background-color: rgba(78, 115, 223, 0.1);
}

.bg-success-soft {
    background-color: rgba(28, 200, 138, 0.1);
}

.bg-warning-soft {
    background-color: rgba(246, 194, 62, 0.1);
}

.bg-danger-soft {
    background-color: rgba(231, 74, 59, 0.1);
}

.bg-info-soft {
    background-color: rgba(54, 185, 204, 0.1);
}

/* Hover effects */
.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(78, 115, 223, 0.03);
}

/* Responsive table adjustments */
@media (max-width: 575.98px) {
    .table th, .table td {
        padding: 0.4rem 0.3rem;
        font-size: 0.8rem;
    }
    
    .table .badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    
    .table-responsive {
        margin-left: -0.75rem;
        margin-right: -0.75rem;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
}

@media (max-width: 767.98px) {
    .table th, .table td {
        padding: 0.5rem;
        font-size: 0.85rem;
    }
    
    .table .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Stacked table rows on extra small screens */
    .table tbody tr td:first-child {
        border-top: 2px solid #f0f0f0;
    }
    
    .table tbody tr:first-child td:first-child {
        border-top: none;
    }
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .profile-avatar {
        width: 60px;
        height: 60px;
        font-size: 1.2rem;
    }
    
    .h3 {
        font-size: 1.25rem;
    }
    
    .h4 {
        font-size: 1.1rem;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between .btn {
        align-self: flex-end;
    }
}

@media (max-width: 768px) {
    .nav-tabs-custom .nav-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .nav-tabs-custom .nav-link i {
        font-size: 0.9rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .h5.font-weight-bold {
        font-size: 1.1rem;
    }
    
    /* Invoice popup mobile */
    #invoiceBox {
        width: 95%;
        height: 85%;
    }
}

@media (min-width: 768px) {
    .nav-tabs-custom .nav-link {
        padding: 1rem 1.5rem;
        font-size: 0.9rem;
    }
}

/* Scrollable tabs on mobile */
@media (max-width: 575.98px) {
    .nav-tabs-custom {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    
    .nav-tabs-custom::-webkit-scrollbar {
        display: none;
    }
}

/* Mobile-specific table improvements */
@media (max-width: 767.98px) {
    .table th:nth-child(2),
    .table td:nth-child(2),
    .table th:nth-child(3),
    .table td:nth-child(3) {
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
}

/* Horizontal scroll indicator for tables */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tab functionality
    const triggerTabList = [].slice.call(document.querySelectorAll('#userTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
    
    // Store active tab in sessionStorage
    const tabEl = document.querySelector('button[data-bs-toggle="tab"]');
    if (tabEl) {
        tabEl.addEventListener('shown.bs.tab', function (event) {
            localStorage.setItem('activeTab', event.target.id);
        });
    }
    
    // Restore active tab
    const activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        const triggerEl = document.querySelector(`#${activeTab}`);
        if (triggerEl) {
            bootstrap.Tab.getInstance(triggerEl).show();
        }
    }
    
    // Close popup when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeInvoice();
        }
    });
});

function generateInvoice() {
    const unpaidFines = @json($unpaidFines->values() ?? []);
    const user = {
        name: "{{ $user->name }}",
        email: "{{ $user->email }}",
        contact: "{{ $user->contact ?? 'Not provided' }}",
        id: "{{ $user->id }}"
    };

    if (!unpaidFines.length) {
        alert('No fines available to generate invoice.');
        return;
    }

    const now = new Date();
    const invoiceNo = `INV-${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}-${String(Math.floor(Math.random() * 1000)).padStart(3, '0')}`;
    const issueDate = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    const dueDate = new Date(now.setDate(now.getDate() + 30)).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

    let totalAmount = 0;

    const itemsHTML = unpaidFines.map((fine, i) => {
        const amount = parseFloat(fine.amount || 0);
        totalAmount += amount;
        const fineDate = new Date(fine.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

        return `
            <tr>
                <td style="text-align:center; padding: 12px; border-bottom: 1px solid #e0e0e0;">${i + 1}</td>
                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0;">
                    <div style="font-weight: 600;">${fine.reason ?? 'Library Fine'}</div>
                    <div style="font-size: 12px; color: #666;">${fine.checkout?.book?.title ?? 'N/A'}</div>
                </td>
                <td style="text-align:center; padding: 12px; border-bottom: 1px solid #e0e0e0;">${fineDate}</td>
                <td style="text-align:right; padding: 12px; border-bottom: 1px solid #e0e0e0; font-weight: 600;">$${amount.toFixed(2)}</td>
            </tr>
        `;
    }).join('');

    const invoiceHTML = `
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Library Services Invoice - ${invoiceNo}</title>
<style>
@page { 
    size: A4; 
    margin: 0;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Georgia', 'Times New Roman', Times, serif;
    font-size: 13px;
    color: #333;
    line-height: 1.4;
    background: #fff;
    margin: 0;
    padding: 0;
}
.invoice-wrapper {
    width: 210mm;
    min-height: 297mm;
    margin: 0 auto;
    padding: 20mm;
    background: white;
    position: relative;
}
.watermark {
    position: absolute;
    opacity: 0.15; /* Clearly visible */
    font-size: 70px; /* Good readable size */
    font-weight: 700; /* Bold */
    color: #777; /* Dark enough gray */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    white-space: nowrap;
    z-index: 0;
    letter-spacing: 4px;
    text-transform: uppercase;
}
.invoice-content {
    position: relative;
    z-index: 1;
}
.invoice-header {
    border-bottom: 3px solid #1a237e;
    padding-bottom: 25px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.logo-section {
    flex: 1;
}
.university-name {
    font-size: 24px;
    font-weight: bold;
    color: #1a237e;
    letter-spacing: 1px;
}
.university-subtitle {
    font-size: 14px;
    color: #666;
    font-style: italic;
    margin-top: 2px;
}
.address {
    margin-top: 10px;
    color: #555;
    line-height: 1.6;
}
.invoice-title-section {
    text-align: right;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 2px solid #1a237e;
    min-width: 300px;
}
.invoice-title {
    font-size: 28px;
    font-weight: bold;
    color: #1a237e;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 2px;
}
.invoice-details {
    margin-top: 10px;
}
.invoice-details div {
    margin-bottom: 4px;
}
.invoice-details strong {
    color: #333;
    min-width: 100px;
    display: inline-block;
}
.billing-section {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #dee2e6;
}
.bill-to, .library-info {
    flex: 1;
}
.bill-to {
    padding-right: 30px;
}
.library-info {
    padding-left: 30px;
    border-left: 2px dashed #adb5bd;
}
.section-title {
    font-size: 16px;
    font-weight: bold;
    color: #1a237e;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #1a237e;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.customer-info {
    line-height: 1.8;
}
.customer-info strong {
    color: #333;
}
.items-table {
    width: 100%;
    border-collapse: collapse;
    margin: 30px 0;
    background: white;
    box-shadow: 0 0 20px rgba(0,0,0,0.05);
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #dee2e6;
}
.items-table thead {
    background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
    color: white;
}
.items-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.items-table td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
}
.items-table tbody tr:hover {
    background-color: #f8f9fa;
}
.total-section {
    width: 300px;
    margin-left: auto;
    margin-top: 30px;
    background: #f8f9fa;
    padding: 25px;
    border-radius: 8px;
    border: 2px solid #1a237e;
}
.total-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #dee2e6;
    font-size: 14px;
}
.total-row:last-child {
    border-bottom: none;
    font-weight: bold;
    font-size: 18px;
    color: #1a237e;
    margin-top: 10px;
    padding-top: 15px;
}
.payment-title {
    font-weight: bold;
    color: #1976d2;
    margin-bottom: 10px;
}
.footer {
    margin-top: 50px;
    padding-top: 20px;
    border-top: 2px solid #1a237e;
    text-align: center;
    color: #666;
    font-size: 12px;
}
.footer p {
    margin: 5px 0;
}
.stamp {
    position: absolute;
    bottom: 30px;
    right: 30px;
    width: 150px;
    height: 150px;
    border: 3px solid #d32f2f;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: rotate(-15deg);
    opacity: 0.8;
}
.stamp-text {
    color: #d32f2f;
    font-weight: bold;
    font-size: 18px;
    text-transform: uppercase;
    letter-spacing: 2px;
}
.print-controls {
    text-align: center;
    margin-top: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}
.print-btn {
    background: #1a237e;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: background 0.3s;
}
.print-btn:hover {
    background: #283593;
}
@media print {
    /* Hide browser's default headers and footers */
    @page {
        margin: 0;
        size: A4;
    }
    
    body {
        margin: 0 !important;
        padding: 0 !important;
        width: 210mm;
        height: 297mm;
        background: white;
    }
    
    .invoice-wrapper {
        margin: 0 !important;
        padding: 15mm !important;
        width: 210mm !important;
        min-height: 297mm !important;
        box-shadow: none;
        page-break-after: avoid;
        page-break-inside: avoid;
    }
    
    .print-controls { 
        display: none !important; 
    }
    
    /* Prevent elements from being cut off */
    .stamp {
        page-break-inside: avoid;
    }
    
    .total-section {
        page-break-inside: avoid;
    }
    
    /* Ensure proper spacing for print */
    .invoice-content {
        position: static;
    }
    
    /* Remove hover effects for print */
    .items-table tbody tr:hover {
        background-color: transparent;
    }
}

/* Special styles to remove URL in print (for Chrome/Edge) */
@media print {
    /* Remove URL and page info */
    html, body {
        height: 100% !important;
        overflow: hidden !important;
    }
    
    /* Prevent URL from appearing */
    body::before,
    body::after {
        display: none !important;
    }
}
</style>
</head>
<body>
<div class="invoice-wrapper">
    <div class="watermark">WESTERN UNIVERSITY</div>
    
    <div class="invoice-content">
        <div class="invoice-header">
            <div class="logo-section">
                <div class="university-name">WESTERN UNIVERSITY</div>
                <div class="university-subtitle">Central Library Services</div>
                <div class="address">
                    54 St 606, Phnom Penh<br>
                    Phone: 098 765 432 | Email: library@university.edu<br>
                    Website: library.university.edu
                </div>
            </div>
            <div class="invoice-title-section">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-details">
                    <div><strong>Invoice #:</strong> ${invoiceNo}</div>
                    <div><strong>Issue Date:</strong> ${issueDate}</div>
                    <div><strong>Due Date:</strong> ${dueDate}</div>
                </div>
            </div>
        </div>

        <div class="billing-section">
            <div class="bill-to">
                <div class="section-title">Bill To</div>
                <div class="customer-info">
                    <strong>${user.name}</strong><br>
                    Student ID: ${user.id}<br>
                    Email: ${user.email}<br>
                    Phone: ${user.contact || 'Not provided'}<br>
                    Account Type: Student Borrower
                </div>
            </div>
            <div class="library-info">
                <div class="section-title">Library Information</div>
                <div class="customer-info">
                    <strong>Fines & Payments Department</strong><br>
                    University Central Library<br>
                    Library Building, Room 101<br>
                    Email: fines@university.edu<br>
                    Phone: 098 765 432
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 57%;">Description & Details</th>
                    <th style="width: 15%; text-align: center;">Date</th>
                    <th style="width: 20%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                ${itemsHTML}
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>$${totalAmount.toFixed(2)}</span>
            </div>
            <div class="total-row">
                <span>Late Payment Fee:</span>
                <span>$0.00</span>
            </div>
            <div class="total-row">
                <span>Processing Fee:</span>
                <span>$0.00</span>
            </div>
            <div class="total-row" style="background: #e8f4fd; padding: 15px; border-radius: 5px;">
                <span><strong>TOTAL:</strong></span>
                <span style="color: #d32f2f; font-size: 20px;"><strong>$${totalAmount.toFixed(2)}</strong></span>
            </div>
        </div>

        <div class="footer">
            <p><strong>University Library Services - Official Invoice</strong></p>
            <p>This document constitutes an official invoice from University Library Services. Please retain for your records.</p>
            <p>Invoice generated electronically on ${new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
            <p style="color: #999; margin-top: 10px;">Invoice ID: ${invoiceNo} | Reference: LIB-FINE-${user.id}</p>
        </div>

        <div class="stamp">
            <div class="stamp-text">Unpaid</div>
        </div>
    </div>
</div>

<div class="print-controls">
    <button class="print-btn" onclick="window.print()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        Print Invoice
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add print functionality
    window.printInvoice = function() {
        window.print();
    };
});
<\/script>
</body>
</html>
`;

    // Set iframe content and show popup
    const iframe = document.getElementById('invoiceFrame');
    iframe.srcdoc = invoiceHTML;
    
    // Show popup
    document.getElementById('invoicePopup').style.display = 'block';
    
    // Prevent body scroll when popup is open
    document.body.style.overflow = 'hidden';
    
    // Auto-print after a short delay when iframe loads
    iframe.onload = function() {
        // Wait a bit for content to render
        setTimeout(() => {
            iframe.contentWindow.focus();
        }, 500);
    };
}

function closeInvoice() {
    document.getElementById('invoicePopup').style.display = 'none';
    document.body.style.overflow = 'auto';
}
</script>
@endsection