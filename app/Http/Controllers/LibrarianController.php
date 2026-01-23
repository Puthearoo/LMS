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
    private $overdueFineRate = 0.50; // $0.50 per day

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
                ->where('due_date', '<', now())
                ->count(),
            'active_reservations' => Reservation::whereIn('status', ['waiting', 'ready'])->count(),
            'unpaid_fines' => Fine::where('status', 'unpaid')->sum('amount'),
            'pending_extensions' => Checkout::where('extension_requested', true)
                ->where('status', 'checked_out')
                ->count(),
            'total_overdue_fines' => $this->calculateTotalOverdueFines(),
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

        // Overdue books - Calculate days overdue (rounded UP) and sort by most overdue first
        $pendingReturns = Checkout::with(['book', 'user'])
            ->whereNull('return_date')
            ->where('due_date', '<', now())
            ->get()
            ->map(function ($checkout) {
                // Calculate days overdue and round UP (libraries charge for full days)
                $checkout->daysOverdue = $this->calculateDaysOverdue($checkout);
                $checkout->fineAmount = $this->calculateOverdueFine($checkout->daysOverdue);
                $checkout->canExtend = $this->canExtendCheckout($checkout);
                $checkout->isExtended = $checkout->extension_status === 'approved';
                $checkout->urgency = $this->getUrgencyLevel($checkout->daysOverdue);
                return $checkout;
            })
            ->sortByDesc('daysOverdue')
            ->take(5);

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
            ->where('due_date', '>=', now())
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
     * Calculate days overdue (rounded UP for library fines)
     */
    private function calculateDaysOverdue($checkout)
    {
        if (!$checkout->due_date || $checkout->return_date) {
            return 0;
        }

        $dueDate = Carbon::parse($checkout->due_date);

        // If not overdue yet
        if (now()->lt($dueDate)) {
            return 0;
        }

        // Calculate full days overdue (round UP)
        // Use floor for negative days, ceil for positive days
        $days = $dueDate->diffInDays(now(), false);

        if ($days <= 0) {
            return 0;
        }

        // For positive days, if there's any partial day, round UP
        $fullDays = (int) $days;
        $hasPartialDay = ($days - $fullDays) > 0;

        return $hasPartialDay ? $fullDays + 1 : $fullDays;
    }

    /**
     * Calculate overdue fine amount
     */
    private function calculateOverdueFine($daysOverdue)
    {
        if ($daysOverdue <= 0) {
            return 0;
        }

        return $daysOverdue * $this->overdueFineRate;
    }

    /**
     * Calculate total potential overdue fines
     */
    private function calculateTotalOverdueFines()
    {
        $totalOverdueDays = Checkout::whereNull('return_date')
            ->where('due_date', '<', now())
            ->get()
            ->sum(function ($checkout) {
                return $this->calculateDaysOverdue($checkout);
            });

        return $totalOverdueDays * $this->overdueFineRate;
    }

    /**
     * Determine urgency level based on days overdue
     */
    private function getUrgencyLevel($daysOverdue)
    {
        if ($daysOverdue <= 0) {
            return 'none';
        }

        if ($daysOverdue > 14) {
            return 'high';
        } elseif ($daysOverdue > 7) {
            return 'medium';
        } else {
            return 'low';
        }
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

        // Can't extend if overdue
        if ($checkout->due_date && now()->gt($checkout->due_date)) {
            return false;
        }

        return true;
    }
}