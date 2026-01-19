@extends('layouts.student')

@section('title', 'Checkout Request Submitted - Digital Library')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="text-warning mb-4">
                        <i class="fas fa-clock fa-5x"></i>
                    </div>
                    <h2 class="card-title mb-4">Checkout Request Submitted!</h2>
                    
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Request Details</h5>
                        <hr>
                        <p class="mb-1"><strong>Title:</strong> {{ $checkout->book->title }}</p>
                        <p class="mb-1"><strong>Author:</strong> {{ $checkout->book->author }}</p>
                        <p class="mb-1"><strong>Request Date:</strong> {{ \Carbon\Carbon::parse($checkout->created_at)->format('M j, Y') }}</p>
                        <p class="mb-0"><strong>Status:</strong> 
                            <span class="badge bg-warning">Pending Approval</span>
                        </p>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Your request is waiting for librarian approval. You will be notified once it's approved.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('welcome') }}" class="btn btn-primary me-md-2">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                        <a href="{{ route('my.checkouts') }}" class="btn btn-outline-primary">
                            <i class="fas fa-history me-2"></i>View My Checkouts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 15px;
        border: none;
    }
    
    .text-warning {
        color: #ffc107 !important;
    }
    
    .alert {
        border-radius: 10px;
        border: none;
    }
</style>
@endpush