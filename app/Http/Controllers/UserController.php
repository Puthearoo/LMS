<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Keep for other Hash methods if needed
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'student') // Librarians care about borrowers
            ->withCount([
                // Active loans
                'checkouts as active_loans_count' => function ($q) {
                    $q->whereIn('status', ['approved', 'checked_out']);
                },
                // Overdue loans
                'checkouts as overdue_loans_count' => function ($q) {
                    $q->whereIn('status', ['approved', 'checked_out'])
                        ->where('due_date', '<', now());
                },
                // Unpaid fines count
                'fines as unpaid_fines_count' => function ($q) {
                    $q->where('status', 'unpaid');
                },
                // Active reservations
                'reservations as active_reservations_count' => function ($q) {
                    $q->whereIn('status', ['waiting', 'ready']);
                },
            ])
            ->withSum([
                'fines as unpaid_fines_total' => function ($q) {
                    $q->where('status', 'unpaid');
                }
            ], 'amount');

        /* 🔍 Filters */
        // User account status (active / blocked)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Search by name / email / contact
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact', 'like', "%{$search}%");
            });
        }
        // Only users with overdue loans
        if ($request->boolean('overdue_only')) {
            $query->havingRaw('overdue_loans_count > 0');
        }
        // Only users with unpaid fines
        if ($request->boolean('unpaid_only')) {
            $query->havingRaw('unpaid_fines_count > 0');
        }
        // Only users with active reservations
        if ($request->boolean('reserved_only')) {
            $query->havingRaw('active_reservations_count > 0');
        }

        $users = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        /* 📊 Dashboard Statistics */
        $stats = [
            'totalStudents' => User::where('role', 'student')->count(),
            'activeBorrowers' => Checkout::whereIn('status', ['approved', 'checked_out'])
                ->distinct('user_id')
                ->count('user_id'),
            'overdueUsers' => Checkout::whereIn('status', ['approved', 'checked_out'])
                ->where('due_date', '<', now())
                ->distinct('user_id')
                ->count('user_id'),
            'usersWithUnpaidFines' => Fine::where('status', 'unpaid')
                ->distinct('user_id')
                ->count('user_id'),
        ];

        return view('librarian.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get recently added users for the sidebar
        $recentUsers = User::latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'role', 'status', 'created_at']);

        return view('librarian.users.create', compact('recentUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'contact' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,librarian',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Create the user with bcrypt
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact' => $validated['contact'] ?? null,
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('librarian.users.show', $user)
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('librarian.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('librarian.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'contact' => 'required|string|max:20',
            'role' => 'required|in:student,librarian',
            'status' => 'required|in:active,inactive,suspended',
            'password' => 'nullable|min:8|confirmed'
        ]);

        // Use bcrypt consistently
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('librarian.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('librarian.users.index')
            ->with('success', 'User deleted successfully.');
    }
}