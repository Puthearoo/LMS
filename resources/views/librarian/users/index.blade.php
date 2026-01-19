@extends('layouts.librarian')

@section('title', 'Borrowers Management')

@section('content')
<div class="container-fluid px-2 px-md-3 px-lg-4 py-2 py-md-3">

    <!-- Header with Breadcrumb -->
    <div class="mb-3 mb-md-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-1 mb-md-2">
                <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Borrowers</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <div class="mb-2 mb-md-0">
                <h1 class="h3 h2-md mb-1 font-weight-bold">Borrowers Management</h1>
                <p class="text-muted mb-0 d-none d-md-block">Track students with active loans, reservations, and fines</p>
                <p class="text-muted mb-0 d-block d-md-none">Manage student borrowers</p>
            </div>
            <div>
                <button class="btn btn-outline-secondary btn-sm d-md-none" onclick="window.print()">
                    <i class="fas fa-print"></i>
                </button>
                <button class="btn btn-outline-secondary d-none d-md-inline-flex align-items-center justify-content-center" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        @php
            $cards = [
                [
                    'title' => 'Total Students',
                    'count' => $stats['totalStudents'],
                    'icon' => 'fas fa-user-graduate',
                    'color' => 'primary',
                    'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
                ],
                [
                    'title' => 'Active Borrowers',
                    'count' => $stats['activeBorrowers'],
                    'icon' => 'fas fa-book-reader',
                    'color' => 'success',
                    'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
                ],
                [
                    'title' => 'Overdue Users',
                    'count' => $stats['overdueUsers'],
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => 'danger',
                    'gradient' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'
                ],
                [
                    'title' => 'Unpaid Fines',
                    'count' => $stats['usersWithUnpaidFines'],
                    'icon' => 'fas fa-coins',
                    'color' => 'warning',
                    'gradient' => 'linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%)'
                ],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="col-6 col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body p-2 p-md-3">
                        <div class="d-flex justify-content-between align-items-start mb-2 mb-md-3">
                            <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                                 style="background: {{ $card['gradient'] }}; width: 36px; height: 36px;">
                                <i class="{{ $card['icon'] }} text-white" style="font-size: 0.9rem;"></i>
                            </div>
                            @if($card['color'] === 'danger' && $card['count'] > 0)
                                <span class="badge bg-danger-soft text-danger small">
                                    <i class="fas fa-arrow-up"></i>
                                    <span class="d-none d-sm-inline">Alert</span>
                                </span>
                            @endif
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fs-8 fs-md-7 mb-1 font-weight-normal">
                                {{ $card['title'] }}
                            </h6>
                            <h2 class="h4 h3-md mb-0 font-weight-bold">{{ number_format($card['count']) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Enhanced Filters -->
    <div class="card border-0 shadow-sm mb-3 mb-md-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('librarian.users.index') }}" id="filterForm">
                <div class="row g-2 g-md-3 align-items-end">
                    <!-- Search Input -->
                    <div class="col-12 col-md-5 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-0" name="search"
                                placeholder="Search borrowers..."
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Filter Checkboxes -->
                    <div class="col-12 col-md-7 col-lg-8">
                        <div class="d-flex flex-wrap gap-2 gap-md-3">
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="checkbox" name="overdue_only" 
                                       value="1" id="overdueCheck"
                                       {{ request('overdue_only') ? 'checked' : '' }}
                                       onchange="document.getElementById('filterForm').submit()">
                                <label class="form-check-label small" for="overdueCheck">
                                    <i class="fas fa-exclamation-circle text-danger me-1"></i>
                                    <span class="d-none d-sm-inline">Overdue</span>
                                </label>
                            </div>

                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="checkbox" name="unpaid_only" 
                                       value="1" id="unpaidCheck"
                                       {{ request('unpaid_only') ? 'checked' : '' }}
                                       onchange="document.getElementById('filterForm').submit()">
                                <label class="form-check-label small" for="unpaidCheck">
                                    <i class="fas fa-dollar-sign text-warning me-1"></i>
                                    <span class="d-none d-sm-inline">Unpaid Fines</span>
                                </label>
                            </div>

                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="checkbox" name="reserved_only" 
                                       value="1" id="reservedCheck"
                                       {{ request('reserved_only') ? 'checked' : '' }}
                                       onchange="document.getElementById('filterForm').submit()">
                                <label class="form-check-label small" for="reservedCheck">
                                    <i class="fas fa-bookmark text-info me-1"></i>
                                    <span class="d-none d-sm-inline">Reserved</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                @if(request()->hasAny(['search', 'overdue_only', 'unpaid_only', 'reserved_only']))
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex flex-wrap align-items-center gap-1 gap-md-2">
                            <span class="small text-muted">Active filters:</span>
                            
                            @if(request('search'))
                                <span class="badge bg-light text-dark border small">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ route('librarian.users.index', request()->except('search')) }}" 
                                       class="text-dark ms-1 text-decoration-none">×</a>
                                </span>
                            @endif
                            
                            @if(request('overdue_only'))
                                <span class="badge bg-danger-soft text-danger small">
                                    Overdue
                                    <a href="{{ route('librarian.users.index', request()->except('overdue_only')) }}" 
                                       class="text-danger ms-1 text-decoration-none">×</a>
                                </span>
                            @endif
                            
                            @if(request('unpaid_only'))
                                <span class="badge bg-warning-soft text-warning small">
                                    Unpaid Fines
                                    <a href="{{ route('librarian.users.index', request()->except('unpaid_only')) }}" 
                                       class="text-warning ms-1 text-decoration-none">×</a>
                                </span>
                            @endif
                            
                            @if(request('reserved_only'))
                                <span class="badge bg-info-soft text-info small">
                                    Reserved
                                    <a href="{{ route('librarian.users.index', request()->except('reserved_only')) }}" 
                                       class="text-info ms-1 text-decoration-none">×</a>
                                </span>
                            @endif
                            
                            <a href="{{ route('librarian.users.index') }}" class="small text-primary ms-2">
                                Clear all
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Enhanced Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-2 py-md-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-1">
                <h5 class="mb-0 font-weight-bold">
                    <i class="fas fa-users text-primary me-2"></i>
                    <span class="d-none d-sm-inline">Borrowers List</span>
                    <span class="d-inline d-sm-none">Borrowers</span>
                </h5>
                <span class="text-muted small">
                    <span class="d-none d-md-inline">
                        Showing <strong>{{ $users->firstItem() ?? 0 }}</strong> – 
                        <strong>{{ $users->lastItem() ?? 0 }}</strong> of 
                        <strong>{{ $users->total() }}</strong>
                    </span>
                    <span class="d-inline d-md-none">
                        <strong>{{ $users->total() }}</strong> total
                    </span>
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="text-center py-4 py-md-5">
                    <div class="mb-3 mb-md-4">
                        <i class="fas fa-users fa-3x fa-4x-md text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">No Borrowers Found</h5>
                    <p class="text-muted small mb-0">
                        @if(request()->hasAny(['search', 'overdue_only', 'unpaid_only', 'reserved_only']))
                            Try adjusting your filters to see more results
                        @else
                            No students have borrowed books yet
                        @endif
                    </p>
                </div>
            @else
                <!-- Mobile Cards View -->
                <div class="d-md-none">
                    @foreach($users as $user)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="font-weight-bold">{{ $user->name }}</div>
                                    <div class="small text-muted mb-1">{{ $user->email }}</div>
                                    <div class="small text-muted">
                                        <i class="fas fa-id-card fa-xs me-1"></i>ID: {{ $user->id }}
                                    </div>
                                </div>
                                <a href="{{ route('librarian.users.show', $user) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-2 align-self-start">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-center border rounded py-2">
                                        <div class="small text-muted mb-1">Active Loans</div>
                                        <div class="font-weight-bold">
                                            @if($user->active_loans_count > 0)
                                                <span class="text-info">{{ $user->active_loans_count }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center border rounded py-2">
                                        <div class="small text-muted mb-1">Overdue</div>
                                        <div class="font-weight-bold">
                                            @if($user->overdue_loans_count > 0)
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle fa-xs me-1"></i>
                                                    {{ $user->overdue_loans_count }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center border rounded py-2">
                                        <div class="small text-muted mb-1">Reservations</div>
                                        <div class="font-weight-bold">
                                            @if($user->active_reservations_count > 0)
                                                <span class="text-warning">{{ $user->active_reservations_count }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center border rounded py-2">
                                        <div class="small text-muted mb-1">Unpaid Fines</div>
                                        <div class="font-weight-bold">
                                            @if($user->unpaid_fines_count > 0)
                                                <div class="text-danger">
                                                    <span>{{ $user->unpaid_fines_count }}</span>
                                                    <div class="xsmall mt-1">${{ number_format($user->unpaid_fines_total ?? 0, 2) }}</div>
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4">Student Information</th>
                                <th class="border-0 text-center">Active Loans</th>
                                <th class="border-0 text-center">Overdue</th>
                                <th class="border-0 text-center">Reservations</th>
                                <th class="border-0 text-center">Unpaid Fines</th>
                                <th class="border-0 text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3">
                                        <div class="font-weight-bold mb-1">{{ $user->name }}</div>
                                        <div class="small text-muted mb-1">{{ $user->email }}</div>
                                        <div class="small">
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-id-card fa-xs me-1"></i>ID: {{ $user->id }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        @if($user->active_loans_count > 0)
                                            <span class="badge bg-info-soft text-info px-3 py-2 rounded-pill">
                                                {{ $user->active_loans_count }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if($user->overdue_loans_count > 0)
                                            <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ $user->overdue_loans_count }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if($user->active_reservations_count > 0)
                                            <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill">
                                                {{ $user->active_reservations_count }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if($user->unpaid_fines_count > 0)
                                            <div>
                                                <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill">
                                                    {{ $user->unpaid_fines_count }}
                                                </span>
                                                <div class="small text-danger font-weight-bold mt-1">
                                                    ${{ number_format($user->unpaid_fines_total ?? 0, 2) }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="text-center pe-4">
                                        <a href="{{ route('librarian.users.show', $user) }}"
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Enhanced Pagination -->
                @if($users->hasPages())
                    <div class="p-3 p-md-4 border-top bg-light">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                            <div class="text-muted small text-center text-md-start">
                                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                            </div>
                            <div>
                                {{ $users->withQueryString()->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>

<style>
    /* Responsive Utilities */
    .fs-8 { font-size: 0.75rem; }
    .fs-7 { font-size: 0.875rem; }
    
    .h3-md { font-size: 1.75rem; }
    .h4 { font-size: 1.25rem; }
    
    @media (min-width: 768px) {
        .fs-8 { font-size: 0.8rem; }
        .h4 { font-size: 1.5rem; }
    }
    
    @media (min-width: 992px) {
        .fs-8 { font-size: 0.875rem; }
        .h4 { font-size: 1.75rem; }
    }

    /* Media Queries */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .h3 {
            font-size: 1.25rem;
        }
        
        .stat-icon {
            width: 32px !important;
            height: 32px !important;
        }
        
        .card-body {
            padding: 0.75rem !important;
        }
    }
    
    @media (max-width: 576px) {
        .breadcrumb {
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        
        .col-6 > .card {
            margin-bottom: 0.25rem;
        }
        
        .badge {
            font-size: 0.65rem;
            padding: 0.2em 0.4em;
        }
        
        .xsmall {
            font-size: 0.65rem;
        }
    }
    
    @media (min-width: 768px) and (max-width: 992px) {
        .col-md-6 > .card .stat-icon {
            width: 40px !important;
            height: 40px !important;
        }
    }

    /* Hover effects */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1) !important;
    }

    /* Soft badge backgrounds */
    .bg-primary-soft {
        background-color: rgba(78, 115, 223, 0.1);
    }
    
    .bg-info-soft {
        background-color: rgba(54, 185, 204, 0.1);
    }
    
    .bg-success-soft {
        background-color: rgba(28, 200, 138, 0.1);
    }
    
    .bg-warning-soft {
        background-color: rgba(246, 194, 62, 0.1);
    }
    
    .bg-danger-soft {
        background-color: rgba(231, 74, 59, 0.1);
    }

    /* Table enhancements */
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.03);
    }

    /* Input focus effects */
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    /* Badge enhancements */
    .badge {
        font-weight: 500;
    }

    .rounded-pill {
        border-radius: 50rem !important;
    }
    
    /* Mobile card improvements */
    .d-md-none .border.rounded {
        border: 1px solid #dee2e6 !important;
        background: #f8f9fa;
    }
    
    .d-md-none .col-6 {
        padding: 0.25rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add active class to current table row on click (mobile)
        const mobileCards = document.querySelectorAll('.d-md-none .border-bottom');
        mobileCards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Only trigger if not clicking on a button or link
                if (!e.target.closest('a, button')) {
                    this.classList.toggle('active');
                }
            });
        });
        
        // Responsive table row highlighting
        const tableRows = document.querySelectorAll('.table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('a, button')) {
                    this.classList.toggle('table-active');
                }
            });
        });
    });
</script>

@endsection