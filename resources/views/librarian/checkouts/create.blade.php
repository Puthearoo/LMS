@extends('layouts.librarian')

@section('title', 'Create Checkout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <!-- Card Header -->
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <h3 class="card-title mb-2 mb-md-0 fw-bold">
                            <i class="fas fa-book-medical me-2"></i>
                            Create New Checkout
                        </h3>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('librarian.checkouts.store') }}" method="POST">
                        @csrf

                        <!-- Selection Row - Stack on mobile -->
                        <div class="row g-3">
                            <!-- Student Selection -->
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="user_id" class="form-label required fw-semibold">
                                        <i class="fas fa-user-graduate me-1"></i>Select Student
                                    </label>
                                    <select name="user_id" id="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" required>
                                        <option value="">-- Select Student --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted mt-1">
                                        <i class="fas fa-info-circle me-1"></i>Search by name or email
                                    </small>
                                </div>
                            </div>

                            <!-- Book Selection -->
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="book_id" class="form-label required fw-semibold">
                                        <i class="fas fa-book me-1"></i>Select Book
                                    </label>
                                    <select name="book_id" id="book_id" class="form-control select2 @error('book_id') is-invalid @enderror" required>
                                        <option value="">-- Select Book --</option>
                                        @foreach($books as $book)
                                            <option value="{{ $book->id }}" 
                                                    {{ old('book_id') == $book->id ? 'selected' : '' }}
                                                    data-available="{{ $book->availability_status === 'available' }}">
                                                {{ $book->title }} by {{ $book->author }}
                                                @if($book->availability_status === 'available')
                                                    (Available)
                                                @else
                                                    (Unavailable)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('book_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted mt-1">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Only available books can be checked out
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Information - Full width on all screens -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info border-0 shadow-sm">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <h6 class="mb-0 fw-bold">Checkout Information</h6>
                                    </div>
                                    <div class="row g-2 g-md-3">
                                        <div class="col-12 col-sm-6 col-md-3">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-calendar-check text-primary me-2 mt-1"></i>
                                                <div>
                                                    <small class="text-muted d-block">Checkout Date</small>
                                                    <strong>{{ now()->format('M j, Y') }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-3">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-clock text-warning me-2 mt-1"></i>
                                                <div>
                                                    <small class="text-muted d-block">Due Date</small>
                                                    <strong>{{ now()->addDays(14)->format('M j, Y') }}</strong>
                                                    <small class="d-block text-muted">(14 days)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-3">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-bookmark text-success me-2 mt-1"></i>
                                                <div>
                                                    <small class="text-muted d-block">Max Checkouts</small>
                                                    <strong>5 books per student</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-3">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-sync-alt text-info me-2 mt-1"></i>
                                                <div>
                                                    <small class="text-muted d-block">Extension</small>
                                                    <strong>3 days upon request</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Information Display - Stack on mobile -->
                        <div class="row g-3 mt-2">
                            <!-- Student Information -->
                            <div class="col-12 col-lg-6">
                                <div id="studentInfo" class="d-none">
                                    <div class="alert alert-success border-0 shadow-sm h-100">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-user-graduate fa-lg me-2"></i>
                                            <h6 class="mb-0 fw-bold">Student Information</h6>
                                        </div>
                                        <div id="studentDetails" class="small">
                                            <!-- Dynamic content will be inserted here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Book Information -->
                            <div class="col-12 col-lg-6">
                                <div id="bookInfo" class="d-none">
                                    <div class="alert alert-warning border-0 shadow-sm h-100">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-book fa-lg me-2"></i>
                                            <h6 class="mb-0 fw-bold">Book Information</h6>
                                        </div>
                                        <div id="bookDetails" class="small">
                                            <!-- Dynamic content will be inserted here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons - Responsive layout -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-md-start">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill flex-sm-grow-0 px-3">
                                        <i class="fas fa-check me-2"></i>Create Checkout
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary btn-sm flex-fill flex-sm-grow-0 px-3">
                                        <i class="fas fa-redo me-2"></i>Reset Form
                                    </button>
                                    <a href="{{ route('librarian.checkouts.index') }}" class="btn btn-outline-danger btn-sm flex-fill flex-sm-grow-0 px-3">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<style>
    .required:after {
        content: " *";
        color: #dc3545;
    }
    
    /* Select2 Responsive Styles */
    .select2-container--default .select2-selection--single {
        height: calc(2.5rem + 2px);
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.5rem;
    }
    
    /* Mobile First Responsive Design */
    @media (max-width: 575.98px) {
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .btn-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
        
        /* Stack alerts vertically on mobile */
        .alert .row .col-12 {
            margin-bottom: 0.5rem;
        }
        
        .alert .row .col-12:last-child {
            margin-bottom: 0;
        }
    }
    
    @media (max-width: 767.98px) {
        .card-header {
            text-align: center;
        }
        
        .card-header .d-flex {
            flex-direction: column;
            gap: 1rem;
        }
        
        .form-label {
            font-size: 0.9rem;
        }
        
        /* Improve Select2 on mobile */
        .select2-container {
            width: 100% !important;
        }
    }
    
    @media (min-width: 768px) and (max-width: 1199.98px) {
        /* Tablet optimizations */
        .card-body {
            padding: 1.5rem !important;
        }
        
        .btn {
            min-width: 140px;
        }
    }
    
    /* Enhanced form styles */
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-text {
        font-size: 0.8rem;
    }
    
    /* Card shadow and border */
    .card {
        border: none;
        border-radius: 12px;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
        border-bottom: none;
    }
    
    /* Alert enhancements */
    .alert {
        border-radius: 8px;
        border: none;
    }
    
    /* Button group responsive behavior */
    @media (max-width: 575.98px) {
        .d-flex.flex-column .btn {
            margin-bottom: 0.5rem;
        }
        
        .d-flex.flex-column .btn:last-child {
            margin-bottom: 0;
        }
    }
    
    /* Select2 dropdown responsive */
    .select2-container--open .select2-dropdown--below {
        min-width: 300px !important;
    }
    
    @media (max-width: 767.98px) {
        .select2-container--open .select2-dropdown--below {
            min-width: 100% !important;
            left: 0 !important;
        }
    }
    
    /* Loading states */
    .loading {
        opacity: 0.7;
        pointer-events: none;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 with responsive settings
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true,
        width: '100%',
        dropdownAutoWidth: true,
        dropdownParent: $('.card-body')
    });

    // Handle student selection with enhanced info
    $('#user_id').on('change', function() {
        const userId = $(this).val();
        const studentText = $(this).find('option:selected').text();
        
        if (userId) {
            $('#studentDetails').html(`
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-user-circle fa-lg text-primary me-2"></i>
                    <div>
                        <strong class="d-block">${studentText.split(' (')[0]}</strong>
                        <small class="text-muted">${studentText.match(/\((.*?)\)/)[1]}</small>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-check-circle text-success me-1"></i>
                        Checkout limit and history will be validated upon submission.
                    </small>
                </div>
            `);
            $('#studentInfo').removeClass('d-none').addClass('d-block');
        } else {
            $('#studentInfo').removeClass('d-block').addClass('d-none');
        }
    });

    // Handle book selection with availability check
    $('#book_id').on('change', function() {
        const bookOption = $(this).find('option:selected');
        const bookText = bookOption.text();
        const isAvailable = bookOption.data('available');
        
        if ($(this).val()) {
            const availabilityIcon = isAvailable ? 
                '<i class="fas fa-check-circle text-success me-1"></i>' : 
                '<i class="fas fa-times-circle text-danger me-1"></i>';
            const availabilityText = isAvailable ? 'Available for checkout' : 'Currently unavailable';
            
            $('#bookDetails').html(`
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-book-open fa-lg text-warning me-2"></i>
                    <div>
                        <strong class="d-block">${bookText.split(' by ')[0]}</strong>
                        <small class="text-muted">by ${bookText.split(' by ')[1]?.split(' (')[0]}</small>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="${isAvailable ? 'text-success' : 'text-danger'}">
                        ${availabilityIcon}${availabilityText}
                    </small>
                </div>
            `);
            $('#bookInfo').removeClass('d-none').addClass('d-block');
        } else {
            $('#bookInfo').removeClass('d-block').addClass('d-none');
        }
    });

    // Form submission handling
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).addClass('loading');
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
    });

    // Handle window resize for Select2
    $(window).on('resize', function() {
        $('.select2').each(function() {
            $(this).select2({
                width: '100%',
                dropdownAutoWidth: true,
                dropdownParent: $('.card-body')
            });
        });
    });
});
</script>
@endsection