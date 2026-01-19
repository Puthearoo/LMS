@extends('layouts.librarian')

@section('title', 'Add New User')

@section('content')
<div class="container-fluid px-2 px-md-3 px-lg-4 py-2 py-md-3">

    <!-- Header with Breadcrumb -->
    <div class="mb-3 mb-md-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-1 mb-md-2">
                <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('librarian.users.index') }}">Borrowers</a></li>
                <li class="breadcrumb-item active">Add New User</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <div class="mb-2 mb-md-0">
                <h1 class="h3 h2-md mb-1 font-weight-bold">Add New User</h1>
                <p class="text-muted mb-0">Create a new user account in the library system</p>
            </div>
            <div>
                <a href="{{ route('librarian.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        User Information
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form method="POST" action="{{ route('librarian.users.store') }}" id="userForm">
                        @csrf

                        <!-- Personal Information Section -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="font-weight-bold mb-3 text-primary">
                                <i class="fas fa-id-card me-2"></i>Personal Information
                            </h6>
                            
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label font-weight-bold">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Enter full name"
                                           required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label font-weight-bold">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="user@example.com"
                                           required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text small text-muted mt-1">
                                    This will be used for login and notifications
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div class="mb-3">
                                <label for="contact" class="form-label font-weight-bold">
                                    Contact Number
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                    <input type="tel" 
                                           class="form-control @error('contact') is-invalid @enderror" 
                                           id="contact" 
                                           name="contact" 
                                           value="{{ old('contact') }}" 
                                           placeholder="+1 (555) 123-4567">
                                </div>
                                @error('contact')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text small text-muted mt-1">
                                    Optional - for emergency contact
                                </div>
                            </div>
                        </div>

                        <!-- Account Settings Section -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="font-weight-bold mb-3 text-primary">
                                <i class="fas fa-lock me-2"></i>Account Settings
                            </h6>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label font-weight-bold">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-key text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Create a password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text small text-muted mt-1">
                                    Minimum 8 characters with letters and numbers
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label font-weight-bold">
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-key text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Confirm password"
                                           required>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Account Type & Status -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="font-weight-bold mb-3 text-primary">
                                <i class="fas fa-user-tag me-2"></i>Account Type & Status
                            </h6>

                            <!-- Role Selection -->
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">User Role <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="role" 
                                               id="role_student" 
                                               value="student" 
                                               {{ old('role', 'student') == 'student' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_student">
                                            <span class="badge bg-primary">
                                                <i class="fas fa-user-graduate me-1"></i> Student
                                            </span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="role" 
                                               id="role_librarian" 
                                               value="librarian"
                                               {{ old('role') == 'librarian' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_librarian">
                                            <span class="badge bg-info">
                                                <i class="fas fa-book-reader me-1"></i> Librarian
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                @error('role')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text small text-muted mt-2">
                                    Students can borrow books, librarians manage the system
                                </div>
                            </div>

                            <!-- Account Status -->
                            <div class="mb-0">
                                <label class="form-label font-weight-bold">Account Status <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="status" 
                                               id="status_active" 
                                               value="active" 
                                               {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_active">
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="status" 
                                               id="status_inactive" 
                                               value="inactive"
                                               {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_inactive">
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times-circle me-1"></i> Inactive
                                            </span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="status" 
                                               id="status_suspended" 
                                               value="suspended"
                                               {{ old('status') == 'suspended' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_suspended">
                                            <span class="badge bg-warning">
                                                <i class="fas fa-ban me-1"></i> Suspended
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text small text-muted mt-2">
                                    Active accounts can borrow books, inactive/suspended cannot
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex flex-column flex-sm-row gap-2 gap-md-3 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                <i class="fas fa-user-plus me-2"></i> Create User
                            </button>
                            <a href="{{ route('librarian.users.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Help Section -->
        <div class="col-12 col-lg-4 col-xl-6 mt-4 mt-lg-0">
            <!-- Quick Tips -->
            <div class="card border-0 shadow-sm mb-3 mb-md-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-lightbulb text-warning me-2"></i>Quick Tips
                    </h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                <span class="small">Use a valid email for password recovery</span>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                <span class="small">Email must be unique in the system</span>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                <span class="small">Student role is default for borrowers</span>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                <span class="small">Only active accounts can borrow books</span>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                                <span class="small">Password will be sent to user's email</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Recent Users -->
            @if($recentUsers->isNotEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-history text-info me-2"></i>Recently Added Users
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($recentUsers as $recentUser)
                                <div class="list-group-item border-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            @if($recentUser->role == 'student')
                                                <div class="avatar-sm rounded-circle bg-primary-soft d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user-graduate text-primary"></i>
                                                </div>
                                            @else
                                                <div class="avatar-sm rounded-circle bg-info-soft d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-book-reader text-info"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $recentUser->name }}</h6>
                                            <div class="small text-muted">
                                                <div class="mb-1">
                                                    <i class="fas fa-envelope fa-xs me-1"></i> {{ $recentUser->email }}
                                                </div>
                                                <div>
                                                    <i class="fas fa-calendar fa-xs me-1"></i> 
                                                    {{ $recentUser->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-{{ $recentUser->status == 'active' ? 'success' : ($recentUser->status == 'inactive' ? 'secondary' : 'warning') }}">
                                                {{ ucfirst($recentUser->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

<style>
    /* Responsive Utilities */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .h3 {
            font-size: 1.25rem;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 576px) {
        .breadcrumb {
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        
        .form-label {
            font-size: 0.9rem;
        }
        
        .input-group-text {
            padding: 0.375rem 0.75rem;
        }
    }

    /* Custom Styles */
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
    
    .bg-primary-soft {
        background-color: rgba(78, 115, 223, 0.1);
    }
    
    .bg-info-soft {
        background-color: rgba(54, 185, 204, 0.1);
    }
    
    .border-bottom {
        border-bottom: 1px solid #e3e6f0 !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .list-group-item {
        transition: background-color 0.2s ease;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    /* Required field asterisk */
    .text-danger {
        color: #e74a3b !important;
    }
    
    /* Form section headers */
    h6.text-primary {
        color: #4e73df !important;
    }
    
    /* Help text styling */
    .form-text {
        font-size: 0.8rem;
    }
    
    /* Badge improvements */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    /* Input group button */
    #togglePassword {
        border-color: #d1d3e2;
    }
    
    #togglePassword:hover {
        background-color: #eaecf4;
        border-color: #b7b9cc;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
        
        // Auto-format contact number
        const contactInput = document.getElementById('contact');
        if (contactInput) {
            contactInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    if (value.length <= 3) {
                        value = `(${value}`;
                    } else if (value.length <= 6) {
                        value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
                    } else {
                        value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
                    }
                }
                e.target.value = value;
            });
        }
        
        // Form validation
        const form = document.getElementById('userForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        
                        // Create error message if not exists
                        if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback d-block';
                            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i> This field is required`;
                            field.parentNode.parentNode.appendChild(errorDiv);
                        }
                    } else {
                        field.classList.remove('is-invalid');
                        // Remove error message if exists
                        const errorDiv = field.parentNode.parentNode.querySelector('.invalid-feedback.d-block');
                        if (errorDiv) {
                            errorDiv.remove();
                        }
                    }
                });
                
                // Check password match
                const password = document.getElementById('password');
                const confirmPassword = document.getElementById('password_confirmation');
                
                if (password && confirmPassword && password.value !== confirmPassword.value) {
                    isValid = false;
                    confirmPassword.classList.add('is-invalid');
                    
                    if (!confirmPassword.nextElementSibling || !confirmPassword.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i> Passwords do not match`;
                        confirmPassword.parentNode.parentNode.appendChild(errorDiv);
                    }
                }
                
                // Check if at least one role is selected
                const roles = form.querySelectorAll('input[name="role"]');
                let roleSelected = false;
                roles.forEach(role => {
                    if (role.checked) roleSelected = true;
                });
                
                if (!roleSelected) {
                    isValid = false;
                    const roleErrorDiv = document.getElementById('roleError');
                    if (roleErrorDiv) {
                        roleErrorDiv.style.display = 'block';
                    }
                }
                
                // Check if at least one status is selected
                const statuses = form.querySelectorAll('input[name="status"]');
                let statusSelected = false;
                statuses.forEach(status => {
                    if (status.checked) statusSelected = true;
                });
                
                if (!statusSelected) {
                    isValid = false;
                    const statusErrorDiv = document.getElementById('statusError');
                    if (statusErrorDiv) {
                        statusErrorDiv.style.display = 'block';
                    }
                }
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Scroll to first error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }
            });
        }
        
        // Real-time email validation
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(this.value) && this.value.trim() !== '') {
                    this.classList.add('is-invalid');
                    
                    if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i> Please enter a valid email address`;
                        this.parentNode.parentNode.appendChild(errorDiv);
                    }
                } else {
                    this.classList.remove('is-invalid');
                    const errorDiv = this.parentNode.parentNode.querySelector('.invalid-feedback.d-block');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            });
        }
        
        // Remove error when radio button is selected
        const roleInputs = document.querySelectorAll('input[name="role"]');
        roleInputs.forEach(input => {
            input.addEventListener('change', function() {
                const roleErrorDiv = document.getElementById('roleError');
                if (roleErrorDiv) {
                    roleErrorDiv.style.display = 'none';
                }
            });
        });
        
        const statusInputs = document.querySelectorAll('input[name="status"]');
        statusInputs.forEach(input => {
            input.addEventListener('change', function() {
                const statusErrorDiv = document.getElementById('statusError');
                if (statusErrorDiv) {
                    statusErrorDiv.style.display = 'none';
                }
            });
        });
    });
</script>

@endsection