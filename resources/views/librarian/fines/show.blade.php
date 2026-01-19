@extends('layouts.librarian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Fine Details #{{ $fine->id }}</h5>
                        <small class="text-muted">Issued on {{ $fine->fine_date->format('F d, Y \a\t h:i A') }}</small>
                    </div>
                    <div>
                        @if($fine->status == 'unpaid')
                            <span class="badge bg-danger">
                                <i class="bi bi-clock me-1"></i> Unpaid
                            </span>
                        @elseif($fine->status == 'paid')
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i> Paid
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-x-circle me-1"></i> Waived
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">User Information</label>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-person-circle text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ $fine->user->name }}</h6>
                                        <p class="text-muted mb-0">{{ $fine->user->email }}</p>
                                        <small class="text-muted">ID: {{ $fine->user_id }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Fine Amount</label>
                                <h2 class="{{ $fine->isUnpaid() ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($fine->amount, 2) }}
                                </h2>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Reason</dt>
                                <dd class="col-sm-8">{{ $fine->reason }}</dd>
                                
                                <dt class="col-sm-4">Status</dt>
                                <dd class="col-sm-8">
                                    @if($fine->status == 'unpaid')
                                        <span class="badge bg-danger">Unpaid</span>
                                    @elseif($fine->status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-secondary">Waived</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Issued Date</dt>
                                <dd class="col-sm-8">
                                    {{ $fine->fine_date->format('F d, Y') }}<br>
                                    <small class="text-muted">{{ $fine->fine_date->format('h:i A') }}</small>
                                </dd>
                            </dl>
                        </div>
                        
                        <div class="col-md-6">
                            <dl class="row">
                                @if($fine->checkout_id)
                                    <dt class="col-sm-4">Checkout ID</dt>
                                    <dd class="col-sm-8">#{{ $fine->checkout_id }}</dd>
                                @endif
                                
                                <dt class="col-sm-4">Paid Date</dt>
                                <dd class="col-sm-8">
                                    @if($fine->paid_date)
                                        <span class="text-success">
                                            {{ $fine->paid_date->format('F d, Y') }}<br>
                                            <small>{{ $fine->paid_date->format('h:i A') }}</small>
                                        </span>
                                    @else
                                        <span class="text-muted">Not paid yet</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Created</dt>
                                <dd class="col-sm-8">
                                    {{ $fine->created_at->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ $fine->created_at->format('h:i A') }}</small>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    
                    @if($fine->checkout && $fine->checkout->book)
                        <div class="card bg-light mt-3">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="bi bi-book me-2"></i>Related Book Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Book Title:</strong> {{ $fine->checkout->book->title }}</p>
                                        <p class="mb-1"><strong>Author:</strong> {{ $fine->checkout->book->author }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Due Date:</strong> {{ $fine->checkout->due_date->format('M d, Y') }}</p>
                                        @if($fine->checkout->return_date)
                                            <p class="mb-0"><strong>Returned:</strong> {{ $fine->checkout->return_date->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('librarian.fines.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                        
                        <div class="btn-group">
                            @if($fine->isUnpaid())
                                <form action="{{ route('librarian.fines.mark-paid', $fine) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-cash me-1"></i> Mark as Paid
                                    </button>
                                </form>
                                <form action="{{ route('librarian.fines.waive', $fine) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-x-lg me-1"></i> Waive Fine
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection