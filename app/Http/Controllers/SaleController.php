<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::latest()->paginate(10)
            ->withQueryString()
            ->through(function ($sale) {
                return [
                    'id' => $sale->id,
                    'created_at' => $sale->date,
                    'buyer_name' => $sale->buyer->name,
                    'buyer_type' => $sale->buyer_type,
                    'items_count' => $sale->items->count(),
                    'total' => $sale->total,
                    'paid' => $sale->paid,
                    'balance' => $sale->total - $sale->paid,
                    'status' => $sale->paid >= $sale->total ? 'paid' :
                        ($sale->paid > 0 ? 'partial' : 'unpaid'),
                ];
            });

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
        ]);
    }

    public function create()
    {
        // Fetch the customers
        $customers = Customer::select('id', 'name')->get();

        // Fetch the stocks, ordered by stockable name (assuming you want to sort by `name` of stockable)
        $stocks = Stock::with(['stockable', 'unit'])
            ->get()
            ->sortBy(function ($stock) {
                return $stock->stockable->name ?? '';
            })
            ->values(); // Reindex the collection

        return inertia('Sales/Create', [
            'customers' => $customers,
            'stocks' => $stocks,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|uuid|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.stock_id' => 'required|uuid|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'balance' => 'required|numeric|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            $stock = Stock::find($item['stock_id']);
            $itemName = ($stock && $stock->stockable && isset($stock->stockable->name)) ? $stock->stockable->name : 'Unknown';
            if (!$stock || $item['quantity'] > $stock->quantity) {
                throw ValidationException::withMessages([
                    'items' => ["The quantity for item {$itemName} exceeds available stock."],
                ]);
            }
        }

        DB::transaction(function () use ($validated) {
            $sale = Sale::create([
                'buyer_type' => 'App\\Models\\Customer',
                'buyer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'total' => $validated['total'],
                'paid' => $validated['paid'],
                'balance' => $validated['balance'],
                'date' => now(),
            ]);

            $sale->receipt()->create([
                'amount' => $validated['paid'],
                'issued_at' => now(),
                'issued_by' => auth()->user()?->name ?? 'System', // Or ID
                'reference' => 'RCT-'.mt_rand(100000, 999999),
            ]);

            $sale->payments()->create([
                'amount' => $validated['paid'],
                'paid_at' => now(),
            ]);



            foreach ($validated['items'] as $item) {
                $stock = Stock::findOrFail($item['stock_id']);

                $sale->items()->create([
                    'sellable_type' => $stock->stockable_type,
                    'sellable_id' => $stock->stockable_id,
                    'stock_id' => $item['stock_id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['quantity'] * $item['price'],
                    'price' => $item['price'],
                ]);

                $stock->decrement('quantity', $item['quantity']);
            }
        });

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }


    public function show(Sale $sale)
    {
        $sale->load(['buyer', 'items.sellable']);
        return inertia('Sales/Show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $sale->load(['buyer', 'items']);

        return inertia('Sales/Edit', [
            'sale' => $sale,
            'customers' => Customer::select('id', 'name')->get(),
            'patients' => Patient::select('id', 'name')->get(),
            'medicines' => Medicine::select('id', 'name')->get(),
            'products' => Product::select('id', 'name')->get(),
            'equipment' => Equipment::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Sale $sale)
    {
        // (Optional: you can implement updates similarly to store)
    }

    public function printReceipt(Sale $sale)
    {
        $receipt = $sale->receipt;
        $receipt->load(['sale.buyer', 'sale.payments','sale.items.sellable']);

        $pdf = Pdf::loadView('receipts.show', compact('receipt'))
            ->setPaper([0, 0, 226.77, 420]); // 8cm x 29.7cm in points

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=Receipt-{$receipt->reference}.pdf",
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }


}
