@extends('layouts.librarian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-clock me-2"></i>Reservations Management
    </h1>
    <a href="{{ route('librarian.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Reservations</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReservations ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bookmark fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Waiting</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $waitingCount ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Ready</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $readyCount ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
</div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Expiring Soon</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiringCount ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('librarian.reservations.index') }}" 
               class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                All ({{ $totalReservations ?? 0 }})
            </a>
            <a href="{{ route('librarian.reservations.index', ['status' => 'waiting']) }}" 
               class="btn btn-sm {{ request('status') == 'waiting' ? 'btn-warning' : 'btn-outline-warning' }}">
                Waiting ({{ $waitingCount ?? 0 }})
            </a>
            <a href="{{ route('librarian.reservations.index', ['status' => 'ready']) }}" 
               class="btn btn-sm {{ request('status') == 'ready' ? 'btn-success' : 'btn-outline-success' }}">
                Ready ({{ $readyCount ?? 0 }})
            </a>
            <a href="{{ route('librarian.reservations.index', ['status' => 'active']) }}" 
               class="btn btn-sm {{ request('status') == 'active' ? 'btn-info' : 'btn-outline-info' }}">
                Active ({{ $activeCount ?? 0 }})
            </a>
            <a href="{{ route('librarian.reservations.index', ['status' => 'expiring']) }}" 
               class="btn btn-sm {{ request('status') == 'expiring' ? 'btn-danger' : 'btn-outline-danger' }}">
                Expiring ({{ $expiringCount ?? 0 }})
            </a>
            <a href="{{ route('librarian.reservations.index', ['status' => 'expired']) }}" 
               class="btn btn-sm {{ request('status') == 'expired' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                Expired ({{ $expiredCount ?? 0 }})
            </a>
            <a href="{{ route('librarian.reservations.index', ['status' => 'cancelled']) }}" 
               class="btn btn-sm {{ request('status') == 'cancelled' ? 'btn-dark' : 'btn-outline-dark' }}">
                Cancelled ({{ $cancelledCount ?? 0 }})
            </a>
            <a href="{{ route('librarian.reservations.index', ['status' => 'picked_up']) }}" 
               class="btn btn-sm {{ request('status') == 'picked_up' ? 'btn-info' : 'btn-outline-info' }}">
                Picked Up ({{ $pickedUpCount ?? 0 }})
            </a>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        @if($reservations->count() > 0)
            <!-- Desktop/Tablet Table View -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="30%">Book</th>
                            <th width="15%">Reserved On</th>
                            <th width="20%">Pickup Status</th>
                            <th width="20%">Book Status</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($reservations as $reservation)
                            <tr>
                                <!-- Book Column -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($reservation->book->image)
                                            <img src="{{ asset('storage/' . $reservation->book->image) }}" 
                                                alt="{{ $reservation->book->title }}" 
                                                class="img-thumbnail me-3" 
                                                style="width: 60px; height: 80px; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='{{ asset('images/default-book.jpg') }}';">
                                        @else
                                            <div class="img-thumbnail book-thumbnail-placeholder me-3 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-book text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="book-info">
                                            <strong class="d-block">{{ $reservation->book->title }}</strong>
                                            <small class="text-muted d-block">by {{ $reservation->book->author }}</small>
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-barcode me-1"></i>ISBN: {{ $reservation->book->isbn }}
                                            </small>
                                            
                                            <!-- User Information -->
                                            <div class="mt-2 pt-2 border-top">
                                                @if($reservation->user)
                                                    <div class="borrower-info">
                                                        <p class="mb-1 text-muted small">
                                                            <i class="fas fa-user me-1"></i><strong>Reserved By</strong>
                                                        </p>
                                                        <div class="mb-2">
                                                            <div class="text-primary fw-semibold small">
                                                                <i class="fas fa-user fa-xs me-1"></i> {{ $reservation->user->name }}
                                                            </div>
                                                            <div class="text-muted small">
                                                                <i class="fas fa-phone fa-xs me-1"></i> {{ $reservation->user->contact ?? 'No contact' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Reserved On Column -->
                                <td>
                                    <div class="date-info">
                                        <strong class="d-block">
                                            {{ $reservation->reservation_date ? $reservation->reservation_date->format('M d, Y') : 'Not set' }}
                                        </strong>
                                        <small class="text-muted">
                                            {{ $reservation->reservation_date ? $reservation->reservation_date->format('g:i A') : '' }}
                                        </small>
                                    </div>
                                </td>
                                
                                <!-- Pickup Status Column -->
                                <td>
                                    @if($reservation->isWaiting())
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-warning me-2">
                                                <i class="fas fa-clock me-1"></i>Waiting
                                            </span>
                                        </div>
                                        @if($reservation->getCurrentCheckout())
                                            <div class="mt-1">
                                                <small class="text-muted d-block">
                                                    Book is checked out
                                                </small>
                                            </div>
                                        @endif
                                    @elseif($reservation->isReady())
                                        <div>
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-success me-2">
                                                    <i class="fas fa-check-circle me-1"></i>Ready
                                                </span>
                                                @if($reservation->expiry_date)
                                                    @php
                                                        $daysLeft = $reservation->getDaysUntilExpiry();
                                                    @endphp
                                                    @if($daysLeft !== null)
                                                        @if($daysLeft == 0)
                                                            <span class="badge bg-danger">
                                                                Due Today
                                                            </span>
                                                        @elseif($daysLeft <= 3)
                                                            <span class="badge bg-warning">
                                                                {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }} left
                                                            </span>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            @if($reservation->expiry_date)
                                                <strong class="d-block">
                                                    Pickup by: {{ $reservation->expiry_date->format('M d, Y') }}
                                                </strong>
                                                <small class="text-muted d-block">
                                                    {{ $reservation->getPickupDeadlineMessage() }}
                                                </small>
                                            @endif
                                        </div>
                                   @elseif($reservation->isPickedUp())
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge bg-info me-2">
                                                <i class="fas fa-book-reader me-1"></i>Picked Up
                                            </span>
                                        </div>
                                        @if($reservation->updated_at)
                                            <small class="text-muted d-block">
                                                <b>{{ $reservation->updated_at->format('M d, Y h:i A') }}</b>
                                            </small>
                                        @endif
                                    </div>
                                    @elseif($reservation->isExpired())
                                        <div class="expired-reservation">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Expired
                                                </span>
                                            </div>
                                            @if($reservation->expiry_date)
                                                <small>
                                                    <div class="text-muted d-block">
                                                        <b>{{ $reservation->expiry_date->format('M d, Y') }}</b>
                                                    </div>
                                                </small>
                                            @endif
                                        </div>
                                    @elseif($reservation->isCancelled())
                                        <div class="cancelled-reservation">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>Cancelled
                                                </span>
                                            </div>
                                            <div>
                                                @if($reservation->status === 'cancelled_by_user')
                                                    <small class="text-muted d-block">
                                                        <b>Cancelled by Reserver</b>
                                                    </small>
                                                @elseif($reservation->status === 'cancelled_by_librarian')
                                                    <small class="text-muted d-block">
                                                        <b>Cancelled by Librarian</b>
                                                    </small>
                                                @endif
                                                @if($reservation->updated_at)
                                                    <small class="text-muted d-block">
                                                        <b>{{ $reservation->updated_at->format('M d, Y h:i A') }}</b>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                
                                <!-- Book Status Column -->
                                <td>
                                    @if($reservation->isWaiting())
                                        @php
                                            $currentCheckout = $reservation->getCurrentCheckout();
                                        @endphp
                                        <div>
                                            @if($currentCheckout)
                                                <strong class="d-block text-warning">
                                                    <i class="fas fa-book-reader me-1"></i>Checked Out
                                                </strong>
                                                <small class="text-muted">
                                                    Due: {{ $currentCheckout->due_date->format('M d, Y') }}
                                                </small>
                                                @if($currentCheckout->user)
                                                    <small class="text-muted d-block">
                                                        By: {{ $currentCheckout->user->name }}
                                                    </small>
                                                @endif
                                            @else
                                                <strong class="d-block">
                                                    Expected: {{ $reservation->getFormattedExpectedDueDate() }}
                                                </strong>
                                                <small class="text-muted">
                                                    {{ $reservation->getDaysLeftMessage() }}
                                                </small>
                                            @endif
                                        </div>
                                    @elseif($reservation->isReady())
                                        <div>
                                            <strong class="d-block text-success">
                                                <i class="fas fa-check-circle me-1"></i>Available
                                            </strong>
                                            <small class="text-muted d-block">
                                                Book is at library
                                            </small>
                                        </div>
                                    @elseif($reservation->isPickedUp())
                                        @php
                                            // Find the checkout record for this picked up book
                                            $checkout = App\Models\Checkout::where('book_id', $reservation->book_id)
                                                ->where('user_id', $reservation->user_id)
                                                ->whereNull('return_date')
                                                ->whereIn('status', ['borrowed', 'approved', 'checked_out'])
                                                ->latest()
                                                ->first();
                                        @endphp

                                        <div>
                                            @if($checkout)
                                                <strong class="d-block text-info">
                                                    <i class="fas fa-book me-1"></i>Checked Out
                                                </strong>
                                                <small class="text-muted d-block">
                                                    To: {{ $reservation->user->name ?? 'user' }}
                                                </small>
                                                @if($checkout->due_date)
                                                    <small class="text-muted">
                                                        Due: {{ $checkout->due_date->format('M d, Y') }}
                                                    </small>
                                                @endif
                                            @else
                                                <strong class="d-block text-info">
                                                    <i class="fas fa-book-reader me-1"></i>With Librarian
                                                </strong>
                                                <small class="text-muted d-block">
                                                    {{ $reservation->user->name ?? 'user' }} has the book
                                                </small>
                                            @endif
                                        </div>
                                    @elseif($reservation->isExpired())
                                        <div>
                                            <strong class="d-block text-danger">
                                                <i class="fas fa-exclamation-circle me-1"></i>Available Again
                                            </strong>
                                            <small class="text-muted">
                                                Will go to next in queue
                                            </small>
                                        </div>
                                    @elseif($reservation->isCancelled())
                                        <div>
                                            <strong class="text-muted">
                                                Not available
                                            </strong>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Actions Column -->
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown" 
                                                aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($reservation->isWaiting())
                                                <form action="{{ route('librarian.reservations.confirm', $reservation) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success">
                                                        <i class="fas fa-check-circle me-2"></i>Mark as Ready
                                                    </button>
                                                </form>
                                                <form action="{{ route('librarian.reservations.cancel', $reservation) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-times me-2"></i>Cancel
                                                    </button>
                                                </form>
                                            @elseif($reservation->isReady())
                                                <form action="{{ route('librarian.reservations.markAsPickedUp', $reservation) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-info">
                                                        <i class="fas fa-book-reader me-2"></i>Mark as Picked Up
                                                    </button>
                                                </form>
                                                <form action="{{ route('librarian.reservations.expire', $reservation) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-warning">
                                                        <i class="fas fa-exclamation-circle me-2"></i>Mark as Expired
                                                    </button>
                                                </form>
                                                <form action="{{ route('librarian.reservations.cancel', $reservation) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item text-secondary">
                                                        <i class="fas fa-times me-2"></i>Cancel
                                                    </button>
                                                </form>
                                            @elseif($reservation->isPickedUp() || $reservation->isExpired() || $reservation->isCancelled())
                                                <form action="{{ route('librarian.reservations.cancel', $reservation) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item text-secondary">
                                                        <i class="fas fa-times me-2"></i>Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
          <!-- Mobile Card View -->
<div class="d-lg-none">
@foreach($reservations as $reservation)
    <div class="card mb-3 border">
        <div class="card-body">

            <!-- Book Info -->
            <div class="d-flex align-items-start mb-3">
                @if($reservation->book && $reservation->book->image)
                    <img src="{{ asset('storage/' . $reservation->book->image) }}"
                         alt="{{ $reservation->book->title }}"
                         class="img-thumbnail me-3"
                         style="width:60px;height:80px;object-fit:cover"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-book.jpg') }}';">
                @else
                    <div class="img-thumbnail me-3 d-flex align-items-center justify-content-center"
                         style="width:60px;height:80px">
                        <i class="fas fa-book text-muted"></i>
                    </div>
                @endif

                <div class="flex-grow-1">
                    <strong class="d-block">{{ $reservation->book->title ?? 'Unknown Book' }}</strong>
                    <small class="text-muted d-block">
                        by {{ $reservation->book->author ?? 'Unknown Author' }}
                    </small>
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-barcode me-1"></i>
                        ISBN: {{ $reservation->book->isbn ?? '-' }}
                    </small>

                    <!-- Reserver Info -->
                    @if($reservation->user)
                        <div class="mt-2 pt-2 border-top">
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-user me-1"></i><strong>Reserved By</strong>
                            </small>
                            <div class="text-primary fw-semibold small">
                                {{ $reservation->user->name }}
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-phone fa-xs me-1"></i>
                                {{ $reservation->user->contact ?? 'No contact' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reserved On + Book Status -->
            <div class="row mb-3">
                <!-- Reserved On -->
                <div class="col-6">
                    <small class="text-muted d-block mb-1">Reserved On</small>
                    <strong class="d-block">
                        {{ optional($reservation->reservation_date)->format('M d, Y') ?? 'Not set' }}
                    </strong>
                    <small class="text-muted">
                        {{ optional($reservation->reservation_date)->format('g:i A') }}
                    </small>
                </div>

                <!-- Book Status -->
                <div class="col-6">
                    <small class="text-muted d-block mb-1">Book Status</small>

                    @if($reservation->isWaiting())
                        @php $checkout = $reservation->getCurrentCheckout(); @endphp
                        @if($checkout)
                            <strong class="text-warning small d-block">
                                <i class="fas fa-book-reader me-1"></i>Checked Out
                            </strong>
                            <small class="text-muted">
                                Due: {{ $checkout->due_date->format('M d, Y') }}
                            </small>
                        @else
                            <strong class="small d-block">-</strong>
                        @endif

                    @elseif($reservation->isReady())
                        <strong class="text-success small d-block">
                            <i class="fas fa-check-circle me-1"></i>Available
                        </strong>

                    @elseif($reservation->isPickedUp())
                        <strong class="text-info small d-block">
                            <i class="fas fa-book me-1"></i>Checked Out
                        </strong>

                    @elseif($reservation->isExpired())
                        <strong class="text-danger small d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>Available Again
                        </strong>

                    @elseif($reservation->isCancelled())
                        <strong class="text-secondary small d-block">
                            <i class="fas fa-times me-1"></i>Cancelled
                        </strong>
                    @endif
                </div>
            </div>

            <!-- Pickup Status -->
            <div class="mb-3">
                <small class="text-muted d-block mb-1">Pickup Status</small>

                @if($reservation->isWaiting())
                    <span class="badge bg-warning">
                        <i class="fas fa-clock me-1"></i>Waiting
                    </span>

                @elseif($reservation->isReady())
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>Ready
                    </span>

                @elseif($reservation->isPickedUp())
                    <span class="badge bg-info">
                        <i class="fas fa-book-reader me-1"></i>Picked Up
                    </span>

                @elseif($reservation->isExpired())
                    <span class="badge bg-danger">
                        <i class="fas fa-exclamation-circle me-1"></i>Expired
                    </span>

                @elseif($reservation->isCancelled())
                    <span class="badge bg-secondary">
                        <i class="fas fa-times me-1"></i>Cancelled
                    </span>
                @endif
            </div>

            <!-- Actions -->
            <div class="d-grid">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle w-100"
                            type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="fas fa-cog me-1"></i>Actions
                    </button>

                    <ul class="dropdown-menu w-100">

                        @if($reservation->isWaiting())
                            <form action="{{ route('librarian.reservations.confirm', $reservation) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-success">
                                    <i class="fas fa-check-circle me-2"></i>Mark as Ready
                                </button>
                            </form>

                            <form action="{{ route('librarian.reservations.cancel', $reservation) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                            </form>

                        @elseif($reservation->isReady())
                            <form action="{{ route('librarian.reservations.markAsPickedUp', $reservation) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-info">
                                    <i class="fas fa-book-reader me-2"></i>Mark as Picked Up
                                </button>
                            </form>

                            <form action="{{ route('librarian.reservations.expire', $reservation) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-warning">
                                    <i class="fas fa-exclamation-circle me-2"></i>Mark as Expired
                                </button>
                            </form>

                            <form action="{{ route('librarian.reservations.cancel', $reservation) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item text-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                            </form>

                        @elseif($reservation->isPickedUp() || $reservation->isExpired() || $reservation->isCancelled())
                            <form action="{{ route('librarian.reservations.cancel', $reservation) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item text-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                            </form>
                        @endif

                    </ul>
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>


            <!-- Pagination -->
            @if($reservations->hasPages())
                <div class="mt-4">
                    <!-- Results Count (Top) -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                        <div class="mb-2 mb-md-0 text-muted small">
                            Showing {{ $reservations->firstItem() ?? 0 }} to {{ $reservations->lastItem() ?? 0 }} 
                            of {{ $reservations->total() }} results
                        </div>
                        
                        <!-- Items per page selector -->
                        <div class="d-flex align-items-center">
                            <span class="me-2 small text-muted">Items per page:</span>
                            <select class="form-select form-select-sm w-auto" onchange="window.location.href = this.value">
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 100]) }}" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pagination Links -->
                    <nav aria-label="Reservations pagination">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            <li class="page-item {{ $reservations->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $reservations->previousPageUrl() }}" 
                                   aria-label="Previous" {{ $reservations->onFirstPage() ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                    <i class="fas fa-chevron-left"></i>
                                    <span class="d-none d-sm-inline ms-1">Previous</span>
                                </a>
                            </li>

                            {{-- Page Numbers --}}
                            @foreach ($reservations->getUrlRange(1, $reservations->lastPage()) as $page => $url)
                                @if($page == $reservations->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @elseif($page == 1 || $page == $reservations->lastPage() || ($page >= $reservations->currentPage() - 1 && $page <= $reservations->currentPage() + 1))
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @elseif($page == $reservations->currentPage() - 2 || $page == $reservations->currentPage() + 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            <li class="page-item {{ !$reservations->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $reservations->nextPageUrl() }}" 
                                   aria-label="Next" {{ !$reservations->hasMorePages() ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                    <span class="d-none d-sm-inline me-1">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Reservations Found</h4>
                <p class="text-muted">There are no book reservations in the system.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .book-info .border-top {
        border-top: 1px solid #e0e0e0 !important;
    }
    
    .book-info .text-muted {
        font-size: 0.85rem;
    }
    
    .book-info .small {
        font-size: 0.9rem;
    }
    
    .book-thumbnail-placeholder {
        width: 60px;
        height: 80px;
        background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.8em;
    }
    
    @media (max-width: 1199px) {
        .table td, .table th {
            padding: 0.5rem;
            font-size: 0.875rem;
        }
        
        .book-info strong {
            font-size: 0.95rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('img[src*="storage/"]').forEach(img => {
            img.addEventListener('error', function() {
                this.src = '{{ asset("images/default-book.jpg") }}';
                this.classList.add('img-thumbnail');
            });
        });
    });
</script>
@endpush