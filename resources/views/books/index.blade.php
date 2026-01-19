@extends('layouts.librarian')

@section('content')
<div class="container mt-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-books text-primary me-2"></i>Books Management
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-layer-group me-1"></i>Organize and manage your library collection
            </p>
        </div>
        <a href="{{ route('librarian.books.create') }}" class="btn btn-primary btn-lg shadow-sm px-4">
            <i class="fas fa-circle-plus me-2"></i>Add Book
        </a>
    </div>

    <!-- Search and Filter Section - Optimized for iPad -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body py-3">
            <div class="row align-items-center g-3">
                <div class="col-xl-5 col-lg-6 col-md-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-magnifying-glass text-primary"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Search by title, author, or ISBN...">
                    </div>
                </div>
                <div class="col-xl-7 col-lg-6 col-md-12">
                    <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                        <select class="form-select ipad-select" style="min-width: 160px;">
                            <option value="">All Categories</option>
                            <option>Fiction</option>
                            <option>Non-Fiction</option>
                            <option>Science</option>
                            <option>Technology</option>
                            <option>History</option>
                        </select>
                        <select class="form-select ipad-select" style="min-width: 140px;">
                            <option value="">All Status</option>
                            <option>Available</option>
                            <option>Reserved</option>
                            <option>Checked Out</option>
                        </select>
                        <button class="btn btn-outline-secondary ipad-btn">
                            <i class="fas fa-filter-circle-xmark me-1"></i>Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-gradient-primary text-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-rectangle-list me-2"></i>Book Collection
                </h5>
                <div class="d-flex gap-3 align-items-center">
                    <span class="badge bg-white text-primary px-3 py-2">
                        <i class="fas fa-database me-1"></i>{{ $books->total() }} Total
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($books->count() > 0)
                <!-- Mobile Cards View - For screens below 992px -->
                <div class="d-block d-lg-none">
                    @foreach($books as $book)
                    <div class="card book-card-mobile m-3 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Book Cover and Basic Info -->
                                <div class="col-12">
                                    <div class="d-flex align-items-start gap-3">
                                        @if($book->image)
                                            <div class="book-cover-wrapper flex-shrink-0">
                                                <img src="{{ asset('storage/' . $book->image) }}" 
                                                     alt="{{ $book->title }}" 
                                                     class="book-cover img-thumbnail shadow-sm" 
                                                     style="width: 70px; height: 90px; object-fit: cover;">
                                            </div>
                                        @else
                                            <div class="book-cover-placeholder bg-gradient-light d-flex align-items-center justify-content-center border rounded shadow-sm flex-shrink-0" 
                                                 style="width: 70px; height: 90px;">
                                                <i class="fas fa-book-open fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold text-dark mb-1 ipad-title">{{ Str::limit($book->title, 35) }}</h6>
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-user-pen me-1 text-muted small"></i>
                                                <small class="text-muted ipad-author">{{ Str::limit($book->author, 22) }}</small>
                                            </div>
                                            @if($book->genre)
                                                <small class="text-muted ipad-genre">
                                                    <i class="fas fa-tag me-1"></i>{{ Str::limit($book->genre, 18) }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Book Details in Grid -->
                                <div class="col-12">
                                    <div class="row g-2 small">
                                        <div class="col-6">
                                            <div class="text-muted">ISBN</div>
                                            @if($book->isbn)
                                                <code class="bg-light px-1 py-0 rounded text-dark border small ipad-isbn">{{ Str::limit($book->isbn, 10) }}</code>
                                            @else
                                                <span class="text-muted fst-italic small">Not available</span>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted">Category</div>
                                            @if($book->category)
                                                <span class="badge rounded-pill bg-light text-dark border small ipad-category">
                                                    {{ Str::limit($book->category, 12) }}
                                                </span>
                                            @else
                                                <span class="text-muted fst-italic small">Uncategorized</span>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted">Status</div>
                                            @php
                                                $statusConfig = [
                                                    'available' => [
                                                        'color' => 'success', 
                                                        'icon' => 'fa-circle-check', 
                                                        'text' => 'Available',
                                                        'bg' => 'success'
                                                    ],
                                                    'checked_out' => [
                                                        'color' => 'danger', 
                                                        'icon' => 'fa-right-from-bracket', 
                                                        'text' => 'Checked Out',
                                                        'bg' => 'danger'
                                                    ],
                                                    'reserved' => [
                                                        'color' => 'warning', 
                                                        'icon' => 'fa-hourglass-half', 
                                                        'text' => 'Reserved',
                                                        'bg' => 'warning'
                                                    ]
                                                ];
                                                $status = $statusConfig[$book->availability_status] ?? [
                                                    'color' => 'secondary', 
                                                    'icon' => 'fa-circle-question', 
                                                    'text' => 'Unknown',
                                                    'bg' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge status-badge bg-{{ $status['bg'] }} bg-opacity-10 text-{{ $status['color'] }} border border-{{ $status['color'] }} px-2 py-1 small ipad-status">
                                                <i class="fas {{ $status['icon'] }} me-1"></i>{{ $status['text'] }}
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted">Price</div>
                                            @if($book->price)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-dollar-sign text-success me-1 small"></i>
                                                    <strong class="text-success ipad-price">{{ number_format($book->price, 2) }}</strong>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic small">Not priced</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-center pt-2 border-top">
                                        <a href="{{ route('librarian.books.show', $book) }}" 
                                           class="btn btn-sm btn-info text-white flex-fill ipad-action-btn" 
                                           title="View Details">
                                            <i class="fa-solid fa-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('librarian.books.edit', $book) }}" 
                                           class="btn btn-sm btn-warning text-dark flex-fill ipad-action-btn" 
                                           title="Edit Book">
                                            <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-danger text-white flex-fill ipad-action-btn" 
                                                title="Delete Book"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $book->id }}">
                                            <i class="fa-solid fa-trash-can me-1"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Desktop Table View - Optimized for iPad -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive ipad-table-container">
                        <table class="table table-hover align-middle mb-0 ipad-table">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center py-3" width="80">
                                        <i class="fas fa-image me-1 text-primary"></i>Cover
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-book-bookmark me-1 text-primary"></i>Book Info
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-feather-pointed me-1 text-primary"></i>Author
                                    </th>
                                    <th class="py-3 d-xl-table-cell d-lg-none">
                                        <i class="fas fa-barcode me-1 text-primary"></i>ISBN
                                    </th>
                                    <th class="py-3 d-xl-table-cell d-lg-none">
                                        <i class="fas fa-folder-tree me-1 text-primary"></i>Category
                                    </th>
                                    <th class="text-center py-3">
                                        <i class="fas fa-chart-simple me-1 text-primary"></i>Status
                                    </th>
                                    <th class="text-end py-3">
                                        <i class="fas fa-money-bill-wave me-1 text-primary"></i>Price
                                    </th>
                                    <th class="text-center py-3" width="140">
                                        <i class="fas fa-screwdriver-wrench me-1 text-primary"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($books as $book)
                                <tr class="book-row">
                                    <td class="text-center">
                                        @if($book->image)
                                            <div class="book-cover-wrapper">
                                                <img src="{{ asset('storage/' . $book->image) }}" 
                                                     alt="{{ $book->title }}" 
                                                     class="book-cover img-thumbnail shadow-sm ipad-book-cover" 
                                                     style="width: 50px; height: 65px; object-fit: cover;">
                                            </div>
                                        @else
                                            <div class="book-cover-placeholder bg-gradient-light d-flex align-items-center justify-content-center border rounded shadow-sm ipad-book-cover" 
                                                 style="width: 50px; height: 65px;">
                                                <i class="fas fa-book-open fa-lg text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold text-dark mb-1 ipad-table-title">{{ Str::limit($book->title, 25) }}</div>
                                            @if($book->genre)
                                                <small class="text-muted ipad-table-genre">
                                                    <i class="fas fa-tag me-1"></i>{{ Str::limit($book->genre, 15) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-pen me-2 text-muted ipad-table-icon"></i>
                                            <span class="ipad-table-author">{{ Str::limit($book->author, 18) }}</span>
                                        </div>
                                    </td>
                                    <td class="d-xl-table-cell d-lg-none">
                                        @if($book->isbn)
                                            <code class="bg-light px-1 py-0 rounded text-dark border small ipad-table-isbn">{{ Str::limit($book->isbn, 10) }}</code>
                                        @else
                                            <span class="text-muted fst-italic small">N/A</span>
                                        @endif
                                    </td>
                                    <td class="d-xl-table-cell d-lg-none">
                                        @if($book->category)
                                            <span class="badge rounded-pill bg-light text-dark border border-1 px-2 py-1 small ipad-table-category">
                                                {{ Str::limit($book->category, 12) }}
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic small">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusConfig = [
                                                'available' => [
                                                    'color' => 'success', 
                                                    'icon' => 'fa-circle-check', 
                                                    'text' => 'Available',
                                                    'bg' => 'success'
                                                ],
                                                'checked_out' => [
                                                    'color' => 'danger', 
                                                    'icon' => 'fa-right-from-bracket', 
                                                    'text' => 'Checked Out',
                                                    'bg' => 'danger'
                                                ],
                                                'reserved' => [
                                                    'color' => 'warning', 
                                                    'icon' => 'fa-hourglass-half', 
                                                    'text' => 'Reserved',
                                                    'bg' => 'warning'
                                                ]
                                            ];
                                            $status = $statusConfig[$book->availability_status] ?? [
                                                'color' => 'secondary', 
                                                    'icon' => 'fa-circle-question', 
                                                    'text' => 'Unknown',
                                                    'bg' => 'secondary'
                                                ];
                                        @endphp
                                        <span class="badge status-badge bg-{{ $status['bg'] }} bg-opacity-10 text-{{ $status['color'] }} border border-{{ $status['color'] }} px-2 py-1 small ipad-table-status">
                                            <i class="fas {{ $status['icon'] }} me-1"></i>{{ $status['text'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($book->price)
                                            <div class="d-flex align-items-center justify-content-end">
                                                <i class="fas fa-dollar-sign text-success me-1 ipad-table-icon"></i>
                                                <strong class="text-success ipad-table-price">{{ number_format($book->price, 2) }}</strong>
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic small">Not priced</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center action-buttons">
                                            <a href="{{ route('librarian.books.show', $book) }}" 
                                               class="btn btn-sm btn-info text-white ipad-table-btn" 
                                               title="View Details"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('librarian.books.edit', $book) }}" 
                                               class="btn btn-sm btn-warning text-dark ipad-table-btn" 
                                               title="Edit Book"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger text-white ipad-table-btn" 
                                                    title="Delete Book"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $book->id }}"
                                                    data-bs-placement="top">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($books->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div class="text-muted ipad-pagination-info">
                            <i class="fas fa-circle-info me-1 text-primary"></i>
                            Showing <strong class="text-primary">{{ $books->firstItem() }}</strong> to 
                            <strong class="text-primary">{{ $books->lastItem() }}</strong> of 
                            <strong class="text-primary">{{ $books->total() }}</strong> books
                        </div>
                        <div class="ipad-pagination">
                            {{ $books->links() }}
                        </div>
                    </div>
                </div>
                @endif

            @else
                <!-- Enhanced Empty State -->
                <div class="text-center py-5 my-5">
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-book-open-reader fa-5x text-primary opacity-25"></i>
                    </div>
                    <h3 class="text-dark mb-3 fw-bold">No Books in Your Collection</h3>
                    <p class="text-muted mb-4 px-3">
                        Your library is waiting to be filled! Start building an amazing collection<br>
                        by adding your first book today.
                    </p>
                    <a href="{{ route('librarian.books.create') }}" class="btn btn-primary btn-lg shadow-sm px-5 py-3">
                        <i class="fas fa-circle-plus me-2"></i>Add Your First Book
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modals -->
@foreach($books as $book)
<div class="modal fade" id="deleteModal{{ $book->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $book->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <!-- Modal Header -->
            <div class="modal-header bg-danger text-white border-0 px-4 py-3">
                <h5 class="modal-title d-flex align-items-center" id="deleteModalLabel{{ $book->id }}">
                    <i class="fas fa-triangle-exclamation me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body text-center py-5 px-4">
                <!-- Trash Icon -->
                <div class="mb-4">
                    <i class="fas fa-trash-can fa-4x text-danger"></i>
                </div>
                
                <!-- Confirmation Message -->
                <h4 class="fw-bold mb-3">Are you sure?</h4>
                
                <p class="text-muted mb-2">You are about to delete:</p>
                <p class="fw-bold text-dark mb-2">"{{ $book->title }}"</p>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">
                    <i class="fas fa-xmark me-2"></i>Cancel
                </button>

                <form action="{{ route('librarian.books.destroy', $book->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 py-2">
                        <i class="fas fa-trash-can me-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- iPad Mini (768x1024) Responsive Styles -->
<style>
    /* Base responsive adjustments */
    .container {
        max-width: 100%;
        padding-left: 15px;
        padding-right: 15px;
    }
    
    /* Gradient Background */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .bg-gradient-light {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    /* iPad Specific Breakpoints */
    @media (min-width: 768px) and (max-width: 1199.98px) {
        /* Header adjustments */
        .h2 {
            font-size: 1.4rem;
        }
        
        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        /* Search and filter section */
        .ipad-select {
            min-width: 140px !important;
            font-size: 0.875rem;
        }
        
        .ipad-btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
        
        /* Table optimizations for iPad */
        .ipad-table-container {
            font-size: 0.875rem;
        }
        
        .ipad-table th {
            font-size: 0.75rem;
            padding: 0.75rem 0.5rem;
        }
        
        .ipad-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .ipad-book-cover {
            width: 45px !important;
            height: 60px !important;
        }
        
        .ipad-table-title {
            font-size: 0.875rem;
            max-width: 180px;
        }
        
        .ipad-table-author {
            font-size: 0.8rem;
            max-width: 120px;
        }
        
        .ipad-table-genre {
            font-size: 0.75rem;
        }
        
        .ipad-table-isbn {
            font-size: 0.7rem;
        }
        
        .ipad-table-category {
            font-size: 0.75rem;
        }
        
        .ipad-table-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem !important;
        }
        
        .ipad-table-price {
            font-size: 0.875rem;
        }
        
        .ipad-table-icon {
            font-size: 0.8rem;
        }
        
        .ipad-table-btn {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.8rem;
        }
        
        /* Mobile card optimizations for iPad */
        .book-card-mobile {
            margin: 0.5rem !important;
        }
        
        .ipad-title {
            font-size: 0.9rem;
            max-width: 250px;
        }
        
        .ipad-author {
            font-size: 0.8rem;
            max-width: 180px;
        }
        
        .ipad-genre {
            font-size: 0.75rem;
        }
        
        .ipad-isbn {
            font-size: 0.7rem;
        }
        
        .ipad-category {
            font-size: 0.75rem;
        }
        
        .ipad-status {
            font-size: 0.75rem;
        }
        
        .ipad-price {
            font-size: 0.8rem;
        }
        
        .ipad-action-btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Pagination adjustments */
        .ipad-pagination-info {
            font-size: 0.875rem;
        }
        
        .ipad-pagination .pagination {
            font-size: 0.875rem;
        }
        
        .ipad-pagination .page-link {
            padding: 0.375rem 0.75rem;
        }
    }
    
    /* Specific optimization for iPad Mini portrait */
    @media (min-width: 768px) and (max-width: 1024px) and (orientation: portrait) {
        .d-xl-table-cell.d-lg-none {
            display: none !important;
        }
        
        .ipad-table-title {
            max-width: 150px;
        }
        
        .ipad-table-author {
            max-width: 100px;
        }
        
        .container {
            padding-left: 10px;
            padding-right: 10px;
        }
    }
    
    /* Specific optimization for iPad Mini landscape */
    @media (min-width: 1024px) and (max-width: 1199.98px) {
        .ipad-table-title {
            max-width: 200px;
        }
        
        .ipad-table-author {
            max-width: 150px;
        }
    }
    
    /* Table Styles */
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        background-color: #f8f9fa;
    }
    
    .table tbody tr.book-row {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 3px solid transparent;
    }
    
    .table tbody tr.book-row:hover {
        background-color: rgba(102, 126, 234, 0.06);
        border-left: 3px solid #667eea;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateX(4px);
    }
    
    /* Mobile Card Styles */
    .book-card-mobile {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .book-card-mobile:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    /* Book Cover Styles */
    .book-cover {
        transition: all 0.3s ease;
        border-radius: 4px;
    }
    
    .book-cover-wrapper:hover .book-cover {
        transform: scale(1.15) rotate(2deg);
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        z-index: 10;
    }
    
    .book-cover-placeholder {
        transition: all 0.3s ease;
    }
    
    .book-cover-placeholder:hover {
        transform: scale(1.1);
        background: linear-gradient(135deg, #e0e7ff 0%, #cfd8ff 100%);
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 4px;
    }
    
    .action-buttons .btn {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 14px;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    }
    
    /* Status Badge Animation */
    .status-badge {
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.2s ease;
        border-width: 1.5px !important;
    }
    
    .status-badge:hover {
        transform: scale(1.05);
    }
    
    /* Card Hover Effects */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    /* Empty State Animation */
    .empty-state-icon {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }
    
    /* Modal Enhancements */
    .modal-content {
        border-radius: 15px;
        overflow: hidden;
    }
    
    /* Search input focus */
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    /* Smooth transitions for mobile cards */
    .book-card-mobile {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<!-- Enhanced JavaScript with iPad optimizations -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // iPad specific optimizations
        function optimizeForIpad() {
            const isIpad = window.innerWidth >= 768 && window.innerWidth <= 1199;
            
            if (isIpad) {
                // Add specific iPad classes
                document.body.classList.add('ipad-view');
                
                // Optimize table responsiveness
                const tableCells = document.querySelectorAll('.ipad-table td, .ipad-table th');
                tableCells.forEach(cell => {
                    cell.style.padding = '0.5rem';
                });
            } else {
                document.body.classList.remove('ipad-view');
            }
        }
        
        // Initial optimization
        optimizeForIpad();
        
        // Re-optimize on resize
        window.addEventListener('resize', optimizeForIpad);
        
        // Add animation to cards on scroll
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
        
        // Observe desktop cards and mobile cards
        document.querySelectorAll('.card, .book-card-mobile').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    });
</script>
@endsection