<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Checkout;
use App\Models\Reservation;
use App\Models\Fine;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:student']);
    }

    public function dashboard()
    {
        $user = auth()->user();
        $userId = $user->id;

        $stats = [
            'current_checkouts' => Checkout::where('user_id', $userId)
                ->whereNull('returned_at')
                ->count(),

            'overdue_books' => Checkout::where('user_id', $userId)
                ->whereNull('returned_at')
                ->where('due_date', '<', now())
                ->count(),

            'active_reservations' => Reservation::where('user_id', $userId)
                ->where('status', 'active')
                ->count(),

            'total_fines' => Fine::where('user_id', $userId)
                ->where('status', 'unpaid')
                ->sum('amount'),
        ];

        $myCheckouts = Checkout::with('book')
            ->where('user_id', $userId)
            ->whereNull('returned_at')
            ->orderBy('due_date', 'asc')
            ->get();

        $myReservations = Reservation::with('book')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        return view('student.dashboard', compact('stats', 'myCheckouts', 'myReservations'));
    }
}