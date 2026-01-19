@extends('layouts.librarian')

@section('title', 'Edit User - Library System')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Edit User: {{ $user->name }}</h1>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Edit Form -->
    <form action="{{ route('librarian.users.update', $user) }}" method="POST" class="card shadow p-4">
    @csrf
    @method('PUT')

    <!-- Name & Email -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="font-weight-bold">Full Name *</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="font-weight-bold">Email *</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Contact & Role -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="font-weight-bold">Contact *</label>
            <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
                   value="{{ old('contact', $user->contact) }}" required>
            @error('contact')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="font-weight-bold">Role *</label>
            <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student</option>
                <option value="librarian" {{ old('role', $user->role) === 'librarian' ? 'selected' : '' }}>Librarian</option>
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Password -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>New Password (optional)</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Leave blank to keep current password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Confirm new password">
        </div>
    </div>

    <!-- Status -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="font-weight-bold">Status *</label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Buttons -->
    <div class="d-flex justify-content-between mt-4">
        <!-- Left: Delete Button (separate form) -->
        <form action="{{ route('librarian.users.destroy', $user) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>

        <!-- Right: Cancel & Save Buttons (Edit Form Submit) -->
        <div>
            <a href="{{ route('librarian.users.show', $user) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
    </form>




@endsection

@push('scripts')
<script>
function confirmDelete(action, userName) {
    // Set the user name in the modal
    document.getElementById('deleteUserName').textContent = userName;
    
    // Set the form action
    document.getElementById('deleteForm').action = action;
    
    // Show the modal
    $('#deleteModal').modal('show');
}
</script>
@endpush