<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;
    public function checkout()
    {
        return $this->hasOne(Checkout::class);
    }

    protected $fillable = [
        'reservation_date',
        'expiry_date',
        'status',
        'user_id',
        'book_id'
    ];

    protected $casts = [
        'reservation_date' => 'datetime',
        'expiry_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Scopes for different statuses - ADD THE PENDING SCOPE
    public function scopePending($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    // Get all active reservations (waiting or ready)
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'ready'])
            ->where(function ($q) {
                $q->where('expiry_date', '>=', Carbon::today())
                    ->orWhereNull('expiry_date');
            });
    }

    // Get expired reservations
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
                ->orWhere(function ($q2) {
                    $q2->whereIn('status', ['waiting', 'ready'])
                        ->where('expiry_date', '<', Carbon::today());
                });
        });
    }

    // Status check methods
    public function isWaiting()
    {
        return $this->status === 'waiting';
    }

    public function isReady()
    {
        return $this->status === 'ready';
    }

    public function isPickedUp()
    {
        return $this->status === 'picked_up';
    }

    public function isCancelled()
    {
        return in_array($this->status, ['cancelled_by_user', 'cancelled_by_librarian']);
    }


    // Get who cancelled
    public function getCancelledBy()
    {
        if ($this->status === 'cancelled_by_user') {
            return 'Reserver';
        } elseif ($this->status === 'cancelled_by_librarian') {
            return 'Librarian';
        }
        return null;
    }
    public function getCancelledByUser()
    {
        if ($this->isCancelled()) {
            $userId = str_replace('cancelled_by_', '', $this->status);
            return User::find($userId);
        }
        return null;
    }
    public function isExpired()
    {
        if ($this->status === 'expired') {
            return true;
        }

        if (in_array($this->status, ['waiting', 'ready'])) {
            if ($this->expiry_date && $this->expiry_date < Carbon::today()) {
                return true;
            }
        }

        return false;
    }

    public function isActive()
    {
        if (!in_array($this->status, ['waiting', 'ready'])) {
            return false;
        }

        if (!$this->expiry_date) {
            return $this->isWaiting();
        }

        return $this->expiry_date >= Carbon::today();
    }

    // Check if the reservation can be cancelled
    public function canBeCancelled()
    {
        return $this->status === 'waiting' && !$this->isExpired();
    }

    // Get the current checkout for this book
    public function getCurrentCheckout()
    {
        return Checkout::where('book_id', $this->book_id)
            ->whereNull('return_date')
            ->whereIn('status', ['approved', 'borrowed'])
            ->orderBy('created_at', 'desc')
            ->first();
    }
    // In your Reservation model
    public function canBeMarkedAsReady()
    {
        // Check if book is available OR reserved (but maybe the reservation expired)
        if ($this->book->availability_status === 'available') {
            return true;
        }

        // If book is reserved, check if there's an active ready reservation
        if ($this->book->availability_status === 'reserved') {
            $activeReadyReservation = Reservation::where('book_id', $this->book_id)
                ->where('status', 'ready')
                ->where('expiry_date', '>=', now())
                ->exists();

            // If no active ready reservation, book is actually available
            return !$activeReadyReservation;
        }

        return false;
    }
    /**
     * Get the expected due date from the current checkout
     */
    public function getExpectedDueDate()
    {
        $checkout = $this->getCurrentCheckout();

        if ($checkout) {
            if ($checkout->extension_status === 'approved' && $checkout->extended_due_date) {
                return $checkout->extended_due_date;
            }

            return $checkout->due_date;
        }

        return null;
    }

    /**
     * Get days left until the book is due to be returned
     */
    public function getDaysLeftUntilReturn()
    {
        $dueDate = $this->getExpectedDueDate();

        if ($dueDate) {
            $dueDate = Carbon::parse($dueDate);
            $now = Carbon::now();
            return $now->diffInDays($dueDate, false);
        }

        return null;
    }

    /**
     * Get formatted days left message
     */
    public function getDaysLeftMessage()
    {
        $daysLeft = $this->getDaysLeftUntilReturn();

        if ($daysLeft === null) {
            return 'Date not available';
        }

        if ($daysLeft > 0) {
            return "Due in {$daysLeft} " . ($daysLeft == 1 ? 'day' : 'days');
        } elseif ($daysLeft == 0) {
            return 'Due today';
        } else {
            $overdueDays = abs($daysLeft);
            return "Overdue by {$overdueDays} " . ($overdueDays == 1 ? 'day' : 'days');
        }
    }

    /**
     * Format the expected due date for display
     */
    public function getFormattedExpectedDueDate()
    {
        $dueDate = $this->getExpectedDueDate();

        if ($dueDate) {
            return Carbon::parse($dueDate)->format('M d, Y');
        }

        return 'Not available';
    }

    /**
     * Get the position in the queue for this reservation
     */
    public function getQueuePosition()
    {
        return self::where('book_id', $this->book_id)
            ->where('status', 'waiting')
            ->where('created_at', '<', $this->created_at)
            ->count() + 1;
    }

    /**
     * Get the pickup deadline message based on expiry date
     */
    public function getPickupDeadlineMessage()
    {
        if (!$this->expiry_date) {
            return 'No pickup deadline set';
        }

        $daysLeft = Carbon::today()->diffInDays($this->expiry_date, false);

        if ($daysLeft > 0) {
            return 'Pick up within ' . $daysLeft . ' days';
        } elseif ($daysLeft == 0) {
            return 'Must pick up TODAY!';
        } else {
            return 'Expired ' . abs($daysLeft) . ' days ago';
        }
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiry()
    {
        if (!$this->expiry_date) {
            return null;
        }

        return Carbon::today()->diffInDays($this->expiry_date, false);
    }

    /**
     * Get the reason for expiry date (explains why this date was chosen)
     */
    public function getExpiryDateReason()
    {
        if (!$this->expiry_date || !$this->isReady()) {
            return null;
        }

        $currentCheckout = $this->getCurrentCheckout();
        if (!$currentCheckout) {
            return '';
        }

        // Get due date (considering extensions)
        $dueDate = Carbon::parse(
            $currentCheckout->extension_status === 'approved' && $currentCheckout->extended_due_date
            ? $currentCheckout->extended_due_date
            : $currentCheckout->due_date
        );

        $daysDifference = $this->expiry_date->diffInDays($dueDate, false);

        if ($daysDifference == 0) {
            return 'Pickup deadline matches book due date';
        } elseif ($daysDifference < 0) {
            return 'Pickup deadline is ' . abs($daysDifference) . ' days before due date';
        } else {
            return 'Pickup deadline is after due date';
        }
    }

    /**
     * Check if pickup is urgent (expiring today or tomorrow)
     */
    public function isPickupUrgent()
    {
        if (!$this->expiry_date || !$this->isReady()) {
            return false;
        }

        $daysLeft = $this->getDaysUntilExpiry();
        return $daysLeft !== null && $daysLeft <= 1;
    }
}