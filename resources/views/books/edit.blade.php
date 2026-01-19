@extends('layouts.librarian')

@section('content')
<div class="container mt-4">
    <!-- Header Section -->
    <div class="row mb-4 g-3">
        <div class="col-lg-6 col-12">
            <h1 class="h2 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-edit text-primary me-2"></i>Edit Book
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-pen-to-square me-1"></i>Update book information in your collection
            </p>
        </div>
        <div class="col-lg-6 col-12">
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-lg-end">
                <a href="{{ route('librarian.books.index') }}" class="btn btn-outline-secondary btn-lg shadow-sm px-4">
                    <i class="fas fa-arrow-left me-2"></i>Back to Books
                </a>
                <a href="{{ route('librarian.books.show', $book) }}" class="btn btn-info btn-lg shadow-sm px-4">
                    <i class="fas fa-eye me-2"></i>View Details
                </a>
            </div>
        </div>
    </div>

    <!-- Main Edit Form -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-gradient-warning text-dark py-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-book-medical me-2"></i>Edit: {{ $book->title }}
                </h5>
                <span class="badge bg-white text-warning px-3 py-2">
                    <i class="fas fa-database me-1"></i>Book ID: #{{ $book->id }}
                </span>
            </div>
        </div>
        
        <div class="card-body">
            <form action="{{ route('librarian.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Left Column - Book Cover & Status -->
                    <div class="col-lg-4 col-md-5">
                        <!-- Book Cover Upload -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">
                                    <i class="fas fa-image me-2 text-primary"></i>Book Cover
                                </h6>
                                
                                <!-- Cover Image Container -->
                                <div class="cover-container mb-3">
                                    <div id="coverImageWrapper" class="text-center">
                                        @if($book->image)
                                            <img src="{{ asset('storage/' . $book->image) }}" 
                                                 alt="Current cover" 
                                                 class="img-fluid rounded shadow" 
                                                 id="currentCover">
                                        @else
                                            <div class="bg-gradient-light d-flex align-items-center justify-content-center border rounded" 
                                                 id="placeholderCover">
                                                <i class="fas fa-book-open fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Cover Upload -->
                                <div class="mb-2">
                                    <label for="image" class="form-label fw-semibold">Upload New Cover</label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image"
                                           accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Recommended: 3:4 ratio, max 2MB</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Status Update -->
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">
                                    <i class="fas fa-chart-simple me-2 text-primary"></i>Availability Status
                                </h6>
                                
                                <div class="mb-0">
                                    <label for="availability_status" class="form-label fw-semibold">Status</label>
                                    <select class="form-select @error('availability_status') is-invalid @enderror" 
                                            id="availability_status" 
                                            name="availability_status">
                                        <option value="available" {{ $book->availability_status == 'available' ? 'selected' : '' }}>
                                            📗 Available
                                        </option>
                                        <option value="reserved" {{ $book->availability_status == 'reserved' ? 'selected' : '' }}>
                                            📙 Reserved
                                        </option>
                                        <option value="checked_out" {{ $book->availability_status == 'checked_out' ? 'selected' : '' }}>
                                            📕 Checked Out
                                        </option>
                                    </select>
                                    @error('availability_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Book Details -->
                    <div class="col-lg-8 col-md-7">
                        <!-- Basic Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>Basic Information
                                </h6>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="title" class="form-label fw-semibold">
                                            Book Title <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('title') is-invalid @enderror" 
                                               id="title" 
                                               name="title" 
                                               value="{{ old('title', $book->title) }}"
                                               placeholder="Enter book title"
                                               required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="author" class="form-label fw-semibold">
                                            Author <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('author') is-invalid @enderror" 
                                               id="author" 
                                               name="author" 
                                               value="{{ old('author', $book->author) }}"
                                               placeholder="Enter author name"
                                               required>
                                        @error('author')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="isbn" class="form-label fw-semibold">ISBN</label>
                                        <input type="text" 
                                               class="form-control @error('isbn') is-invalid @enderror" 
                                               id="isbn" 
                                               name="isbn" 
                                               value="{{ old('isbn', $book->isbn) }}"
                                               placeholder="e.g., 978-3-16-148410-0">
                                        @error('isbn')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Categories & Classification -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">
                                    <i class="fas fa-folder-tree me-2 text-primary"></i>Categories & Classification
                                </h6>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                       <label for="category" class="form-label fw-semibold">Category</label>
                                        <input type="text" 
                                            class="form-control @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            value="{{ old('category', $book->category ?? '') }}"
                                            list="categoryOptions"
                                            placeholder="Type or select category"
                                            required>
                                        <datalist id="categoryOptions">
                                            <option value="Fiction">
                                            <option value="Non-Fiction">
                                            <option value="Science">
                                            <option value="Technology">
                                            <option value="History">
                                            <option value="Biography">
                                            <option value="Children">
                                            <option value="Young Adult">
                                            <option value="Romance">
                                            <option value="Mystery">
                                            <option value="Fantasy">
                                            <option value="Horror">
                                            <option value="Self-Help">
                                            <option value="Business">
                                            <option value="Art">
                                            <option value="Travel">
                                        </datalist>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Start typing to see suggestions or enter a custom category</div>
                                    </div>
                                    <div class="col-md-6">
                                    <label for="genre" class="form-label fw-semibold">Genre</label>
                                    <input type="text" 
                                        class="form-control @error('genre') is-invalid @enderror" 
                                        id="genre" 
                                        name="genre" 
                                        value="{{ old('genre', $book->genre) }}"
                                        list="genreOptions"
                                        placeholder="Type or select genre">
                                    <datalist id="genreOptions">
                                        <option value="Mystery">
                                        <option value="Romance">
                                        <option value="Science Fiction">
                                        <option value="Fantasy">
                                        <option value="Thriller">
                                        <option value="Horror">
                                        <option value="Historical Fiction">
                                        <option value="Adventure">
                                        <option value="Crime">
                                        <option value="Drama">
                                        <option value="Comedy">
                                        <option value="Biography">
                                        <option value="Autobiography">
                                        <option value="Memoir">
                                        <option value="Self-Help">
                                        <option value="Business">
                                        <option value="Science">
                                        <option value="Technology">
                                        <option value="Travel">
                                        <option value="Cookbook">
                                        <option value="Health">
                                        <option value="Psychology">
                                        <option value="Philosophy">
                                        <option value="Religion">
                                        <option value="Art">
                                        <option value="Music">
                                        <option value="Sports">
                                        <option value="True Crime">
                                        <option value="Young Adult">
                                        <option value="Children's">
                                        <option value="Poetry">
                                        <option value="Graphic Novel">
                                        <option value="Manga">
                                        <option value="Classic">
                                        <option value="Contemporary">
                                        <option value="Dystopian">
                                        <option value="Paranormal">
                                        <option value="Western">
                                        <option value="Military">
                                    </datalist>
                                    @error('genre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Start typing to see suggestions or enter a custom genre</div>
                                </div>

                                    <div class="col-md-6">
                                        <label for="price" class="form-label fw-semibold">Price ($)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" 
                                                   class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" 
                                                   name="price" 
                                                   value="{{ old('price', $book->price) }}"
                                                   step="0.01"
                                                   min="0"
                                                   placeholder="0.00">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-4 col-12 order-md-1 order-2">
                                        <a href="{{ route('librarian.books.show', $book) }}" class="btn btn-outline-secondary w-100 px-4">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                    </div>
                                    <div class="col-md-8 col-12 order-md-2 order-1">
                                        <div class="d-flex gap-2">
                                            <button type="reset" class="btn btn-outline-warning w-100 px-4">
                                                <i class="fas fa-rotate me-2"></i>Reset
                                            </button>
                                            <button type="submit" class="btn btn-primary w-100 px-4">
                                                <i class="fas fa-save me-2"></i>Update Book
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    }
    
    .bg-gradient-light {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    .card {
        transition: all 0.3s ease;
        border-radius: 10px;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
    }
    
    /* Cover image container - keeps images centered */
    .cover-container {
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    #coverImageWrapper {
        width: 100%;
        max-width: 200px;
        margin: 0 auto;
    }
    
    #coverImageWrapper img {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }
    
    #placeholderCover {
        width: 200px;
        height: 250px;
        margin: 0 auto;
    }
</style>

<!-- JavaScript for Image Preview -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const coverImageWrapper = document.getElementById('coverImageWrapper');
        const currentCover = document.getElementById('currentCover');
        const placeholderCover = document.getElementById('placeholderCover');
        
        // Store original image source
        const originalImageSrc = currentCover ? currentCover.src : null;
        
        // Image preview functionality
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Replace the content with new preview
                    coverImageWrapper.innerHTML = `
                        <img src="${e.target.result}" 
                             alt="New cover preview" 
                             class="img-fluid rounded shadow"
                             style="width: 100%; height: 250px; object-fit: cover;">
                        <small class="text-success d-block mt-2">
                            <i class="fas fa-check-circle me-1"></i>New cover selected
                        </small>
                    `;
                }
                
                reader.readAsDataURL(file);
            }
        });
        
        // Reset form behavior - restores original state
        document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
            // Small delay to let form reset
            setTimeout(function() {
                // Restore original cover
                if (originalImageSrc) {
                    coverImageWrapper.innerHTML = `
                        <img src="${originalImageSrc}" 
                             alt="Current cover" 
                             class="img-fluid rounded shadow" 
                             id="currentCover"
                             style="width: 100%; height: 250px; object-fit: cover;">
                    `;
                } else if (placeholderCover) {
                    coverImageWrapper.innerHTML = `
                        <div class="bg-gradient-light d-flex align-items-center justify-content-center border rounded" 
                             id="placeholderCover"
                             style="width: 200px; height: 250px; margin: 0 auto;">
                            <i class="fas fa-book-open fa-3x text-muted"></i>
                        </div>
                    `;
                }
            }, 10);
        });
        
        // Auto-format ISBN input
        const isbnInput = document.getElementById('isbn');
        isbnInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\dX-]/gi, '');
            e.target.value = value;
        });
        
        // Price formatting
        const priceInput = document.getElementById('price');
        priceInput.addEventListener('blur', function(e) {
            if (e.target.value && !isNaN(e.target.value)) {
                e.target.value = parseFloat(e.target.value).toFixed(2);
            }
        });
    });
</script>
@endsection