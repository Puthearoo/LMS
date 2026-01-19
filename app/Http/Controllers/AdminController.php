<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Checkout;
use App\Models\Reservation;
use App\Models\Fine;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    public function dashboard()
    {
        // Admin sees everything
        $stats = [
            'total_books' => Book::count(),
            'total_users' => User::count(),
            'total_librarians' => User::where('role', 'librarian')->count(),
            'total_students' => User::where('role', 'student')->count(),
            'active_checkouts' => Checkout::where('returned_at', null)->count(),
            'overdue_books' => Checkout::where('returned_at', null)
                ->where('due_date', '<', now())->count(),
            'total_fines' => Fine::where('status', 'unpaid')->sum('amount'),
            'system_health' => 'Good',
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentBooks = Book::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentBooks'));
    }
}
