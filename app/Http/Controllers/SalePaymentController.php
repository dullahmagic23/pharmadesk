<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SalePaymentController extends Controller
{
    public function index(Request $request)
    {
        // Base query with sale and customer
        $query = SalePayment::with(['sale.buyer']);

        // Search by payment ID, reference, customer name, or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_payments.id', 'like', "%{$search}%")
                    ->orWhere('sale_payments.reference', 'like', "%{$search}%")
                    ->orWhereHas('sale.buyer', function ($buyer) use ($search) {
                        $buyer->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // No filtering by payment method (removed)

        // Date filtering
        if ($request->filled('from_date')) {
            $query->whereDate('sale_payments.created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('sale_payments.created_at', '<=', $request->to_date);
        }

        // Get all for computed status filtering
        $payments = $query->orderBy('sale_payments.created_at', 'desc')->get();

        // Filter by computed sale status
        if ($request->filled('status')) {
            $payments = $payments->filter(function ($payment) use ($request) {
                return $payment->sale && $payment->sale->status === $request->status;
            })->values();
        }

        // Manual pagination after collection filtering
        $page = $request->input('page', 1);
        $perPage = 15;
        $paginatedPayments = new \Illuminate\Pagination\LengthAwarePaginator(
            $payments->forPage($page, $perPage),
            $payments->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Stats using computed sale status
        $stats = [
            'total_payments' => SalePayment::count(),
            'total_amount' => SalePayment::sum('amount'),
            'pending_count' => $payments->filter(fn($p) => $p->sale->status === 'unpaid')->count(),
            'partial_count' => $payments->filter(fn($p) => $p->sale->status === 'partial')->count(),
            'completed_count' => $payments->filter(fn($p) => $p->sale->status === 'paid')->count(),
        ];

        return Inertia::render('Sales/Payments/Index', [
            'payments' => $paginatedPayments,
            'stats' => $stats,
            'filters' => [
                'search' => $request->search ?? '',
                'status' => $request->status ?? '',
                'from_date' => $request->from_date ?? '',
                'to_date' => $request->to_date ?? '',
            ],
            // You can build these dynamically from config or constants if needed
            'statuses' => ['pending', 'partial', 'completed'],
        ]);
    }

    public function show( $payment)
    {
        $Payment = SalePayment::findOrFail($payment);
        $Payment->load('sale.buyer');
        return Inertia::render('Sales/Payments/Show',[
            'payment' => $Payment,
        ]);
    }




    public function addPayments(Sale $sale)
    {
        return Inertia::render("Sales/AddPayments", [
            "sale" => $sale->load('buyer'),
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

    public function destroy(SalePayment $salesPayment)
    {
        DB::transaction(function () use ($salesPayment) {
            // Update the sale's paid and balance fields before deleting the payment
            $sale = $salesPayment->sale;
            $sale->refresh();
            $sale->paid -= $salesPayment->amount;
            $sale->balance = $sale->total - $sale->paid;
            $sale->save();

            // Delete the payment
            $salesPayment->delete();
        });

        return redirect()
            ->route('sales-payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

}
