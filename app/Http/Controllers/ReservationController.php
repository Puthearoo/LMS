<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Checkout;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Notifications\BookReservedNotification;
use App\Notifications\ReservationDateUpdated;
use App\Notifications\BookAvailableNotification;

class ReservationController extends Controller
{
    /**
     * Display user's reservations
     */
    public function index()
    {
        $reservations = Reservation::with('book')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Display all reservations for librarians
     */
    public function librarianIndex(Request $request)
    {
        $query = Reservation::with(['user', 'book']);

        // Apply status filters
        if ($request->has('status')) {
            switch ($request->status) {
                case 'waiting':
                    $query->where('status', 'waiting');
                    break;
                case 'ready':
                    $query->where('status', 'ready');
                    break;
                case 'active':
                    $query->active();
                    break;
                case 'expiring':
                    $query->where('status', 'ready')
                        ->where('expiry_date', '<=', now()->addDays(2))
                        ->where('expiry_date', '>=', now());
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'cancelled':
                    $query->where('status', 'like', 'cancelled_by_%');
                    break;
                case 'picked_up':
                    $query->where('status', 'picked_up');
                    break;
            }
        }

        $reservations = $query->latest()->paginate($request->per_page ?? 10);

        // Get counts for all statuses
        $totalReservations = Reservation::count();
        $waitingCount = Reservation::where('status', 'waiting')->count();
        $readyCount = Reservation::where('status', 'ready')->count();
        $activeCount = Reservation::active()->count();
        $expiringCount = Reservation::where('status', 'ready')
            ->where('expiry_date', '<=', now()->addDays(2))
            ->where('expiry_date', '>=', now())
            ->count();
        $expiredCount = Reservation::expired()->count();
        $cancelledCount = Reservation::where('status', 'like', 'cancelled_by_%')->count();
        $pickedUpCount = Reservation::where('status', 'picked_up')->count();

        $currentFilter = $request->status;

        return view('librarian.reservations.index', compact(
            'reservations',
            'currentFilter',
            'totalReservations',
            'waitingCount',
            'readyCount',
            'activeCount',
            'expiringCount',
            'expiredCount',
            'cancelledCount',
            'pickedUpCount'
        ));
    }

    /**
     * Store a new reservation
     */
    public function store(Request $request, Book $book)
    {
        // 1. Check authentication
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to reserve books.');
        }

        // 2. Check if book is available - should checkout instead
        if ($book->availability_status === 'available') {
            return redirect()->back()->with('error', 'This book is available for immediate checkout! Please use the checkout button instead.');
        }

        // 3. Check if user already has an active reservation for this book
        $existingReservation = Reservation::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereIn('status', ['waiting', 'ready'])
            ->first();

        if ($existingReservation) {
            return redirect()->back()->with('error', 'You already have an active reservation for this book.');
        }

        // 4. Check reservation limit
        $userReservationCount = Reservation::where('user_id', auth()->id())
            ->whereIn('status', ['waiting', 'ready'])
            ->count();

        if ($userReservationCount >= 5) {
            return redirect()->back()->with('error', 'You have reached the maximum limit of 5 active reservations.');
        }

        // 5. Check if book has any available copies (based on status only)
        if ($book->availability_status === 'available') {
            return redirect()->back()->with('error', 'Book is available for immediate checkout. No reservation needed.');
        }

        try {
            DB::beginTransaction();

            // 6. Calculate queue position
            $queuePosition = Reservation::where('book_id', $book->id)
                ->where('status', 'waiting')
                ->count() + 1;

            // 7. Create the reservation
            $reservation = Reservation::create([
                'user_id' => auth()->id(),
                'book_id' => $book->id,
                'reservation_date' => now(),
                'expiry_date' => null,
                'status' => 'waiting',
            ]);

            // 8. Calculate expected return date
            $expectedReturnDate = null;
            $currentCheckout = Checkout::where('book_id', $book->id)
                ->whereNull('return_date')
                ->whereIn('status', ['approved', 'borrowed', 'checked_out'])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($currentCheckout) {
                $expectedReturnDate = $currentCheckout->due_date;
                if ($currentCheckout->extension_status === 'approved' && $currentCheckout->extended_due_date) {
                    $expectedReturnDate = $currentCheckout->extended_due_date;
                }
            } else {
                // If no current checkout, book might be unavailable for other reasons
                $expectedReturnDate = now()->addDays(14);
            }

            // 9. Send reservation confirmation notification
            try {
                $user = auth()->user();
                $user->notify(new BookReservedNotification(
                    $reservation,
                    $expectedReturnDate ? $expectedReturnDate->format('F j, Y') : 'Unknown',
                    $queuePosition
                ));
            } catch (\Exception $e) {
                Log::error('Failed to send reservation notification: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('reservations.index')
                ->with('success', 'Book reserved successfully! You are #' . $queuePosition . ' in queue. Expected availability: ' . ($expectedReturnDate ? $expectedReturnDate->format('M d, Y') : 'Unknown'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create reservation: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a reservation (user action)
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        // Check if user owns this reservation
        if ($reservation->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Check if reservation can be cancelled
        if (!$reservation->canBeCancelled()) {
            return redirect()->back()->with('error', 'This reservation cannot be cancelled.');
        }

        // Store the original status BEFORE updating
        $originalStatus = $reservation->status;

        // Store who cancelled
        $reservation->update([
            'status' => 'cancelled_by_user'
        ]);

        // If the reservation was ready, free up the book
        if ($originalStatus === 'ready' && $reservation->book->availability_status === 'reserved') {
            $reservation->book->update([
                'availability_status' => 'available'
            ]);
        }

        // If there are waiting reservations for the same book, notify next in line
        if ($originalStatus === 'waiting') {
            $this->notifyNextInQueue($reservation->book_id);
        }

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation cancelled successfully.');
    }

    public function librarianCancel(Request $request, Reservation $reservation)
    {
        // Store the original status BEFORE updating
        $originalStatus = $reservation->status;

        // Store who cancelled
        $reservation->update([
            'status' => 'cancelled_by_librarian'
        ]);

        // If the reservation was ready, free up the book
        if ($originalStatus === 'ready' && $reservation->book->availability_status === 'reserved') {
            $reservation->book->update([
                'availability_status' => 'available'
            ]);
        }

        // If there are waiting reservations for the same book, notify next in line
        if ($originalStatus === 'waiting') {
            $this->notifyNextInQueue($reservation->book_id);
        }

        return redirect()->route('librarian.reservations.index')
            ->with('success', 'Reservation cancelled successfully.');
    }

    /**
     * Mark reservation as ready for pickup (librarian only)
     */
    public function confirm(Request $request, Reservation $reservation)
    {
        // Check if the reservation is waiting
        if ($reservation->status !== 'waiting') {
            return redirect()->back()->with(
                'error',
                'Only waiting reservations can be marked as ready.'
            );
        }

        // Check if book is actually available for marking as ready
        if (!$reservation->canBeMarkedAsReady()) {
            // Check if there's an expired ready reservation
            $expiredReadyReservation = Reservation::where('book_id', $reservation->book_id)
                ->where('status', 'ready')
                ->where('expiry_date', '<', now())
                ->first();

            if ($expiredReadyReservation) {
                // Auto-expire the old reservation
                $expiredReadyReservation->update(['status' => 'expired']);
                $expiredReadyReservation->book->update(['availability_status' => 'available']);

                // Now try again
                return redirect()->route('librarian.reservations.confirm', $reservation)
                    ->with('info', 'Expired reservation cleaned up. Please try marking as ready again.');
            }

            return redirect()->back()->with(
                'error',
                'Cannot mark as ready: Book is not available for checkout. Current status: ' . $reservation->book->availability_status
            );
        }

        // Check if this is the first in queue
        $isFirstInQueue = Reservation::where('book_id', $reservation->book_id)
            ->where('status', 'waiting')
            ->where('created_at', '<', $reservation->created_at)
            ->doesntExist();

        if (!$isFirstInQueue) {
            return redirect()->back()->with(
                'error',
                'Cannot mark as ready: There are other users ahead in the queue.'
            );
        }

        try {
            DB::beginTransaction();

            // Set expiry date (3 days from now)
            $expiryDate = now()->addDays(3)->startOfDay();

            // Update the reservation
            $reservation->update([
                'status' => 'ready',
                'expiry_date' => $expiryDate
            ]);

            // Update book status to reserved to prevent others from checking it out
            $reservation->book->update([
                'availability_status' => 'reserved'
            ]);

            // SEND BOOK AVAILABLE NOTIFICATION TO USER - (*not work)
            try {
                $reservation->user->notify(new BookAvailableNotification(
                    $reservation,
                    $expiryDate->format('F j, Y')
                ));
            } catch (\Exception $e) {
                Log::error('Failed to send book available notification: ' . $e->getMessage());
            }

            // Log the action
            Log::info('Reservation marked as ready and user notified', [
                'reservation_id' => $reservation->id,
                'book_id' => $reservation->book_id,
                'user_id' => $reservation->user_id,
                'expiry_date' => $expiryDate->format('Y-m-d'),
                'librarian_id' => auth()->id()
            ]);

            DB::commit();

            return redirect()->route('librarian.reservations.index')
                ->with('success', 'Reservation marked as ready! User has been notified. Pickup deadline: ' . $expiryDate->format('M d, Y'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark reservation as ready: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark reservation as ready.');
        }
    }

    /**
     * Mark reservation as picked up (librarian only)
     */
    public function markAsPickedUp(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== 'ready') {
            return redirect()->back()->with('error', 'Only ready reservations can be picked up.');
        }

        // Check if pickup deadline has passed
        if ($reservation->expiry_date && $reservation->expiry_date < now()) {
            return redirect()->back()->with(
                'error',
                'Pickup deadline has passed. Please mark as expired instead.'
            );
        }

        try {
            DB::beginTransaction();

            // 1. Check if book is still available or reserved
            if (!in_array($reservation->book->availability_status, ['available', 'reserved'])) {
                return redirect()->back()->with('error', 'Book is no longer available for checkout. Current status: ' . $reservation->book->availability_status);
            }

            // 2. Create a checkout record with PROPER RESERVATION LINKING
            $checkout = Checkout::create([
                'user_id' => $reservation->user_id,
                'book_id' => $reservation->book_id,
                'reservation_id' => $reservation->id,
                'checkout_date' => now(),
                'due_date' => now()->addDays(14),
                'status' => 'checked_out'
            ]);

            // 3. Update reservation to 'picked_up'
            $reservation->update([
                'status' => 'picked_up',
                'expiry_date' => null,
                'checkout_id' => $checkout->id
            ]);

            // 4. Update book availability to checked_out
            $reservation->book->update([
                'availability_status' => 'checked_out'
            ]);

            // 5. Cancel other reservations for same book
            $otherReservations = Reservation::where('book_id', $reservation->book_id)
                ->whereIn('status', ['waiting', 'ready'])
                ->where('id', '!=', $reservation->id)
                ->get();

            foreach ($otherReservations as $otherRes) {
                $otherRes->update([
                    'status' => 'cancelled_by_librarian',
                    'expiry_date' => null
                ]);
            }

            Log::info('Book picked up and checked out', [
                'reservation_id' => $reservation->id,
                'checkout_id' => $checkout->id,
                'book_id' => $reservation->book_id,
                'user_id' => $reservation->user_id,
                'due_date' => $checkout->due_date->format('Y-m-d')
            ]);

            DB::commit();

            return redirect()->route('librarian.reservations.index')
                ->with('success', 'Book marked as picked up and checked out. Due date: ' . $checkout->due_date->format('M d, Y'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark as picked up: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process pickup: ' . $e->getMessage());
        }
    }

    /**
     * Mark reservation as expired (librarian only)
     */
    public function expire(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== 'ready') {
            return redirect()->back()->with(
                'error',
                'Only ready reservations can be expired.'
            );
        }

        try {
            DB::beginTransaction();

            // Mark as expired
            $reservation->update(['status' => 'expired']);

            // IMPORTANT: Always set book status back to available when reservation expires
            $reservation->book->update([
                'availability_status' => 'available'
            ]);

            // Move to next in queue if available
            $nextReservation = Reservation::where('book_id', $reservation->book_id)
                ->where('status', 'waiting')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($nextReservation) {
                // Automatically mark next reservation as ready
                $expiryDate = now()->addDays(3)->startOfDay();
                $nextReservation->update([
                    'status' => 'ready',
                    'expiry_date' => $expiryDate
                ]);

                // Update book status to reserved
                $reservation->book->update([
                    'availability_status' => 'reserved'
                ]);

                // NOTIFY NEXT USER THAT BOOK IS AVAILABLE *Not work
                try {
                    $nextReservation->user->notify(new BookAvailableNotification(
                        $nextReservation,
                        $expiryDate->format('F j, Y')
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to notify next user: ' . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('librarian.reservations.index')
                ->with('success', 'Reservation marked as expired.' . ($nextReservation ? ' Next user has been notified.' : ''));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to expire reservation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to mark reservation as expired.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        // Check if user owns this reservation or is librarian/admin
        if ($reservation->user_id !== auth()->id() && !auth()->user()->hasRole(['librarian', 'admin'])) {
            abort(403);
        }

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        // Check if user owns this reservation
        if ($reservation->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }

    /**
     * Update reservation dates when checkout is extended
     */
    public function updateReservationDates($checkoutId)
    {
        $checkout = Checkout::findOrFail($checkoutId);

        if (!$checkout->extended_due_date) {
            return false;
        }

        $reservations = Reservation::where('book_id', $checkout->book_id)
            ->where('status', 'waiting')
            ->get();

        foreach ($reservations as $reservation) {
            // Format dates safely as strings
            $oldDate = $reservation->expiry_date;
            $oldDateFormatted = $oldDate ? Carbon::parse($oldDate)->format('F j, Y') : 'Unknown';
            $newDateFormatted = Carbon::parse($checkout->extended_due_date)->format('F j, Y');

            // Update user about new expected date
            $user = User::find($reservation->user_id);
            if ($user) {
                try {
                    $user->notify(new ReservationDateUpdated(
                        $reservation,
                        $oldDateFormatted,
                        $newDateFormatted
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to send date update notification: ' . $e->getMessage());
                }
            }

            Log::info("Updated reservation {$reservation->id} with new expected return date: {$newDateFormatted}");
        }

        return true;
    }

    /**
     * Show reservation details for librarian
     */
    public function showForLibrarian(Reservation $reservation)
    {
        return view('librarian.reservations.show', compact('reservation'));
    }

    /**
     * Notify next user in queue when a reservation is cancelled
     */
    private function notifyNextInQueue($bookId)
    {
        $nextReservation = Reservation::where('book_id', $bookId)
            ->where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($nextReservation) {
            // Calculate new queue position
            $newQueuePosition = $nextReservation->getQueuePosition();

            // Here you could send a notification if you create a QueuePositionUpdated notification
            // $nextReservation->user->notify(new QueuePositionUpdated($nextReservation, $newQueuePosition));

            Log::info("Next in queue notified", [
                'reservation_id' => $nextReservation->id,
                'user_id' => $nextReservation->user_id,
                'new_position' => $newQueuePosition
            ]);
        }
    }
}