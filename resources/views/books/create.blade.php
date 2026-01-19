@extends('layouts.librarian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-plus-circle me-2"></i>Add New Book
    </h1>
</div>

<div class="card shadow">
    <div class="card-body">
        <form action="{{ route('librarian.books.store') }}" method="POST" enctype="multipart/form-data">            
            @csrf
            <div class="row">
                {{-- Book Title --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label">Book Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Author --}}
                    <div class="mb-3">
                        <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('author') is-invalid @enderror" 
                               id="author" name="author" value="{{ old('author') }}" required>
                        @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- ISBN --}}
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                               id="isbn" name="isbn" value="{{ old('isbn') }}" required>
                        @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{-- Category --}}
                <div class="col-md-6">
                <div class="mb-3">
                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                    <input type="text" 
                        class="form-control @error('category') is-invalid @enderror" 
                        id="category" 
                        name="category" 
                        value="{{ old('category') }}" required
                        list="categoryOptions"
                        placeholder="Type or select category">
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
                </div>
                    {{-- Genre --}}
                    <div class="mb-3">
                        <label for="genre" class="form-label">Genre <span class="text-danger">*</span></label>
                        <input type="text" 
                            class="form-control @error('genre') is-invalid @enderror" 
                            id="genre" 
                            name="genre" 
                            value="{{ old('genre') }}" required
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
                    </div>
                    {{-- Price --}}
                    <div class="mb-3">
                        <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price') }}" min="0" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            {{-- Availability Status --}}
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="availability_status" class="form-label">Availability Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('availability_status') is-invalid @enderror" 
                                id="availability_status" name="availability_status" required>
                            <option value="">Select Status</option>
                            <option value="available" {{ old('availability_status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="reserved" {{ old('availability_status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            <option value="checked_out" {{ old('availability_status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                        </select>
                        @error('availability_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
                {{-- Image --}}
                <div class="mb-3">
                    <label for="image" class="form-label">Book Cover Image <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                        id="image" name="image" accept="image/*" required>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Supported formats: JPEG, PNG, JPG, GIF, WEBP. Max file size: 2MB.
                    </div>
                </div>

            <!-- Image Preview -->
            <div class="mb-3 d-none" id="imagePreviewContainer">
                <label class="form-label">Image Preview</label>
                <div class="border rounded p-3 text-center">
                    <img id="imagePreview" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 200px; display: none;">
                    <p class="text-muted mb-0" id="imagePreviewText">Image preview will appear here</p>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-secondary me-md-2">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Add Book
                </button>
            </div>
        </form>
    </div>
</div>
@endsection