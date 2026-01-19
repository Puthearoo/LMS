<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\LibrarianController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

// Public Welcome Page - No authentication required (this should be first)
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// About page route
Route::get('/about', function () {
    // Try to get real statistics if models exist
    try {
        $totalBooks = \App\Models\Book::count();
        $totalCategories = \App\Models\Book::distinct('category')->count('category');
        $activeMembers = \App\Models\User::where('role', 'student')->count();
    } catch (\Exception $e) {
        // Use default values if models don't exist yet
        $totalBooks = '10,000+';
        $totalCategories = '50+';
        $activeMembers = '5,000+';
    }

    return view('about', [
        'totalBooks' => $totalBooks,
        'activeMembers' => $activeMembers,
        'monthlyLoans' => '500+',
        'totalCategories' => $totalCategories,
    ]);
})->name('about');
// Public book browsing routes
Route::get('/category/{category}', [WelcomeController::class, 'category'])->name('welcome.category');
Route::get('/search', [WelcomeController::class, 'search'])->name('welcome.search');

// Checkout Routes for Students (outside librarian prefix)
Route::middleware(['auth'])->group(function () {
    Route::post('/books/{book}/checkout', [CheckoutController::class, 'checkout'])->name('books.checkout');
    Route::get('/checkout/success/{checkout}', [CheckoutController::class, 'checkoutSuccess'])->name('checkout.success');
    Route::get('/my-checkouts', [CheckoutController::class, 'myCheckouts'])->name('my.checkouts');
    Route::post('/checkouts/{checkout}/return', [CheckoutController::class, 'returnBook'])->name('checkouts.return');
    Route::post('/checkouts/{checkout}/extend', [CheckoutController::class, 'extendDueDate'])->name('checkouts.extend');
    Route::post('/checkouts/{checkout}/request-extension', [CheckoutController::class, 'requestExtension'])->name('checkouts.request-extension');
    Route::get('/borrowing-history', [CheckoutController::class, 'borrowingHistory'])->name('history');

    // Student cancel route
    Route::post('/checkouts/{checkout}/cancel', [CheckoutController::class, 'cancel'])->name('checkouts.cancel');
    Route::delete('/checkouts/{checkout}', [CheckoutController::class, 'destroy'])->name('checkouts.destroy');
});

// User Reservation Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/my-reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/books/{book}/reserve', [ReservationController::class, 'store'])->name('books.reserve');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});


// Librarian Routes Group with nested dashboard
Route::prefix('librarian')->name('librarian.')->middleware(['auth', 'role:librarian'])->group(function () {
    // Librarian Main Dashboard
    Route::get('/dashboard', [LibrarianController::class, 'dashboard'])->name('dashboard');

    // Books Management under librarian dashboard
    Route::prefix('dashboard/books')->name('books.')->group(function () {
        Route::get('/', [BookController::class, 'index'])->name('index');
        Route::get('/create', [BookController::class, 'create'])->name('create');
        Route::post('/', [BookController::class, 'store'])->name('store');
        Route::get('/{book}', [BookController::class, 'show'])->name('show');
        Route::get('/{book}/edit', [BookController::class, 'edit'])->name('edit');
        Route::put('/{book}', [BookController::class, 'update'])->name('update');
        Route::delete('/{book}', [BookController::class, 'destroy'])->name('destroy');

        // Librarian-specific checkout/reserve actions
        Route::post('/{book}/checkout', [BookController::class, 'librarianCheckout'])->name('librarian.checkout');
        Route::post('/{book}/reserve', [BookController::class, 'reserve'])->name('reserve');
        Route::get('/{book}/availability', [BookController::class, 'checkAvailability'])->name('availability');
    });

    // Checkouts Management under librarian dashboard
    Route::prefix('dashboard/checkouts')->name('checkouts.')->group(function () {
        // PUT PENDING FIRST - before any {checkout} parameter routes
        Route::get('/pending', [CheckoutController::class, 'pendingCheckouts'])->name('pending');

        // Extension routes
        Route::get('/pending-extensions', [CheckoutController::class, 'pendingExtensions'])->name('pending-extensions');
        Route::post('/{checkout}/approve-extension', [CheckoutController::class, 'approveExtension'])->name('approve-extension');
        Route::post('/{checkout}/reject-extension', [CheckoutController::class, 'rejectExtension'])->name('reject-extension');

        // Then the rest of your routes
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::get('/create', [CheckoutController::class, 'create'])->name('create');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::get('/{checkout}', [CheckoutController::class, 'show'])->name('show');
        Route::get('/{checkout}/edit', [CheckoutController::class, 'edit'])->name('edit');
        Route::put('/{checkout}', [CheckoutController::class, 'update'])->name('update');

        // Status update routes
        Route::put('/{checkout}/update-status', [CheckoutController::class, 'updateStatus'])->name('update-status');
        Route::post('/{checkout}/extend', [CheckoutController::class, 'extendDueDate'])->name('extend');

        Route::delete('/{checkout}', [CheckoutController::class, 'destroy'])->name('destroy');
        Route::post('/{checkout}/return', [CheckoutController::class, 'librarianReturnBook'])->name('return');
        Route::get('/user/{user}', [CheckoutController::class, 'getUserCheckouts'])->name('user');

        // Approval routes
        Route::post('/{checkout}/approve', [CheckoutController::class, 'approveCheckout'])->name('approve');
        Route::post('/{checkout}/reject', [CheckoutController::class, 'rejectCheckout'])->name('reject');
        Route::post('/{checkout}/mark-returned', [CheckoutController::class, 'markAsReturned'])->name('mark-returned');

        // Librarian cancel checkout route (different from student cancel)
        Route::post('/{checkout}/cancel', [CheckoutController::class, 'librarianCancel'])->name('cancel');
    });

    // FIXED: Reservations Management under librarian dashboard
    Route::prefix('dashboard/reservations')->name('reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'librarianIndex'])->name('index');
        Route::get('/{reservation}', [ReservationController::class, 'showForLibrarian'])->name('show');

        Route::post('/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('confirm');
        Route::post('/{reservation}/mark-picked-up', [ReservationController::class, 'markAsPickedUp'])->name('markAsPickedUp');
        Route::post('/{reservation}/expire', [ReservationController::class, 'expire'])->name('expire');
        Route::patch('/{reservation}/cancel', [ReservationController::class, 'librarianCancel'])->name('cancel');
        Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('destroy');
    });

    // FIXED: Fines Management under librarian dashboard
    Route::prefix('dashboard/fines')->name('fines.')->group(function () {
        Route::get('/', [FineController::class, 'index'])->name('index');
        Route::get('/create', [FineController::class, 'create'])->name('create');
        Route::post('/', [FineController::class, 'store'])->name('store');

        // User fines route
        Route::get('/user/{user}', [FineController::class, 'getUserFines'])->name('user');

        // Statistics route
        Route::get('/statistics', [FineController::class, 'statistics'])->name('statistics');

        // Action routes (must come BEFORE {fine} routes)
        Route::post('/generate-overdue', [FineController::class, 'generateOverdueFines'])
            ->name('generate-overdue');
        Route::post('/recalculate', [FineController::class, 'recalculateFines'])
            ->name('recalculate');

        // NOW the {fine} parameter routes (must come LAST)
        Route::get('/{fine}', [FineController::class, 'show'])->name('show');
        Route::get('/{fine}/edit', [FineController::class, 'edit'])->name('edit');
        Route::put('/{fine}', [FineController::class, 'update'])->name('update');
        Route::delete('/{fine}', [FineController::class, 'destroy'])->name('destroy');
        Route::post('/{fine}/pay', [FineController::class, 'payFine'])->name('pay');
        Route::post('/{fine}/mark-paid', [FineController::class, 'markAsPaid'])
            ->name('mark-paid');
        Route::post('/{fine}/waive', [FineController::class, 'waive'])
            ->name('waive');
    });

    // Users Management under librarian dashboard
    Route::prefix('dashboard/users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });


});

// Cleanup route (outside the groups)
Route::get('/librarian/dashboard/reservations/cleanup-duplicates', [ReservationController::class, 'cleanupDuplicatePickedUp'])
    ->middleware(['auth', 'role:librarian,admin'])
    ->name('librarian.reservations.cleanup');

// Default dashboard based on role (for authenticated users)
Route::get('/dashboard', function () {
    $user = auth()->user();

    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'librarian':
            return redirect()->route('librarian.dashboard');
        case 'student':
            return redirect()->route('home');
        default:
            return redirect()->route('home');
    }
})->name('dashboard')->middleware('auth');
