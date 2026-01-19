<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'fine_date',
        'amount',
        'status',
        'reason',
        'paid_date',
        'user_id',
        'checkout_id'
    ];

    protected $casts = [
        'fine_date' => 'date',
        'paid_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID = 'paid';
    const STATUS_WAIVED = 'waived';
    const REASON_OVERDUE = 'Overdue Book';
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkout()
    {
        return $this->belongsTo(Checkout::class);
    }

    // Simple status check
    public function isUnpaid()
    {
        return $this->status === self::STATUS_UNPAID;
    }

    // Simple status change
    public function markAsPaid()
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_date' => Carbon::now()
        ]);
    }

    public function waive()
    {
        $this->update([
            'status' => self::STATUS_WAIVED,
            'paid_date' => null
        ]);
    }

    // In App\Models\Fine.php - FIX THE calculateOverdueFine() method
    public static function calculateOverdueFine(Checkout $checkout)
    {
        if (!$checkout->due_date || $checkout->return_date) {
            return 0;
        }

        // Make sure we have Carbon dates
        $dueDate = Carbon::parse($checkout->due_date)->startOfDay();
        $today = Carbon::today()->startOfDay();

        \Log::info("=== FINE CALCULATION DEBUG ===");
        \Log::info("Checkout ID: {$checkout->id}");
        \Log::info("Due date: {$dueDate->format('Y-m-d')}");
        \Log::info("Today: {$today->format('Y-m-d')}");

        // Check if due date is in the past (overdue)
        if ($today->lte($dueDate)) {
            \Log::info("NOT OVERDUE: Today is on or before due date");
            return 0;
        }

        // CORRECT CALCULATION: Get days overdue
        // When today is AFTER due date, this gives positive number of days
        $daysOverdue = $dueDate->diffInDays($today); // Changed this line!

        \Log::info("Days overdue: {$daysOverdue}");

        if ($daysOverdue <= 0) {
            \Log::info("No overdue days calculated");
            return 0;
        }

        // $0.50 per day
        $fineAmount = $daysOverdue * 0.50;

        // Optional: Set maximum fine
        $maxFine = 10.00;
        $fineAmount = min($fineAmount, $maxFine);

        \Log::info("Calculated fine: $" . round($fineAmount, 2));
        \Log::info("=== END CALCULATION ===");

        return round($fineAmount, 2);
    }

    // FIXED: Create fine with $0.50 per day calculation
    public static function createOverdueFine(Checkout $checkout)
    {
        // Check if there's already an unpaid fine for this checkout
        $existingFine = self::where('checkout_id', $checkout->id)
            ->where('status', self::STATUS_UNPAID)
            ->first();

        if ($existingFine) {
            return null; // Already has an unpaid fine
        }

        $amount = self::calculateOverdueFine($checkout);

        if ($amount > 0) {
            // Calculate overdue days for the reason
            $overdueDays = Carbon::now()->startOfDay()->diffInDays(
                $checkout->due_date->startOfDay()
            );
            $daysOverdue = abs($overdueDays);

            return self::create([
                'user_id' => $checkout->user_id,
                'checkout_id' => $checkout->id,
                'fine_date' => Carbon::today(),
                'amount' => $amount,
                'reason' => self::REASON_OVERDUE,
                'status' => self::STATUS_UNPAID
            ]);
        }
        return null;
    }

    // Get overdue days for an existing fine
    public function getOverdueDaysAttribute()
    {
        if ($this->checkout && $this->checkout->due_date) {
            $overdueDays = Carbon::now()->startOfDay()->diffInDays(
                $this->checkout->due_date->startOfDay()
            );

            if ($overdueDays < 0) {
                return abs($overdueDays);
            }
        }
        return 0;
    }
}