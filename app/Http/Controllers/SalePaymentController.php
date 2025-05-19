<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SalePaymentController extends Controller
{

     public function addPayments(Sale $sale)
    {
        return Inertia::render("Sales/AddPayments",  [
            "sale"=> $sale->load('buyer'),
        ]);
    }
    public function store(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'lte:' . $sale->balance],
        ]);

        DB::transaction(function () use ($validated, $sale) {
            // Create the payment
            SalePayment::create([
                'sale_id' => $sale->id,
                'amount' => $validated['amount'],
            ]);

            // Update the sale's paid and balance fields
        $sale->refresh();
            $sale->paid += $validated['amount'];
            $sale->balance = $sale->total - $sale->paid;
            $sale->save();
        });

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Payment added successfully.');
    }

}
