<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Checkout extends Model
{
    protected $fillable = [
        'checkout_date',
        'due_date',
        'return_date',
        'status',
        'extension_requested',
        'extension_requested_at',
        'extension_days',
        'extended_due_date',
        'extension_status',
        'user_id',
        'book_id',
        'reservation_id'
    ];

    protected $casts = [
        'checkout_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'extension_requested_at' => 'datetime',
        'extended_due_date' => 'date',
        'extension_requested' => 'boolean',
    ];
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
    // Check Unpaid Fine
    public function hasUnpaidFine()
    {
        return $this->fines()->where('status', 'unpaid')->exists();
    }

    // Check if checkout is overdue
    public function isOverdue()
    {
        if (!$this->due_date || $this->status !== 'checked_out') {
            return false;
        }
        return $this->due_date->isPast();
    }

    // Calculate days overdue
    public function daysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return $this->due_date->diffInDays(now());
    }

    // Calculate total fine amount
    public function totalFines()
    {
        return $this->fines->sum('amount');
    }

    // Check if can request extension
    public function canRequestExtension()
    {
        return $this->status === 'checked_out' &&
            !$this->extension_requested && // No pending request
            !$this->return_date && // Not returned
            !$this->isOverdue() && // Not overdue
            $this->due_date->diffInDays(now(), false) > 1; // More than 1 day remaining
    }

    // Check if has pending extension request
    public function hasPendingExtension()
    {
        return $this->extension_requested &&
            $this->extension_status === 'pending' &&
            !$this->return_date;
    }

    // Check if extension was approved
    public function isExtended()
    {
        return $this->extension_status === 'approved' &&
            $this->extended_due_date !== null;
    }

    // Approve extension
    public function approveExtension($days = 3)
    {
        $newDueDate = $this->due_date->copy()->addDays($days);

        $this->update([
            'extension_requested' => false,
            'extension_status' => 'approved',
            'extension_days' => $days,
            'extended_due_date' => $newDueDate,
            'due_date' => $newDueDate,
        ]);

        return $newDueDate;
    }

    // Reject extension
    public function rejectExtension()
    {
        $this->update([
            'extension_requested' => false,
            'extension_status' => 'rejected',
            'extension_requested_at' => null,
        ]);
    }

    // Request extension
    public function requestExtension($days = 3)
    {
        if (!$this->canRequestExtension()) {
            throw new \Exception('Cannot request extension for this checkout.');
        }

        $this->update([
            'extension_requested' => true,
            'extension_status' => 'pending',
            'extension_days' => $days,
            'extension_requested_at' => now(),
        ]);
    }
    // SHow extension button 
    public function canShowExtendButton(): bool
    {
        return $this->canRequestExtension()
            && !$this->hasPendingExtension()
            && !$this->isExtended();
    }

    // Scope for active checkouts
    public function scopeActive($query)
    {
        return $query->where('status', 'checked_out')
            ->whereNull('return_date');
    }

    // Scope for overdue checkouts
    public function scopeOverdue($query)
    {
        return $query->where('status', 'checked_out')
            ->whereNull('return_date')
            ->where('due_date', '<', now());
    }

    // Scope for pending extension requests
    public function scopePendingExtensions($query)
    {
        return $query->where('extension_requested', true)
            ->where('extension_status', 'pending')
            ->whereNull('return_date');
    }

    // Scope for approved extensions
    public function scopeApprovedExtensions($query)
    {
        return $query->where('extension_status', 'approved');
    }

    // Get checkout status with badge class
    public function getStatusBadgeAttribute()
    {
        if ($this->return_date) {
            return 'bg-success';
        } elseif ($this->isOverdue()) {
            return 'bg-danger';
        } elseif ($this->status === 'checked_out') {
            return 'bg-primary';
        } else {
            return 'bg-secondary';
        }
    }

    // Get status text
    public function getStatusTextAttribute()
    {
        if ($this->return_date) {
            return 'Returned';
        } elseif ($this->isOverdue()) {
            return 'Overdue';
        } elseif ($this->status === 'checked_out') {
            return 'Checked Out';
        } else {
            return $this->status;
        }
    }

    // Get extension status text
    public function getExtensionStatusTextAttribute()
    {
        if ($this->extension_status === 'approved') {
            return 'Approved';
        } elseif ($this->extension_status === 'rejected') {
            return 'Rejected';
        } elseif ($this->extension_requested) {
            return 'Pending';
        } else {
            return 'None';
        }
    }

    // Get extension status badge class
    public function getExtensionStatusBadgeAttribute()
    {
        $classes = [
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'pending' => 'bg-warning',
            'none' => 'bg-secondary'
        ];

        $status = $this->extension_status_text;
        return $classes[strtolower($status)] ?? 'bg-secondary';
    }

    // Carbon accessors
    public function getDueDateCarbonAttribute()
    {
        return $this->due_date ? Carbon::parse($this->due_date) : null;
    }

    public function getCheckoutDateCarbonAttribute()
    {
        return $this->checkout_date ? Carbon::parse($this->checkout_date) : null;
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->due_date) {
            return null;
        }
        return now()->startOfDay()->diffInDays($this->due_date_carbon->startOfDay(), false);
    }

    public function getIsExtendedAttribute()
    {
        return $this->extension_status === 'approved' && $this->extended_due_date !== null;
    }

    public function getOriginalDueDateAttribute()
    {
        if (!$this->checkout_date) {
            return null;
        }

        $originalDueDate = $this->checkout_date_carbon->copy()->addDays(14);

        // If extended, show what the original due date was before extension
        if ($this->is_extended && $this->extended_due_date) {
            return $originalDueDate;
        }

        return $originalDueDate;
    }

    // Get the actual due date (considers extensions)
    public function getActualDueDateAttribute()
    {
        if ($this->is_extended && $this->extended_due_date) {
            return $this->extended_due_date;
        }

        return $this->due_date;
    }

    // Validation rules 
    public static function validationRules($forCreate = true)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'checkout_date' => 'required|date',
            'due_date' => 'required|date|after:checkout_date',
            'status' => 'required|in:pending,approved,checked_out,returned,rejected,overdue,cancelled',
        ];

        if (!$forCreate) {
            $rules['return_date'] = 'nullable|date|after:checkout_date';
        }

        return $rules;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($checkout) {
            // Set extension_requested_at when extension is requested
            if ($checkout->extension_requested && !$checkout->extension_requested_at) {
                $checkout->extension_requested_at = now();
            }

            // Auto-set status based on dates
            if ($checkout->return_date) {
                $checkout->status = 'returned';
            } elseif ($checkout->due_date && $checkout->due_date->isPast() && $checkout->status === 'checked_out') {
                $checkout->status = 'overdue';
            }
        });
    }
    public function wasReturnedOverdue()
    {
        if (!$this->return_date || !$this->due_date) {
            return false;
        }

        return $this->return_date->gt($this->due_date);
    }

    public function daysReturnedOverdue()
    {
        if (!$this->wasReturnedOverdue()) {
            return 0;
        }

        // Get the dates without time components (start of day)
        $dueDate = $this->due_date->copy()->startOfDay();
        $returnDate = $this->return_date->copy()->startOfDay();

        // Calculate difference in days (exclusive of due date)
        $daysOverdue = $dueDate->diffInDays($returnDate, false);

        // If return is on or before due date, not overdue
        if ($daysOverdue <= 0) {
            return 0;
        }

        return $daysOverdue;
    }
}