<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Models\Book;
use App\Models\Checkout;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // STUDENT: View CURRENT checkouts (active books)
    public function myCheckouts()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to view your checkouts.');
        }

        // Get ALL active checkouts including those from reservations
        $checkouts = Checkout::with(['book', 'reservation'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved', 'checked_out'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Debug logging
        Log::info('User ' . Auth::id() . ' has ' . $checkouts->count() . ' checkouts');

        foreach ($checkouts as $checkout) {
            Log::info('Checkout ' . $checkout->id . ' - Status: ' . $checkout->status .
                ' - Reservation: ' . ($checkout->reservation_id ? 'Yes' : 'No'));
        }

        return view('checkouts.my-checkouts', compact('checkouts'));
    }

    // STUDENT: View BORROWING HISTORY 
    public function borrowingHistory(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to view your borrowing history.');
        }

        $query = Checkout::with('book')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['returned', 'rejected', 'cancelled']);

        // Apply status filter
        if ($request->has('status') && in_array($request->status, ['returned', 'rejected', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        // Apply timeframe filter
        if ($request->has('timeframe')) {
            $date = now();
            switch ($request->timeframe) {
                case 'month':
                    $date = $date->subMonth();
                    break;
                case '3months':
                    $date = $date->subMonths(3);
                    break;
                case '6months':
                    $date = $date->subMonths(6);
                    break;
                case 'year':
                    $date = $date->subYear();
                    break;
            }
            $query->where('created_at', '>=', $date);
        }

        // Apply search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('book', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title':
                $query->join('books', 'checkouts.book_id', '=', 'books.id')
                    ->orderBy('books.title', 'asc')
                    ->select('checkouts.*');
                break;
            case 'author':
                $query->join('books', 'checkouts.book_id', '=', 'books.id')
                    ->orderBy('books.author', 'asc')
                    ->select('checkouts.*');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }

        $checkouts = $query->paginate(15);

        return view('checkouts.history', compact('checkouts'));
    }

    // STUDENT: Request to checkout a book (creates pending request)
    public function checkout(Request $request, Book $book)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to checkout books.');
        }

        // Check if book is available
        if ($book->availability_status !== 'available') {
            return back()->with('error', 'This book is not available for checkout.');
        }

        // Check for existing pending or active checkout for this book by this user
        $existingCheckout = Checkout::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved', 'checked_out'])
            ->first();

        if ($existingCheckout) {
            $statusMessage = match ($existingCheckout->status) {
                'pending' => 'You already have a pending checkout request for this book.',
                'approved' => 'Your checkout request for this book has been approved.',
                'checked_out' => 'You have already checked out this book.',
                default => 'You already have a request for this book.'
            };
            return back()->with('error', $statusMessage);
        }

        // Count only books actually checked out or approved (waiting)
        $userActiveCheckouts = Checkout::where('user_id', Auth::id())
            ->whereIn('status', ['checked_out', 'approved'])  // Only books with user or waiting at desk
            ->count();

        if ($userActiveCheckouts >= 5) {
            $checkedOutCount = Checkout::where('user_id', Auth::id())
                ->where('status', 'checked_out')
                ->count();

            $approvedCount = Checkout::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->count();

            $message = "You have reached the maximum checkout limit of 5 books. ";

            if ($checkedOutCount == 5) {
                $message .= "You currently have 5 books checked out. Please return a book to check out more.";
            } elseif ($approvedCount > 0) {
                $message .= "You have {$checkedOutCount} books checked out and {$approvedCount} approved books waiting. " .
                    "Please check out your approved books or return some books.";
            }

            return back()->with('error', $message);
        }

        try {
            DB::beginTransaction();

            // Create pending checkout request
            $checkout = Checkout::create([
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'checkout_date' => null,
                'due_date' => null,
                'status' => 'pending'
            ]);

            DB::commit();

            return redirect()->route('checkout.success', $checkout)
                ->with('success', 'Checkout request submitted! Waiting for librarian approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit checkout request: ' . $e->getMessage());
        }
    }

    public function checkoutSuccess(Checkout $checkout)
    {
        if ($checkout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('checkouts.success', compact('checkout'));
    }


    // STUDENT: Cancel a pending checkout request
    public function cancel(Request $request, Checkout $checkout)
    {
        // Check if user owns this checkout
        if ($checkout->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Only allow cancellation of pending checkouts
        if ($checkout->status !== 'pending') {
            return redirect()->route('my.checkouts')
                ->with('error', 'Only pending checkout requests can be cancelled.');
        }

        // Update the checkout status
        $checkout->update([
            'status' => 'cancelled'
        ]);

        return redirect()->route('my.checkouts')
            ->with('success', 'Checkout request cancelled successfully.');
    }

    // STUDENT: Delete a checkout request
    public function destroy(Checkout $checkout)
    {
        if ($checkout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow deletion of pending or cancelled checkouts
        if (!in_array($checkout->status, ['pending', 'cancelled', 'rejected'])) {
            return back()->with('error', 'Only pending, cancelled, or rejected checkout requests can be deleted.');
        }

        try {
            $checkout->delete();

            return redirect()->route('my.checkouts')
                ->with('success', 'Checkout request deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Delete checkout failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete checkout request. Please try again.');
        }
    }

    // LIBRARIAN: Approve a checkout request
    public function approveCheckout(Request $request, Checkout $checkout)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($checkout->status !== 'pending') {
            return back()->with('error', 'This checkout request cannot be approved.');
        }

        // Check if book is still available
        if ($checkout->book->availability_status !== 'available') {
            return back()->with('error', 'Book is no longer available.');
        }

        // Check if user is at limit before approving
        $userActiveCheckouts = Checkout::where('user_id', $checkout->user_id)
            ->whereIn('status', ['checked_out', 'approved'])
            ->count();

        if ($userActiveCheckouts >= 5) {
            return back()->with(
                'error',
                'User has reached the maximum checkout limit of 5 books. ' .
                'They must return some books before this one can be approved.'
            );
        }

        try {
            DB::beginTransaction();

            $checkout->update([
                'status' => 'approved',
                'checkout_date' => now(),
                'due_date' => now()->addDays(14) // Set to 14 days as defaults
            ]);

            // Update book availability to checked_out
            $checkout->book->update([
                'availability_status' => 'checked_out'
            ]);

            DB::commit();

            return redirect()->route('librarian.checkouts.pending')
                ->with('success', 'Checkout request approved successfully! Due date: ' . now()->addDays(14)->format('M j, Y'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve checkout failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve checkout request. Please try again.');
        }
    }

    // LIBRARIAN: Reject a checkout request
    public function rejectCheckout(Request $request, Checkout $checkout)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($checkout->status !== 'pending') {
            return back()->with('error', 'This checkout request cannot be rejected.');
        }

        try {
            DB::beginTransaction();

            $checkout->update([
                'status' => 'rejected',
            ]);

            DB::commit();

            return redirect()->route('librarian.checkouts.pending')
                ->with('success', 'Checkout request rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject checkout failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject checkout request. Please try again.');
        }
    }

    // LIBRARIAN: View pending checkout requests
    public function pendingCheckouts()
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $pendingCheckouts = Checkout::with(['book', 'user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('librarian.checkouts.pending', compact('pendingCheckouts'));
    }

    // LIBRARIAN: View pending extension requests
    public function pendingExtensions()
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $pendingExtensions = Checkout::where('extension_requested', true)
            ->where('extension_status', 'pending')
            ->whereNull('return_date') // Only show for active checkouts
            ->with(['book', 'user'])
            ->get();

        return view('librarian.checkouts.pending-extensions', compact('pendingExtensions'));
    }

    // STUDENT: Return a book
    public function returnBook(Checkout $checkout)
    {
        if ($checkout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow return if book was actually checked out
        if ($checkout->status !== 'checked_out') {
            return back()->with('error', 'This book cannot be returned.');
        }

        try {
            DB::beginTransaction();

            $checkout->update([
                'status' => 'returned',
                'return_date' => now()
            ]);

            // Check if there are any waiting reservations for this book
            $waitingReservations = Reservation::where('book_id', $checkout->book_id)
                ->where('status', 'waiting')
                ->exists();

            if ($waitingReservations) {
                // If there are waiting reservations, set status to 'reserved'
                $checkout->book->update([
                    'availability_status' => 'reserved'
                ]);
            } else {
                // If no waiting reservations, set to 'available'
                $checkout->book->update([
                    'availability_status' => 'available'
                ]);
            }

            DB::commit();

            return redirect()->route('my.checkouts')
                ->with('success', 'Book returned successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return book failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to return book. Please try again.');
        }
    }

    // STUDENT: Request extension (requires librarian approval)
    public function requestExtension(Checkout $checkout)
    {
        if ($checkout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the checkout can be extended
        if ($checkout->status !== 'checked_out') {
            return back()->with('error', 'Only checked out books can be extended.');
        }

        if ($checkout->return_date) {
            return back()->with('error', 'Cannot extend a returned book.');
        }

        if ($checkout->extension_requested) {
            return back()->with('error', 'Extension already requested and pending approval.');
        }

        if ($checkout->extension_status === 'approved') {
            return back()->with('error', 'This book has already been extended.');
        }

        // Check if due date is in the future
        if ($checkout->due_date->isPast()) {
            return back()->with('error', 'Cannot extend an overdue book.');
        }

        try {
            $checkout->update([
                'extension_requested' => true,
                'extension_status' => 'pending',
                'extension_requested_at' => now(),
                'extension_days' => 3 // Set default extension days
            ]);

            return redirect()->route('my.checkouts')
                ->with('success', 'Extension request submitted successfully! Waiting for librarian approval.');

        } catch (\Exception $e) {
            Log::error('Extension request failed for checkout ' . $checkout->id . ': ' . $e->getMessage());
            return back()->with('error', 'Failed to submit extension request: ' . $e->getMessage());
        }
    }

    // LIBRARIAN: Approve extension request
    public function approveExtension(Checkout $checkout)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate that this is a pending extension request
            if (!$checkout->extension_requested || $checkout->extension_status !== 'pending') {
                return redirect()->back()
                    ->with('error', 'This extension request has already been processed or is invalid.');
            }

            // Store the original due date for comparison
            $originalDueDate = $checkout->due_date;

            // Calculate new due date
            $extensionDays = $checkout->extension_days ?? 3;
            $newDueDate = Carbon::parse($checkout->due_date)->addDays($extensionDays);

            // Update the checkout
            $checkout->update([
                'due_date' => $newDueDate,
                'extension_requested' => false,
                'extension_status' => 'approved',
                'extended_due_date' => $newDueDate,
                'updated_at' => now()
            ]);

            // Update all pending reservations for this book
            $this->updateReservationDates($checkout);

            // Log the extension approval
            Log::info("Extension approved for checkout {$checkout->id}. Book: {$checkout->book->title}, Original due: {$originalDueDate}, New due: {$newDueDate}");

            return redirect()->route('librarian.checkouts.pending-extensions')
                ->with('success', 'Extension approved successfully. New due date: ' . $newDueDate->format('M j, Y') . '. All reservations for this book have been updated.');

        } catch (\Exception $e) {
            Log::error('Approve extension failed for checkout ' . $checkout->id . ': ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to approve extension: ' . $e->getMessage());
        }
    }

    // Update reservation dates
    private function updateReservationDates(Checkout $checkout)
    {
        try {
            // Get all waiting reservations for this book
            $reservations = Reservation::where('book_id', $checkout->book_id)
                ->where('status', 'waiting')
                ->get();

            $updatedCount = 0;

            foreach ($reservations as $reservation) {
                // Store the old expiry date for notification
                $oldExpiryDate = $reservation->expiry_date;

                // Update to the new extended due date
                $reservation->update([
                    'expiry_date' => $checkout->extended_due_date
                ]);

                // Notify the user about the date change
                $this->notifyReservationDateChange($reservation, $oldExpiryDate, $checkout->extended_due_date);

                $updatedCount++;

                Log::info("Updated reservation {$reservation->id} for user {$reservation->user_id}. Expiry changed from {$oldExpiryDate} to {$checkout->extended_due_date}");
            }

            Log::info("Updated {$updatedCount} reservations for book {$checkout->book_id} after extension approval.");

            return $updatedCount;

        } catch (\Exception $e) {
            Log::error('Failed to update reservation dates for checkout ' . $checkout->id . ': ' . $e->getMessage());
            return 0;
        }
    }

    // Notify users about reservation date changes (*Not worked yet)
    private function notifyReservationDateChange($reservation, $oldDate, $newDate)
    {
        try {
            $user = $reservation->user;
            $bookTitle = $reservation->book->title ?? 'the book';

            // Database notification
            $user->notify(new \App\Notifications\ReservationDateUpdated(
                $reservation,
                $oldDate,
                $newDate
            ));

        } catch (\Exception $e) {
            Log::error('Failed to send notification for reservation date change: ' . $e->getMessage());
        }
    }

    // LIBRARIAN: Reject extension request
    public function rejectExtension(Checkout $checkout)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate that this is a pending extension request
            if (!$checkout->extension_requested || $checkout->extension_status !== 'pending') {
                return redirect()->back()
                    ->with('error', 'This extension request has already been processed or is invalid.');
            }

            $checkout->update([
                'extension_requested' => false,
                'extension_status' => 'rejected',
                'updated_at' => now()
            ]);

            return redirect()->route('librarian.checkouts.pending-extensions')
                ->with('success', 'Extension request rejected.');

        } catch (\Exception $e) {
            Log::error('Reject extension failed for checkout ' . $checkout->id . ': ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reject extension: ' . $e->getMessage());
        }
    }

    // LIBRARIAN: Extend due date directly (without approval process)
    public function extendDueDate(Request $request, Checkout $checkout)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the checkout can be extended
        if ($checkout->status !== 'checked_out') {
            return back()->with('error', 'Only checked out books can be extended.');
        }

        if ($checkout->return_date) {
            return back()->with('error', 'Cannot extend a returned book.');
        }

        if ($checkout->due_date->isPast()) {
            return back()->with('error', 'Cannot extend an overdue book.');
        }

        try {
            DB::beginTransaction();

            // Store the original due date for comparison
            $originalDueDate = $checkout->due_date;
            // Extend 3 day
            $extensionDays = 3;
            $newDueDate = Carbon::parse($checkout->due_date)->addDays($extensionDays);

            // Update the checkout
            $checkout->update([
                'due_date' => $newDueDate,
                'extended_due_date' => $newDueDate,
                'extension_requested' => false,
                'extension_status' => 'approved',
                'updated_at' => now()
            ]);

            // Update all pending reservations for this book
            $this->updateReservationDates($checkout);

            DB::commit();

            // Optional: Log the extension
            Log::info("Librarian extended checkout {$checkout->id}. Book: {$checkout->book->title}, Original due: {$originalDueDate}, New due: {$newDueDate}");

            return redirect()->back()
                ->with('success', 'Loan extended successfully! New due date: ' . $newDueDate->format('M j, Y'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Extend due date failed for checkout ' . $checkout->id . ': ' . $e->getMessage());
            return back()->with('error', 'Failed to extend loan: ' . $e->getMessage());
        }
    }
    // LIBRARIAN: View all checkouts
    public function index(Request $request)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $query = Checkout::with(['book', 'user'])
            ->orderBy('created_at', 'desc');

        // Get per_page from request or use default
        $perPage = $request->get('per_page', 20);
        $checkouts = $query->paginate($perPage)->withQueryString();

        return view('librarian.checkouts.index', compact('checkouts'));
    }

    // LIBRARIAN: Create checkout directly (bypasses approval)
    public function store(Request $request)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
        ]);

        try {
            DB::beginTransaction();

            $book = Book::findOrFail($request->book_id);

            if ($book->availability_status !== 'available') {
                return back()->with('error', 'This book is not available for checkout.');
            }

            // Check if user already has this book checked out
            $existingCheckout = Checkout::where('user_id', $request->user_id)
                ->where('book_id', $request->book_id)
                ->whereIn('status', ['pending', 'approved', 'checked_out'])
                ->first();

            if ($existingCheckout) {
                return back()->with('error', 'This user already has this book checked out or pending.');
            }

            // Count only checked_out or approved books
            $userActiveCheckouts = Checkout::where('user_id', $request->user_id)
                ->whereIn('status', ['checked_out', 'approved'])
                ->count();

            if ($userActiveCheckouts >= 5) {
                return back()->with(
                    'error',
                    'This user has reached the maximum checkout limit of 5 books. ' .
                    'They must return some books first.'
                );
            }

            $checkout = Checkout::create([
                'user_id' => $request->user_id,
                'book_id' => $request->book_id,
                'checkout_date' => now(),
                'due_date' => now()->addDays(14),
                'status' => 'checked_out'
            ]);

            $book->update([
                'availability_status' => 'checked_out'
            ]);

            DB::commit();

            return redirect()->route('librarian.checkouts.index')
                ->with('success', 'Book checked out successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Librarian checkout failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to checkout book. Please try again.');
        }
    }

    // LIBRARIAN: Force return a book 
    public function librarianReturnBook(Checkout $checkout)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $checkout->update([
                'status' => 'returned',
                'return_date' => now()
            ]);

            // Check if there are any waiting reservations for this book
            $waitingReservations = Reservation::where('book_id', $checkout->book_id)
                ->where('status', 'waiting')
                ->exists();

            if ($waitingReservations) {
                // If there are waiting reservations, set status to 'reserved'
                $checkout->book->update([
                    'availability_status' => 'reserved'
                ]);
            } else {
                // If no waiting reservations, set to 'available'
                $checkout->book->update([
                    'availability_status' => 'available'
                ]);
            }

            DB::commit();

            return redirect()->route('librarian.checkouts.index')
                ->with('success', 'Book returned successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Librarian return book failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to return book. Please try again.');
        }
    }

    // LIBRARIAN: Show form to create checkout
    public function create()
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $books = Book::where('availability_status', 'available')->get();
        $users = User::where('role', 'student')->get();

        return view('librarian.checkouts.create', compact('books', 'users'));
    }

    // LIBRARIAN: Show individual checkout
    public function show(Checkout $checkout)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        return view('librarian.checkouts.show', compact('checkout'));
    }

    // LIBRARIAN: Get user's checkouts
    public function getUserCheckouts(User $user)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $checkouts = Checkout::with('book')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('librarian.checkouts.user-checkouts', compact('checkouts', 'user'));
    }

    // LIBRARIAN: Update checkout status (general status update)
    public function updateStatus(Request $request, Checkout $checkout)
    {
        if (!in_array(Auth::user()->role, ['librarian', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:pending,approved,checked_out,returned,rejected,cancelled'
        ]);

        $oldStatus = $checkout->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Checkout status is already set to ' . ucfirst(str_replace('_', ' ', $newStatus)));
        }

        // REAL LIBRARY LOGIC: Prevent exceeding 5-book limit
        if (in_array($newStatus, ['approved', 'checked_out'])) {

            // Count user's checked_out + approved books
            $activeCheckouts = Checkout::where('user_id', $checkout->user_id)
                ->whereIn('status', ['checked_out', 'approved'])
                ->count();

            // If the current checkout is already counted (approved or checked_out), subtract 1
            if (in_array($oldStatus, ['approved', 'checked_out'])) {
                $activeCheckouts--;
            }

            if ($activeCheckouts >= 5) {
                return back()->with(
                    'error',
                    'User has reached the maximum limit of 5 checked out/approved books. ' .
                    'They must return some books first.'
                );
            }
        }

        // Prevent invalid status transitions (real library rules)
        $invalidTransitions = [
            'returned' => ['approved', 'checked_out'],  // Can't re-checkout returned book
            'rejected' => ['approved', 'checked_out'],  // Can't approve rejected request
            'cancelled' => ['approved', 'checked_out'], // Can't approve cancelled request
        ];

        if (isset($invalidTransitions[$oldStatus]) && in_array($newStatus, $invalidTransitions[$oldStatus])) {
            return back()->with(
                'error',
                "Cannot change status from '{$oldStatus}' to '{$newStatus}'. " .
                "Create a new checkout request instead."
            );
        }

        try {
            DB::beginTransaction();

            $checkout->status = $newStatus;

            // Set dates based on status changes
            if ($newStatus === 'approved' && $oldStatus === 'pending') {
                $checkout->due_date = now()->addDays(14);
                $checkout->checkout_date = now();
                $checkout->book->update(['availability_status' => 'checked_out']);

            } elseif ($newStatus === 'checked_out' && $oldStatus === 'approved') {
                $checkout->checkout_date = now();

            } elseif ($newStatus === 'returned') {
                $checkout->return_date = now();

                // Check if there are waiting reservations
                $waitingReservations = Reservation::where('book_id', $checkout->book_id)
                    ->where('status', 'waiting')
                    ->exists();

                if ($waitingReservations) {
                    $checkout->book->update(['availability_status' => 'reserved']);
                } else {
                    $checkout->book->update(['availability_status' => 'available']);
                }

            } elseif ($newStatus === 'rejected') {
                $checkout->checkout_date = null;
                $checkout->due_date = null;
                // Book remains available for rejected requests
                if ($checkout->book->availability_status === 'checked_out') {
                    $checkout->book->update(['availability_status' => 'available']);
                }
            } elseif ($newStatus === 'pending') {
                $checkout->checkout_date = null;
                $checkout->due_date = null;
                $checkout->return_date = null;
                // If book was checked_out, make it available
                if ($checkout->book->availability_status === 'checked_out') {
                    $checkout->book->update(['availability_status' => 'available']);
                }
            } elseif ($newStatus === 'cancelled') {
                $checkout->checkout_date = null;
                $checkout->due_date = null;
                // If book was checked_out, make it available
                if ($checkout->book->availability_status === 'checked_out') {
                    $checkout->book->update(['availability_status' => 'available']);
                }
            }

            $checkout->save();

            DB::commit();

            if ($newStatus === 'pending') {
                return redirect()->route('librarian.checkouts.pending')
                    ->with('success', "Checkout reverted to pending status. Due date has been cleared.");
            } else {
                return redirect()->route('librarian.checkouts.index')
                    ->with('success', "Checkout status updated from " . ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . ucfirst(str_replace('_', ' ', $newStatus)));
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update checkout status failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update checkout status. Please try again.');
        }
    }
}