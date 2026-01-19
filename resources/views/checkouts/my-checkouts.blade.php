@extends('layouts.student')

@section('title', 'My Checkouts')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
                <h1 class="h3 mb-0">
                    <i class="fas fa-book-reader me-2"></i>My Checked Out Books
                </h1>
                <a href="{{ route('welcome') }}" class="btn btn-outline-primary">
                    <i class="fas fa-book me-1"></i> Browse More Books
                </a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($checkouts->count() > 0)
                <!-- Desktop/Tablet View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Book</th>
                                <th>Checkout Date</th>
                                <th>Due Date</th>
                                <th>Days Left</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($checkouts as $checkout)
                                @php
                                    // Initialize variables
                                    $dueDate = null;
                                    $checkoutDate = null;
                                    $daysRemaining = null;
                                    
                                    // Only parse dates if they exist
                                    if ($checkout->due_date) {
                                        $dueDate = \Carbon\Carbon::parse($checkout->due_date);
                                    }
                                    if ($checkout->checkout_date) {
                                        $checkoutDate = \Carbon\Carbon::parse($checkout->checkout_date);
                                    }
                                    
                                    // Determine status and calculate days remaining for checked_out books
                                    $statusClass = 'success';
                                    $statusText = '';
                                    
                                    if ($checkout->status === 'pending') {
                                        $statusClass = 'warning';
                                        $statusText = 'Pending Approval';
                                    } elseif ($checkout->status === 'approved') {
                                        $statusClass = 'info';
                                        $statusText = 'Approved';
                                    } elseif ($checkout->status === 'checked_out' && $dueDate) {
                                        $daysRemaining = now()->startOfDay()->diffInDays($dueDate->startOfDay(), false);
                                        
                                        if ($daysRemaining < 0) {
                                            $statusClass = 'danger';
                                            $statusText = 'Overdue';
                                        } elseif ($daysRemaining === 0) {
                                            $statusClass = 'warning';
                                            $statusText = 'Due Today';
                                        } else {
                                            $statusClass = 'success';
                                            $statusText = 'Checked Out';
                                        }
                                    }
                                    
                                    // Check extension status
                                    $isExtended = $checkout->extension_status === 'approved';
                                    $hasPendingExtension = $checkout->extension_requested && $checkout->extension_status === 'pending';
                                    $canRequestExtension = $checkout->status === 'checked_out' && 
                                                          !$checkout->return_date &&
                                                          !$isExtended && 
                                                          !$hasPendingExtension && 
                                                          $dueDate && 
                                                          $daysRemaining !== null && 
                                                          $daysRemaining >= 2;
                                    
                                    // Check if from reservation
                                    $isFromReservation = $checkout->reservation_id !== null;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($checkout->book->image)
                                                <img src="{{ asset('storage/' . $checkout->book->image) }}" 
                                                    alt="{{ $checkout->book->title }}" 
                                                    class="img-thumbnail me-3 book-image"
                                                    style="width: 70px; height: 100px; object-fit: cover;"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/default-book.jpg') }}';">
                                            @else
                                                <div class="book-thumbnail-placeholder rounded d-flex align-items-center justify-content-center me-3">
                                                    <i class="fas fa-book text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="book-info">
                                                @if($isFromReservation)
                                                    <span class="badge bg-info mb-1">
                                                        <i class="fas fa-bookmark me-1"></i>Reserved
                                                    </span>
                                                @endif
                                                <strong class="d-block">{{ $checkout->book->title }}</strong>
                                                <small class="text-muted d-block">by {{ $checkout->book->author }}</small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-barcode me-1"></i>ISBN: {{ $checkout->book->isbn }}
                                                </small>
                                                @if($isExtended)
                                                    <small class="text-success d-block">
                                                        <i class="fas fa-calendar-plus me-1"></i>Extended
                                                    </small>
                                                @elseif($hasPendingExtension)
                                                    <small class="text-warning d-block">
                                                        <i class="fas fa-clock me-1"></i>Extension Pending
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($checkoutDate)
                                            <strong class="d-block">{{ $checkoutDate->format('M d, Y') }}</strong>
                                            <small class="text-muted">{{ $checkoutDate->format('g:i A') }}</small>
                                        @elseif($isFromReservation)
                                            <small class="text-info">
                                                <i class="fas fa-calendar-check me-1"></i>Reservation Pickup
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dueDate)
                                            <strong class="d-block {{ $daysRemaining !== null && $daysRemaining < 0 ? 'text-danger' : '' }}">
                                                {{ $dueDate->format('M d, Y') }}
                                            </strong>
                                            @if($hasPendingExtension)
                                                <small class="text-info d-block">
                                                    <i class="fas fa-info-circle me-1"></i>+3 days if approved
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($checkout->status === 'checked_out' && $daysRemaining !== null)
                                            @if($daysRemaining == 14)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-calendar-check me-1"></i>14 days
                                                </span>
                                            @elseif($daysRemaining < 0)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ abs($daysRemaining) }} days overdue
                                                </span>
                                            @elseif($daysRemaining === 0)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>Due Today
                                                </span>
                                            @elseif($daysRemaining <= 3)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-calendar-alt me-1"></i>{{ $daysRemaining }} days
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fas fa-calendar-check me-1"></i>{{ $daysRemaining }} days
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($checkout->status === 'pending')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-hourglass-half me-1"></i>Pending
                                            </span>
                                        @elseif($checkout->status === 'approved')
                                            <span class="badge bg-info">
                                                <i class="fas fa-check-circle me-1"></i>Approved
                                            </span>
                                        @elseif($checkout->status === 'checked_out')
                                            <span class="badge bg-{{ $statusClass }}">
                                                <i class="fas fa-book-reader me-1"></i>{{ $statusText }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($checkout->status === 'checked_out')
                                                @if($canRequestExtension)
                                                    <form action="{{ route('checkouts.request-extension', $checkout) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-warning btn-sm" 
                                                                onclick="return confirm('Request 3-day extension for \"{{ $checkout->book->title }}\"? Librarian approval required.')"
                                                                title="Request Extension">
                                                            <i class="fas fa-calendar-plus"></i>
                                                        </button>
                                                    </form>
                                                @elseif($hasPendingExtension)
                                                    <button class="btn btn-outline-warning btn-sm" disabled title="Extension Pending">
                                                        <i class="fas fa-clock"></i>
                                                    </button>
                                                @elseif($isExtended)
                                                    <button class="btn btn-outline-success btn-sm" disabled title="Already Extended">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </button>
                                                @endif
                                            @elseif($checkout->status === 'approved')
                                                <span class="text-muted small">Ready for pickup</span>
                                            @elseif($checkout->status === 'pending')
                                                <div class="d-flex gap-1">
                                                   <form action="{{ route('checkouts.cancel', $checkout) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                            onclick="return confirm('Cancel checkout request for &quot;{{ $checkout->book->title }}&quot;?')"
                                                            title="Cancel Request">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('checkouts.destroy', $checkout) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm" 
                                                            onclick="return confirm('Delete checkout request for &quot;{{ $checkout->book->title }}&quot;? This action cannot be undone.')"
                                                            title="Delete Request">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View - 2 Column Layout -->
                <div class="d-md-none">
                    <div class="row">
                        @foreach($checkouts as $checkout)
                            @php
                                // Initialize variables
                                $dueDate = null;
                                $checkoutDate = null;
                                $daysRemaining = null;
                                
                                // Only parse dates if they exist
                                if ($checkout->due_date) {
                                    $dueDate = \Carbon\Carbon::parse($checkout->due_date);
                                }
                                if ($checkout->checkout_date) {
                                    $checkoutDate = \Carbon\Carbon::parse($checkout->checkout_date);
                                }
                                
                                // Determine status
                                $statusClass = 'success';
                                $statusText = '';
                                
                                if ($checkout->status === 'pending') {
                                    $statusClass = 'warning';
                                    $statusText = 'Pending Approval';
                                } elseif ($checkout->status === 'approved') {
                                    $statusClass = 'info';
                                    $statusText = 'Approved';
                                } elseif ($checkout->status === 'checked_out' && $dueDate) {
                                    $daysRemaining = now()->startOfDay()->diffInDays($dueDate->startOfDay(), false);
                                    
                                    if ($daysRemaining < 0) {
                                        $statusClass = 'danger';
                                        $statusText = 'Overdue';
                                    } elseif ($daysRemaining === 0) {
                                        $statusClass = 'warning';
                                        $statusText = 'Due Today';
                                    } else {
                                        $statusClass = 'success';
                                        $statusText = 'Checked Out';
                                    }
                                }
                                
                                // Check if from reservation
                                $isFromReservation = $checkout->reservation_id !== null;
                            @endphp
                            
                            <div class="col-6 mb-3">
                                <div class="card h-100">
                                    <!-- Book Title & Author -->
                                    <div class="p-3">
                                        @if($isFromReservation)
                                            <span class="badge bg-info mb-2 small">
                                                <i class="fas fa-bookmark me-1"></i>Reserved
                                            </span>
                                        @endif
                                        <h6 class="fw-bold mb-1 text-truncate">{{ $checkout->book->title }}</h6>
                                        <small class="text-muted">by {{ $checkout->book->author }}</small>
                                        <div class="mt-1 small">
                                            <i class="fas fa-barcode text-muted"></i>
                                            <span class="text-muted">ISBN: {{ $checkout->book->isbn }}</span>
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="px-3 mb-2">
                                        @if($checkout->status === 'pending')
                                            <span class="badge bg-warning w-100">Pending</span>
                                        @elseif($checkout->status === 'approved')
                                            <span class="badge bg-info w-100">Approved</span>
                                        @elseif($checkout->status === 'checked_out')
                                            <span class="badge bg-{{ $statusClass }} w-100">{{ $statusText }}</span>
                                        @endif
                                    </div>

                                    <!-- Quick Info -->
                                    <div class="px-3 mb-3">
                                        @if($checkoutDate)
                                            <div class="small mb-1">
                                                <i class="fas fa-calendar text-muted me-1"></i>
                                                {{ $checkoutDate->format('M d') }}
                                            </div>
                                        @elseif($isFromReservation)
                                            <div class="small mb-1">
                                                <i class="fas fa-bookmark text-info me-1"></i>
                                                Reservation Pickup
                                            </div>
                                        @endif
                                        
                                        @if($dueDate)
                                            <div class="small">
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                Due: {{ $dueDate->format('M d') }}
                                            </div>
                                        @endif
                                        
                                        @if($checkout->status === 'checked_out' && $daysRemaining !== null)
                                            <div class="mt-2">
                                                @if($daysRemaining == 14)
                                                    <span class="badge bg-success small">14 days</span>
                                                @elseif($daysRemaining < 0)
                                                    <span class="badge bg-danger small">{{ abs($daysRemaining) }} days overdue</span>
                                                @elseif($daysRemaining === 0)
                                                    <span class="badge bg-warning small">Due Today</span>
                                                @elseif($daysRemaining <= 3)
                                                    <span class="badge bg-warning small">{{ $daysRemaining }} days left</span>
                                                @else
                                                    <span class="badge bg-success small">{{ $daysRemaining }} days left</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Action Button -->
                                    <div class="px-3 pb-3">
                                        @if($checkout->status === 'checked_out')

                                            @if($checkout->canRequestExtension())
                                                <form action="{{ route('checkouts.request-extension', $checkout) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-warning w-100"
                                                            onclick="return confirm('Request 3-day extension for \"{{ $checkout->book->title }}\"?')">
                                                        <i class="fas fa-calendar-plus me-1"></i> Extend
                                                    </button>
                                                </form>

                                            @elseif($checkout->hasPendingExtension())
                                                <button class="btn btn-sm btn-outline-warning w-100" disabled>
                                                    <i class="fas fa-clock me-1"></i> Pending
                                                </button>

                                            @elseif($checkout->isExtended())
                                                <button class="btn btn-sm btn-outline-success w-100" disabled>
                                                    <i class="fas fa-calendar-check me-1"></i> Extended
                                                </button>

                                            @endif

                                        @elseif($checkout->status === 'approved')
                                            <span class="text-center text-muted small d-block">
                                                Ready for pickup
                                            </span>

                                        @elseif($checkout->status === 'pending')
                                            <form action="{{ route('checkouts.cancel', $checkout) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger w-100"
                                                        onclick="return confirm('Cancel request for \"{{ $checkout->book->title }}\"?')">
                                                    <i class="fas fa-times me-1"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Information Note -->
                <div class="card bg-light border-0 mt-4">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Important Information</h6>
                        <ul class="mb-0">
                            <li>You can check out up to <strong>5 books</strong> at a time</li>
                            <li>Books are due in <strong>14 days</strong> from checkout date</li>
                            <li>You can request <strong>one extension of 3 additional days</strong> per book</li>
                            <li>Extensions require <strong>librarian approval</strong></li>
                            <li>Return books at the <strong>library front desk</strong></li>
                            <li><strong>Reserved books:</strong> Must be picked up within 3 days after notification</li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="text-center py-5 px-3">
                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Books Checked Out</h4>
                    <p class="text-muted">You don't have any books checked out at the moment.</p>
                    <a href="{{ route('welcome') }}" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Browse Our Collection
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection