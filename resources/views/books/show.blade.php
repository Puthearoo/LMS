@extends('layouts.librarian')

@section('content')
<div class="container mt-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-book-open text-primary me-2"></i>Book Details
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-eye me-1"></i>View complete book information
            </p>
        </div>
       <div class="d-flex flex-wrap gap-2 justify-content-end">
    <a href="{{ route('librarian.books.index') }}" class="btn btn-outline-secondary btn-lg shadow-sm px-4">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
    <a href="{{ route('librarian.books.edit', $book) }}" class="btn btn-warning btn-lg shadow-sm px-4">
        <i class="fas fa-edit me-2"></i>Edit Book
    </a>
</div>
</div>
    </div>

    <!-- Main Content Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-gradient-primary text-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-circle-info me-2"></i>Book Information
                </h5>
                <div class="d-flex gap-3 align-items-center">
                    @php
                         $statusConfig = [
                            'available' => ['color' => 'success', 'icon' => 'fa-circle-check', 'text' => 'Available'],
                            'checked_out' => ['color' => 'danger', 'icon' => 'fa-right-from-bracket', 'text' => 'Checked Out'],
                            'reserved' => ['color' => 'warning', 'icon' => 'fa-hourglass-half', 'text' => 'Reserved']
                        ];
                        $status = $statusConfig[$book->availability_status] ?? ['color' => 'secondary', 'icon' => 'fa-circle-question', 'text' => 'Unknown'];
                    @endphp
                    <span class="badge bg-white text-{{ $status['color'] }} px-3 py-2 fw-semibold">
                        <i class="fas {{ $status['icon'] }} me-1"></i>{{ $status['text'] }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <!-- Book Cover Column -->
                <div class="col-md-3 text-center mb-4">
                    @if($book->image)
                        <div class="book-cover-wrapper mb-3">
                            <img src="{{ asset('storage/' . $book->image) }}" 
                                 alt="{{ $book->title }}" 
                                 class="book-cover img-fluid rounded shadow" 
                                 style="max-width: 250px; height: 320px; object-fit: cover;">
                        </div>
                    @else
                        <div class="book-cover-placeholder bg-gradient-light d-flex align-items-center justify-content-center border rounded shadow mx-auto" 
                             style="width: 250px; height: 320px;">
                            <i class="fas fa-book-open fa-5x text-muted"></i>
                        </div>
                    @endif
                    
                    <!-- Quick Actions -->
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('librarian.books.edit', $book) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Book
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-can me-2"></i>Delete Book
                        </button>
                    </div>
                </div>

                <!-- Book Details Column -->
                <div class="col-md-9">
                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h2 class="text-primary mb-2">{{ $book->title }}</h2>
                            @if($book->subtitle)
                                <h5 class="text-muted mb-3">{{ $book->subtitle }}</h5>
                            @endif
                            <div class="d-flex flex-wrap gap-3 mb-4">
                                @if($book->genre)
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                        <i class="fas fa-tag me-1"></i>{{ $book->genre }}
                                    </span>
                                @endif
                                @if($book->category)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                        <i class="fas fa-bookmark me-1"></i>{{ $book->category }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Author & ISBN -->
                        <div class="col-md-6">
                            <div class="detail-card card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">
                                        <i class="fas fa-user-pen me-2 text-primary"></i>Author Information
                                    </h6>
                                    <div class="mb-3">
                                        <strong class="text-dark">Author:</strong>
                                        <p class="mb-0 fs-5">{{ $book->author ?? 'Not specified' }}</p>
                                    </div>
                                    @if($book->publisher)
                                    <div class="mb-3">
                                        <strong class="text-dark">Publisher:</strong>
                                        <p class="mb-0">{{ $book->publisher }}</p>
                                    </div>
                                    @endif
                                    @if($book->published_year)
                                    <div>
                                        <strong class="text-dark">Published Year:</strong>
                                        <p class="mb-0">{{ $book->published_year }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Identification -->
                        <div class="col-md-6">
                            <div class="detail-card card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">
                                        <i class="fas fa-fingerprint me-2 text-primary"></i>Identification
                                    </h6>
                                    @if($book->isbn)
                                    <div class="mb-3">
                                        <strong class="text-dark">ISBN:</strong>
                                        <p class="mb-0">
                                            <code class="bg-white px-2 py-1 rounded border">{{ $book->isbn }}</code>
                                        </p>
                                    </div>
                                    @endif
                                    @if($book->id)
                                    <div class="mb-3">
                                        <strong class="text-dark">Book ID:</strong>
                                        <p class="mb-0">
                                            <code class="bg-white px-2 py-1 rounded border">#{{ $book->id }}</code>
                                        </p>
                                    </div>
                                    @endif
                                    <div>
                                        <strong class="text-dark">Added On:</strong>
                                        <p class="mb-0">{{ $book->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Status -->
                        <div class="col-md-6">
                            <div class="detail-card card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">
                                        <i class="fas fa-money-bill-wave me-2 text-primary"></i>Pricing & Status
                                    </h6>
                                    <div class="mb-3">
                                        <strong class="text-dark">Price:</strong>
                                        <p class="mb-0 fs-5 text-success">
                                            <i class="fas fa-dollar-sign me-1"></i>
                                            {{ $book->price ? number_format($book->price, 2) : 'Not priced' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <strong class="text-dark">Availability:</strong>
                                        <p class="mb-0">
                                            <span class="badge bg-{{ $status['color'] }} bg-opacity-10 text-{{ $status['color'] }} border border-{{ $status['color'] }} px-3 py-2">
                                                <i class="fas {{ $status['icon'] }} me-1"></i>{{ $status['text'] }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-md-6">
                            <div class="detail-card card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">
                                        <i class="fas fa-circle-info me-2 text-primary"></i>Additional Details
                                    </h6>
                                    @if($book->edition)
                                    <div class="mb-3">
                                        <strong class="text-dark">Edition:</strong>
                                        <p class="mb-0">{{ $book->edition }}</p>
                                    </div>
                                    @endif
                                    @if($book->language)
                                    <div class="mb-3">
                                        <strong class="text-dark">Language:</strong>
                                        <p class="mb-0">{{ $book->language }}</p>
                                    </div>
                                    @endif
                                    @if($book->page_count)
                                    <div class="mb-3">
                                        <strong class="text-dark">Pages:</strong>
                                        <p class="mb-0">{{ $book->page_count }}</p>
                                    </div>
                                    @endif
                                    @if($book->location)
                                    <div>
                                        <strong class="text-dark">Location:</strong>
                                        <p class="mb-0">{{ $book->location }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($book->description)
                        <div class="col-12">
                            <div class="detail-card card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">
                                        <i class="fas fa-align-left me-2 text-primary"></i>Description
                                    </h6>
                                    <p class="mb-0" style="line-height: 1.6;">{{ $book->description }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Timestamps -->
                        <div class="col-12">
                            <div class="card border-0 bg-transparent">
                                <div class="card-body p-0">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <small class="text-muted">Created</small>
                                            <p class="mb-0 fw-semibold">{{ $book->created_at->format('M d, Y \a\t h:i A') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Last Updated</small>
                                            <p class="mb-0 fw-semibold">{{ $book->updated_at->format('M d, Y \a\t h:i A') }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Days in Collection</small>
                                            <p class="mb-0 fw-semibold text-primary">{{ ceil($book->created_at->diffInDays()) }} days</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title">
                    <i class="fas fa-triangle-exclamation me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-can fa-3x text-danger mb-3"></i>
                <h5 class="mb-3">Delete "{{ $book->title }}"?</h5>
                <p class="text-muted mb-2">This action will permanently remove the book from your collection.</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-xmark me-1"></i>Cancel
                </button>
                <form action="{{ route('librarian.books.destroy', $book) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-trash-can me-1"></i>Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .bg-gradient-light {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    .detail-card {
        transition: all 0.3s ease;
        border-radius: 10px;
    }
    
    .detail-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .book-cover {
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    
    .book-cover-wrapper:hover .book-cover {
        transform: scale(1.05);
        box-shadow: 0 12px 30px rgba(0,0,0,0.2);
    }
    
    .book-cover-placeholder:hover {
        transform: scale(1.02);
        background: linear-gradient(135deg, #e0e7ff 0%, #cfd8ff 100%);
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.detail-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    });
</script>
@endsection