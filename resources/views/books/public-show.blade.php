@extends('layouts.app') {{-- or your layout --}}

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            @if($book->image)
                <img src="{{ asset('storage/' . $book->image) }}" 
                     alt="{{ $book->title }}" 
                     class="img-fluid rounded shadow">
            @else
                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                     style="height: 300px;">
                    <i class="fas fa-book fa-5x text-muted"></i>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <h1>{{ $book->title }}</h1>
            <p class="text-muted">by {{ $book->author }}</p>
            
            <div class="mb-3">
                <span class="badge bg-{{ $book->availability_status == 'available' ? 'success' : 'danger' }}">
                    {{ ucfirst($book->availability_status) }}
                </span>
                <span class="badge bg-primary ms-2">${{ number_format($book->price, 2) }}</span>
            </div>

            <div class="row mb-3">
                <div class="col-sm-6">
                    <strong>Category:</strong> {{ $book->category }}
                </div>
                <div class="col-sm-6">
                    <strong>Genre:</strong> {{ $book->genre }}
                </div>
            </div>

            @if($book->isbn)
            <div class="mb-3">
                <strong>ISBN:</strong> {{ $book->isbn }}
            </div>
            @endif

            @if($book->description)
            <div class="mb-3">
                <strong>Description:</strong>
                <p class="mt-2">{{ $book->description }}</p>
            </div>
            @endif

            <div class="mt-4">
                @auth
                    @if($book->availability_status == 'available')
                        <form action="{{ route('books.checkout', $book) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Checkout Book
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary btn-lg" disabled>
                            @if($book->availability_status == 'checked_out')
                                <i class="fas fa-shopping-cart me-2"></i>Currently Checked Out
                            @elseif($book->availability_status == 'reserved')
                                <i class="fas fa-clock me-2"></i>Reserved
                            @endif
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-success btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                    </a>
                @endauth
                
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-lg ms-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Books
                </a>
            </div>
        </div>
    </div>
</div>
@endsection