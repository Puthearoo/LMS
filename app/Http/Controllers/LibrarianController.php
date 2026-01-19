<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Checkout;
use App\Models\Reservation;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LibrarianController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:librarian']);
    }

    public function dashboard()
    {
        // Enhanced statistics with more details
        $stats = [
            'total_books' => Book::count(),
            'today_checkouts' => Checkout::whereDate('created_at', today())->count(),
            'pending_returns' => Checkout::whereNull('return_date')
                ->where('due_date', '<', now()->toDateString())->count(),
            'active_reservations' => Reservation::whereIn('status', ['waiting', 'ready'])->count(),
            'unpaid_fines' => Fine::where('status', 'unpaid')->sum('amount'),
            'pending_extensions' => Checkout::where('extension_requested', true)
                ->where('status', 'checked_out')
                ->count(),
        ];

        // Today's checkouts
        $todayCheckouts = Checkout::with(['book', 'user'])
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($checkout) {
                $checkout->canExtend = $this->canExtendCheckout($checkout);
                $checkout->isExtended = $checkout->extension_status === 'approved';
                return $checkout;
            });

        // Overdue books
        $pendingReturns = Checkout::with(['book', 'user'])
            ->whereNull('return_date')
            ->where('due_date', '<', now()->toDateString())
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get()
            ->map(function ($checkout) {
                $checkout->canExtend = $this->canExtendCheckout($checkout);
                $checkout->isExtended = $checkout->extension_status === 'approved';
                return $checkout;
            });

        // Pending extensions
        $pendingExtensions = Checkout::with(['book', 'user'])
            ->where('extension_requested', true)
            ->where('status', 'checked_out')
            ->orderBy('extension_requested_at', 'asc')
            ->take(5)
            ->get()
            ->map(function ($checkout) {
                $checkout->canExtend = $this->canExtendCheckout($checkout);
                $checkout->isExtended = $checkout->extension_status === 'approved';
                return $checkout;
            });

        // Active checkouts
        $activeCheckouts = Checkout::with(['book', 'user'])
            ->where('status', 'checked_out')
            ->whereNull('return_date')
            ->where('due_date', '>=', now()->toDateString()) // Not overdue
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get()
            ->map(function ($checkout) {
                $checkout->canExtend = $this->canExtendCheckout($checkout);
                $checkout->isExtended = $checkout->extension_status === 'approved';
                $checkout->daysLeft = now()->diffInDays($checkout->due_date, false);
                return $checkout;
            });

        return view('librarian.dashboard', compact(
            'stats',
            'todayCheckouts',
            'pendingReturns',
            'pendingExtensions',
            'activeCheckouts'
        ));
    }

    /**
     * Helper method to determine if a checkout can be extended
     */
    private function canExtendCheckout($checkout)
    {
        // Can't extend if already returned
        if ($checkout->return_date) {
            return false;
        }

        // Can't extend if not checked out
        if ($checkout->status !== 'checked_out') {
            return false;
        }

        // Can't extend if already approved for extension
        if ($checkout->extension_status === 'approved') {
            return false;
        }

        // Can't extend if already requested and pending
        if ($checkout->extension_requested && $checkout->extension_status === 'pending') {
            return false;
        }

        return true;
    }
}