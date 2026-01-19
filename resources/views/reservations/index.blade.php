@extends('layouts.student')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="card shadow-sm mb-4 border-0 bg-primary text-white">
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div>
                            <h1 class="h3 mb-2">
                                <i class="fas fa-bookmark me-2"></i>My Book Reservations
                            </h1>
                            <p class="mb-0 opacity-75">Track and manage all your book reservations in one place</p>
                        </div>
                        <a href="{{ route('welcome') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-plus me-2"></i>Reserve More Books
                        </a>
                    </div>
                </div>
            </div>

       

            <!-- Status Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-lg me-3"></i>
                        <div>
                            <strong>Success!</strong> {{ session('success') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle fa-lg me-3"></i>
                        <div>
                            <strong>Error!</strong> {{ session('error') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Main Content Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    @if($reservations->count() > 0)
                        <!-- Desktop/Tablet View -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="25%" class="ps-4">Book Details</th>
                                        <th width="15%">Reservation Info</th>
                                        <th width="20%">Status & Timeline</th>
                                        <th width="20%">Pickup Details</th>
                                        <th width="20%" class="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservations as $reservation)
                                    @php
                                        $daysLeft = $reservation->getDaysUntilExpiry();
                                        $daysLeft = $daysLeft !== null ? max(0, round($daysLeft)) : null;
                                        $daysUntilDue = $reservation->getDaysLeftUntilReturn();
                                        $queuePosition = $reservation->getQueuePosition();
                                    @endphp
                                        <tr>
                                            <!-- Book Column -->
                                            <td class="ps-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="position-relative">
                                                        @if($reservation->book->image)
                                                            <img src="{{ asset('storage/' . $reservation->book->image) }}" 
                                                                 alt="{{ $reservation->book->title }}" 
                                                                 class="rounded me-3" 
                                                                 style="width: 60px; height: 80px; object-fit: cover;"
                                                                 onerror="this.onerror=null; this.src='{{ asset('images/default-book.jpg') }}';">
                                                        @else
                                                            <div class="book-thumbnail-small rounded d-flex align-items-center justify-content-center me-3">
                                                                <i class="fas fa-book text-muted"></i>
                                                            </div>
                                                        @endif
                                                        @if($queuePosition > 0 && $reservation->isWaiting())
                                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                                                #{{ $queuePosition }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong class="d-block text-truncate" style="max-width: 200px;">
                                                            {{ $reservation->book->title }}
                                                        </strong>
                                                        <small class="text-muted d-block">by {{ $reservation->book->author }}</small>
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-barcode me-1"></i>{{ $reservation->book->isbn }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Reservation Info -->
                                            <td>
                                                <div class="reservation-info">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Reserved On</small>
                                                        <strong class="d-block">
                                                            {{ $reservation->reservation_date ? $reservation->reservation_date->format('M d, Y') : 'N/A' }}
                                                        </strong>
                                                        <small class="text-muted">
                                                            {{ $reservation->reservation_date ? $reservation->reservation_date->format('g:i A') : '' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Status & Timeline -->
                                            <td>
                                                <div class="status-timeline">
                                                    <!-- Status Badge -->
                                                    @if($reservation->isWaiting())
                                                        <div class="mb-2">
                                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                                                <i class="fas fa-clock me-1"></i>Waiting in Queue
                                                            </span>
                                                            @if($queuePosition > 0)
                                                                <small class="d-block text-muted mt-1">
                                                                    Position: #{{ $queuePosition }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                        @if($daysUntilDue !== null)
                                                            <div class="progress mb-2" style="height: 6px;">
                                                                @if($daysUntilDue > 0)
                                                                    @php
                                                                        $progressPercent = min(100, max(0, (14 - $daysUntilDue) / 14 * 100));
                                                                    @endphp
                                                                    <div class="progress-bar bg-info" role="progressbar" 
                                                                         style="width: {{ $progressPercent }}%"
                                                                         aria-valuenow="{{ $progressPercent }}" 
                                                                         aria-valuemin="0" 
                                                                         aria-valuemax="100">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <small class="d-block {{ $daysUntilDue <= 3 ? 'text-danger' : 'text-muted' }}">
                                                                <i class="fas fa-calendar-alt me-1"></i>
                                                                @if($daysUntilDue > 0)
                                                                    Expected in {{ round($daysUntilDue) }} day{{ round($daysUntilDue) != 1 ? 's' : '' }}
                                                                @elseif($daysUntilDue == 0)
                                                                    Expected today
                                                                @else
                                                                    Overdue by {{ abs(round($daysUntilDue)) }} days
                                                                @endif
                                                            </small>
                                                        @endif
                                                    @elseif($reservation->isReady())
                                                        <div class="mb-2">
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                                <i class="fas fa-check-circle me-1"></i>Ready for Pickup
                                                            </span>
                                                        </div>
                                                        @if($daysLeft !== null)
                                                            <div class="progress mb-2" style="height: 6px;">
                                                                @php
                                                                    $progressPercent = max(0, min(100, (3 - $daysLeft) / 3 * 100));
                                                                @endphp
                                                                <div class="progress-bar {{ $daysLeft <= 1 ? 'bg-danger' : ($daysLeft <= 2 ? 'bg-warning' : 'bg-success') }}" 
                                                                     role="progressbar" 
                                                                     style="width: {{ $progressPercent }}%"
                                                                     aria-valuenow="{{ $progressPercent }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <small class="d-block {{ $daysLeft <= 1 ? 'text-danger fw-bold' : 'text-muted' }}">
                                                                <i class="fas fa-clock me-1"></i>
                                                                @if($daysLeft == 0)
                                                                    <strong>Pickup due today!</strong>
                                                                @elseif($daysLeft == 1)
                                                                    <strong>Pickup tomorrow</strong>
                                                                @else
                                                                    {{ $daysLeft }} days left to pickup
                                                                @endif
                                                            </small>
                                                        @endif
                                                    @elseif($reservation->isPickedUp())
                                                        <div class="mb-2">
                                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                                                <i class="fas fa-book-reader me-1"></i>Picked Up
                                                            </span>
                                                        </div>
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-calendar-check me-1"></i>
                                                            Picked up on {{ $reservation->updated_at->format('M d, Y') }}
                                                        </small>
                                                    @elseif($reservation->isCancelled())
                                                        <div class="mb-2">
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                                                <i class="fas fa-times me-1"></i>Cancelled
                                                            </span>
                                                        </div>
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-calendar-times me-1"></i>
                                                            Cancelled on {{ $reservation->updated_at->format('M d, Y') }}
                                                        </small>
                                                        @if($reservation->status === 'cancelled_by_user')
                                                            <small class="text-muted d-block">
                                                                Cancelled by you
                                                            </small>
                                                        @else
                                                            <small class="text-muted d-block">
                                                                Cancelled by librarian
                                                            </small>
                                                        @endif
                                                    @elseif($reservation->isExpired())
                                                        <div class="mb-2">
                                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                                <i class="fas fa-exclamation-circle me-1"></i>Expired
                                                            </span>
                                                        </div>
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            Expired on {{ $reservation->updated_at->format('M d, Y') }}
                                                        </small>
                                                        <small class="text-danger d-block">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Pickup window missed
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            
                                            <!-- Pickup Details -->
                                            <td>
                                                @if($reservation->isWaiting())
                                                    <div class="pickup-info">
                                                        <small class="text-muted d-block">Expected Return</small>
                                                        <strong class="d-block">
                                                            {{ $reservation->getFormattedExpectedDueDate() }}
                                                        </strong>
                                                        <small class="text-muted d-block mt-2">Pickup Location</small>
                                                        <small class="text-info">
                                                            <i class="fas fa-map-marker-alt me-1"></i>
                                                            Main Library Desk
                                                        </small>
                                                    </div>
                                                @elseif($reservation->isReady())
                                                    <div class="pickup-info">
                                                        <small class="text-muted d-block">Pickup Deadline</small>
                                                        <strong class="d-block {{ $daysLeft <= 1 ? 'text-danger' : '' }}">
                                                            {{ $reservation->expiry_date->format('M d, Y') }}
                                                        </strong>
                                                        @if($daysLeft <= 1)
                                                            <small class="text-danger d-block mt-1">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                                Urgent: Pickup needed
                                                            </small>
                                                        @endif
                                                        <small class="text-muted d-block mt-2">Pickup Location</small>
                                                        <small class="text-success">
                                                            <i class="fas fa-map-marker-alt me-1"></i>
                                                            Main Library Desk
                                                        </small>
                                                        {{-- <small class="">Hours</small> --}}
                                                        <small class="text-muted d-block mt-2">
                                                            <i class="fas fa-clock me-1"></i>
                                                            Mon-Fri: 9AM-5PM
                                                        </small>
                                                    </div>
                                                @elseif($reservation->isPickedUp())
                                                    <div class="pickup-info">
                                                        <small class="text-muted d-block">Checked Out On</small>
                                                        <strong class="d-block">
                                                            {{ $reservation->updated_at->format('M d, Y') }}
                                                        </strong>                    
                                                    </div>
                                                @else
                                                    <div class="pickup-info">
                                                        <small class="text-muted">No pickup details available</small>
                                                    </div>
                                                @endif
                                            </td>
                                            
                                            <!-- Actions -->
                                            <td class="text-center pe-4">
                                                <div class="d-flex flex-column gap-2 align-items-center">
                                                    @if($reservation->canBeCancelled())
                                                        <form action="{{ route('reservations.cancel', $reservation) }}" method="POST" class="w-100">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger btn-sm w-100"
                                                                    onclick="return confirm('Are you sure you want to cancel this reservation?')">
                                                                <i class="fas fa-times me-1"></i>Cancel
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($reservation->isReady())
                                                        <a href="#" class="btn btn-success btn-sm w-100">
                                                            <i class="fas fa-directions me-1"></i>Pickup Now
                                                        </a>
                                                        <small class="text-muted text-center">
                                                            Bring your ID to the library
                                                        </small>
                                                    @elseif($reservation->isPickedUp())
                                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                                            <i class="fas fa-check-circle me-1"></i>Completed
                                                        </span>
                                                    @elseif($reservation->isWaiting())
                                                        <button class="btn btn-outline-info btn-sm w-100" disabled>
                                                            <i class="fas fa-spinner fa-spin me-1"></i>In Queue
                                                        </button>
                                                        @if($queuePosition == 1)
                                                            <small class="text-success text-center">
                                                                <i class="fas fa-crown me-1"></i>Next in line
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">No actions available</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-md-none">
                            @foreach($reservations as $reservation)
                                @php
                                    $daysLeft = $reservation->getDaysUntilExpiry();
                                    $daysLeft = $daysLeft !== null ? max(0, round($daysLeft)) : null;
                                    $daysUntilDue = $reservation->getDaysLeftUntilReturn();
                                    $queuePosition = $reservation->getQueuePosition();
                                @endphp

                                <div class="reservation-mobile-card border-bottom p-3 mb-3 bg-white shadow-sm rounded">
                                    <!-- Book Header -->
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="position-relative me-3">
                                            @if($reservation->book->image)
                                                <img src="{{ asset('storage/' . $reservation->book->image) }}" 
                                                    alt="{{ $reservation->book->title }}" 
                                                    class="rounded" 
                                                    style="width: 60px; height: 85px; object-fit: cover;"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/default-book.jpg') }}';">
                                            @else
                                                <div class="book-thumbnail-mobile rounded d-flex align-items-center justify-content-center" 
                                                    style="width:60px; height:85px; background:#f0f0f0;">
                                                    <i class="fas fa-book text-muted fa-lg"></i>
                                                </div>
                                            @endif

                                            @if($queuePosition > 0 && $reservation->isWaiting())
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                                    #{{ $queuePosition }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 text-truncate" title="{{ $reservation->book->title }}">
                                                {{ $reservation->book->title }}
                                            </h6>
                                            <small class="text-muted d-block text-truncate" title="{{ $reservation->book->author }}">
                                                by {{ $reservation->book->author }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-barcode me-1"></i>{{ $reservation->book->isbn }}
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Status Card -->
                                    <div class="card mb-2 border-{{ $reservation->isWaiting() ? 'warning' : ($reservation->isReady() ? 'success' : ($reservation->isPickedUp() ? 'info' : 'secondary')) }}">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="small fw-bold text-truncate">
                                                    @if($reservation->isWaiting())
                                                        <span class="text-warning"><i class="fas fa-clock me-1"></i>Waiting in Queue</span>
                                                    @elseif($reservation->isReady())
                                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i>Ready for Pickup</span>
                                                    @elseif($reservation->isPickedUp())
                                                        <span class="text-info"><i class="fas fa-book-reader me-1"></i>Picked Up</span> - {{ $reservation->reservation_date->format('M d, Y h:i A') }}
                                                    @elseif($reservation->isCancelled())
                                                        <span class="text-secondary"><i class="fas fa-times me-1"></i>Cancelled</span> - {{ $reservation->reservation_date->format('M d, Y h:i A') }}
                                                    @elseif($reservation->isExpired())
                                                        <span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Expired</span>
                                                    @endif
                                                </div>
                                                @if($daysLeft !== null && $reservation->isReady())
                                                    <span class="badge {{ $daysLeft <= 1 ? 'bg-danger' : ($daysLeft <= 2 ? 'bg-warning' : 'bg-success') }}">
                                                        {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Timeline Info -->
                                            @if($reservation->isWaiting())
                                                <div class="small text-muted">
                                                    <div>Expected Return: <strong>{{ $reservation->getFormattedExpectedDueDate() }}</strong></div>
                                                    @if($daysUntilDue !== null)
                                                        <div class="{{ $daysUntilDue <= 3 ? 'text-warning' : '' }}">
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            @if($daysUntilDue > 0)
                                                                In {{ $daysUntilDue }} day{{ $daysUntilDue != 1 ? 's' : '' }}
                                                            @elseif($daysUntilDue == 0)
                                                                Today
                                                            @else
                                                                Overdue by {{ abs($daysUntilDue) }} days
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($reservation->isReady())
                                                <div class="small text-muted">
                                                    Pickup Deadline: <strong class="{{ $daysLeft <= 1 ? 'text-danger' : '' }}">
                                                        {{ $reservation->expiry_date->format('M d, Y') }}
                                                    </strong>
                                                    @if($daysLeft <= 1)
                                                        <div class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Pickup urgently needed</div>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Progress Bar -->
                                            @if(($reservation->isWaiting() && $daysUntilDue !== null) || ($reservation->isReady() && $daysLeft !== null))
                                                @php
                                                    $progressPercent = $reservation->isWaiting() 
                                                        ? min(100, max(0, (14 - $daysUntilDue) / 14 * 100)) 
                                                        : max(0, min(100, (3 - $daysLeft) / 3 * 100));
                                                    $progressColor = $reservation->isWaiting() 
                                                        ? 'bg-info' 
                                                        : ($daysLeft <= 1 ? 'bg-danger' : ($daysLeft <= 2 ? 'bg-warning' : 'bg-success'));
                                                @endphp
                                                <div class="progress" style="height:5px;">
                                                    <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $progressPercent }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Details Grid -->
                                    <div class="d-flex gap-2 mb-2">
                                        <div class="flex-fill bg-light rounded p-2 text-center small">
                                            <div>Reserved On</div>
                                            <strong>{{ $reservation->reservation_date->format('M d, Y') }}</strong>
                                            <div>{{ $reservation->reservation_date->format('h:i A') }}</div>
                                        </div>
                                        <div class="flex-fill bg-light rounded p-2 text-center small">
                                            @if($reservation->isReady() && $reservation->expiry_date)
                                                <div>Pickup By</div>
                                                <strong class="{{ $daysLeft <= 1 ? 'text-danger' : '' }}">{{ $reservation->expiry_date->format('M d') }}</strong>
                                                <div>{{ $reservation->expiry_date->format('Y') }}</div>
                                            @else
                                                <div>Status</div>
                                                <strong>
                                                    @if($reservation->isWaiting())
                                                        In Queue
                                                    @elseif($reservation->isPickedUp())
                                                        Completed
                                                    @else
                                                        {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                                                    @endif
                                                </strong>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-grid gap-2">
                                        @if($reservation->canBeCancelled())
                                            <form action="{{ route('reservations.cancel', $reservation) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger w-100 btn-sm" onclick="return confirm('Are you sure you want to cancel this reservation?')">
                                                    <i class="fas fa-times me-1"></i>Cancel
                                                </button>
                                            </form>
                                        @endif

                                        @if($reservation->isReady())
                                            <button class="btn btn-success w-100 btn-sm">
                                                <i class="fas fa-directions me-1"></i>Pickup
                                            </button>
                                            <small class="text-center text-muted d-block">Bring student ID to main desk</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>


                        <!-- Improved Pagination -->
                        @if($reservations->hasPages())
                            <div class="border-top p-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                    <!-- Page Info -->
                                    <div class="text-center text-md-start">
                                        <small class="text-muted">
                                            Page <strong>{{ $reservations->currentPage() }}</strong> of 
                                            <strong>{{ $reservations->lastPage() }}</strong> • 
                                            Showing <strong>{{ $reservations->firstItem() }}</strong>-<strong>{{ $reservations->lastItem() }}</strong> 
                                            of <strong>{{ $reservations->total() }}</strong> reservations
                                        </small>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination pagination-sm mb-0">
                                            <!-- Previous -->
                                            <li class="page-item {{ $reservations->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $reservations->previousPageUrl() }}" 
                                                   aria-label="Previous" {{ $reservations->onFirstPage() ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>

                                            <!-- Page Numbers -->
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
                                                    <li class="page-item disabled d-none d-md-block">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                @endif
                                            @endforeach

                                            <!-- Next -->
                                            <li class="page-item {{ !$reservations->hasMorePages() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $reservations->nextPageUrl() }}" 
                                                   aria-label="Next" {{ !$reservations->hasMorePages() ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                    
                                    <!-- Per Page -->
                                    <div class="text-center text-md-end">
                                        <small class="text-muted">
                                            Show: 
                                            <a href="{{ request()->fullUrlWithQuery(['per_page' => 5]) }}" 
                                               class="{{ request('per_page', 10) == 5 ? 'fw-bold text-primary' : 'text-muted' }}">5</a> • 
                                            <a href="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" 
                                               class="{{ request('per_page', 10) == 10 ? 'fw-bold text-primary' : 'text-muted' }}">10</a> • 
                                            <a href="{{ request()->fullUrlWithQuery(['per_page' => 20]) }}" 
                                               class="{{ request('per_page', 10) == 20 ? 'fw-bold text-primary' : 'text-muted' }}">20</a>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5 px-3">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-bookmark fa-4x text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-3">No Reservations Found</h4>
                            <p class="text-muted mb-4">
                                @if(request('status'))
                                    You don't have any {{ request('status') }} reservations.
                                @else
                                    You haven't made any book reservations yet.
                                @endif
                            </p>
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                <a href="{{ route('welcome') }}" class="btn btn-primary">
                                    <i class="fas fa-book me-2"></i>Browse Books
                                </a>
                                <a href="{{ route('books.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-search me-2"></i>Search Library
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Book Thumbnails */
    .book-thumbnail-small {
        width: 60px;
        height: 80px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
    }
    
    .book-thumbnail-mobile {
        width: 70px;
        height: 90px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
    }
    
    /* Status Cards */
    .reservation-mobile-card {
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }
    
    .reservation-mobile-card:hover {
        background-color: #f8f9fa;
    }
    
    /* Progress Bars */
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 10px;
    }
    
    /* Badge Styles */
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    /* Table Styling */
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    /* Empty State */
    .empty-state-icon {
        opacity: 0.6;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .book-thumbnail-small,
        .book-thumbnail-mobile {
            width: 50px;
            height: 70px;
        }
        
        .reservation-mobile-card {
            padding: 1rem;
        }
        
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
    
    /* Custom Badge Styles */
    .badge.bg-opacity-10 {
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
    }
    
    .badge.bg-warning.bg-opacity-10 {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    
    .badge.bg-success.bg-opacity-10 {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    
    .badge.bg-danger.bg-opacity-10 {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    
    .badge.bg-info.bg-opacity-10 {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
    
    .badge.bg-secondary.bg-opacity-10 {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    
    /* Status Timeline */
    .status-timeline {
        position: relative;
    }
    
    .status-timeline::before {
        content: '';
        position: absolute;
        left: -15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #e9ecef 0%, #dee2e6 100%);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle broken images
        document.querySelectorAll('img[src*="storage/"]').forEach(img => {
            img.addEventListener('error', function() {
                this.src = '{{ asset("images/default-book.jpg") }}';
                this.classList.add('img-thumbnail');
            });
        });
        
        // Smooth scroll for pagination
        document.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.closest('.disabled') && !this.closest('.active')) {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Status update notifications
        @if(session('success'))
            // Show success toast if needed
            console.log('Reservation updated successfully');
        @endif
        
        // Auto-refresh for urgent statuses
        setInterval(function() {
            const urgentItems = document.querySelectorAll('.text-danger.fw-bold, .badge.bg-danger');
            if (urgentItems.length > 0) {
                // You could add auto-refresh logic here
                // For now, just add a subtle pulse animation
                urgentItems.forEach(item => {
                    item.classList.add('animate__animated', 'animate__pulse');
                    setTimeout(() => {
                        item.classList.remove('animate__animated', 'animate__pulse');
                    }, 1000);
                });
            }
        }, 30000); // Every 30 seconds
    });
</script>
@endpush