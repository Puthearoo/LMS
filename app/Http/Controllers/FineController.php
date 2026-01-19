<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Http\Request;

class FineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Fine::with(['user', 'checkout.book'])->latest();

        // Status filter (only status now, no reason filter)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // User search
        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%')
                    ->orWhere('email', 'like', '%' . $request->user . '%');
            });
        }

        $fines = $query->paginate(20);

        // Statistics for sidebar
        $unpaidFinesCount = Fine::where('status', 'unpaid')->count();
        $paidFinesCount = Fine::where('status', 'paid')->count();
        $waivedCount = Fine::where('status', 'waived')->count();

        // Dashboard stats
        $totalUnpaid = Fine::where('status', 'unpaid')->sum('amount');
        $totalPaid = Fine::where('status', 'paid')->sum('amount');
        $totalWaived = Fine::where('status', 'waived')->sum('amount');

        return view('librarian.fines.index', compact(
            'fines',
            'unpaidFinesCount',
            'paidFinesCount',
            'totalUnpaid',
            'totalPaid',
            'totalWaived',
            'waivedCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('librarian.fines.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
        ]);

        Fine::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'fine_date' => now(),
            'status' => 'unpaid',
        ]);

        return redirect()->route('librarian.fines.index')
            ->with('success', 'Fine created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fine $fine)
    {
        $fine->load(['user', 'checkout.book']);
        return view('librarian.fines.show', compact('fine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fine $fine)
    {
        return view('librarian.fines.edit', compact('fine'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fine $fine)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:unpaid,paid,waived',
        ]);

        $updateData = [
            'amount' => $request->amount,
            'status' => $request->status,
        ];

        // Only set paid_date if status is changing to paid
        if ($request->status == 'paid' && $fine->status != 'paid') {
            $updateData['paid_date'] = now();
        } elseif ($request->status != 'paid') {
            $updateData['paid_date'] = null;
        }

        $fine->update($updateData);

        return redirect()->route('librarian.fines.index')
            ->with('success', 'Fine updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fine $fine)
    {
        $fine->delete();
        return redirect()->route('librarian.fines.index')
            ->with('success', 'Fine deleted successfully.');
    }

    // Custom Methods
    public function markAsPaid(Fine $fine)
    {
        $fine->markAsPaid();
        return back()->with('success', 'Fine marked as paid successfully.');
    }

    public function waive(Fine $fine)
    {
        $fine->waive();
        return back()->with('success', 'Fine waived successfully.');
    }

    public function generateOverdueFines()
    {
        \Log::info('=== STARTING OVERDUE FINE GENERATION ===');

        // Get checkouts that are overdue and not returned
        $checkouts = Checkout::where('due_date', '<', now())
            ->whereNull('return_date')
            ->where('status', 'checked_out')
            ->with(['user', 'book'])
            ->get();

        \Log::info('Found ' . $checkouts->count() . ' overdue checkouts');

        $created = 0;
        $totalAmount = 0;

        foreach ($checkouts as $checkout) {
            \Log::info("Processing checkout ID: {$checkout->id}");

            // Skip if already has unpaid fine
            if ($checkout->hasUnpaidFine()) {
                \Log::info("Checkout {$checkout->id} already has unpaid fine, skipping");
                continue;
            }

            // Calculate fine using model method
            $fineAmount = Fine::calculateOverdueFine($checkout);

            if ($fineAmount > 0) {
                \Log::info("Creating fine: $" . $fineAmount);

                // Create the fine with simple reason - FIX THIS LINE
                $fine = Fine::create([
                    'user_id' => $checkout->user_id,
                    'checkout_id' => $checkout->id,
                    'amount' => $fineAmount,
                    'fine_date' => now(),
                    'status' => 'unpaid',
                    'reason' => \App\Models\Fine::REASON_OVERDUE, // Use the constant here!
                ]);

                $created++;
                $totalAmount += $fineAmount;
                \Log::info("Created fine ID: {$fine->id}");
            } else {
                \Log::info("No fine calculated for checkout {$checkout->id}");
            }
        }

        \Log::info('=== FINISHED: Created ' . $created . ' fines totaling $' . $totalAmount . ' ===');

        if ($created > 0) {
            return back()->with('success', "Generated {$created} overdue fines totaling $" . number_format($totalAmount, 2));
        } else {
            return back()->with('info', 'No new overdue fines to generate. Check Laravel logs for details.');
        }
    }

    public function recalculateFines()
    {
        $unpaidFines = Fine::where('status', 'unpaid')
            ->whereNotNull('checkout_id')
            ->with('checkout')
            ->get();

        $recalculated = 0;

        foreach ($unpaidFines as $fine) {
            if ($fine->checkout && $fine->checkout->due_date) {

                $dueDate = \Carbon\Carbon::parse($fine->checkout->due_date)->startOfDay();
                $today = \Carbon\Carbon::today()->startOfDay();

                if ($today->gt($dueDate)) {

                    $daysOverdue = $dueDate->diffInDays($today);

                    // Unlimited fine (no max)
                    $newAmount = round($daysOverdue * 0.50, 2);

                    if ($fine->amount != $newAmount) {
                        $fine->update([
                            'amount' => $newAmount,
                            'reason' => Fine::REASON_OVERDUE,
                        ]);

                        $recalculated++;
                    }
                }
            }
        }

        return back()->with(
            $recalculated > 0 ? 'success' : 'info',
            $recalculated > 0
            ? "Recalculated {$recalculated} fines."
            : 'No fines needed recalculation.'
        );
    }


    public function payFine(Fine $fine)
    {
        $fine->markAsPaid();
        return back()->with('success', 'Fine paid successfully.');
    }

    public function getUserFines(User $user)
    {
        $fines = Fine::where('user_id', $user->id)
            ->with('checkout.book')
            ->latest()
            ->paginate(20);

        return view('librarian.fines.user', compact('fines', 'user'));
    }
}