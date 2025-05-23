<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

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
                    'items_count' => $sale->items_count,
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
        return inertia('Sales/Create', [
            'customers' => Customer::select('id', 'name')->get(),
            'patients' => Patient::all(),
            'medicines' => Medicine::with('stock')->whereHas('stock')->select('id', 'name')->get(),
            'products' => Product::with('stock')->whereHas('stock')->select('id', 'name')->get(),
            'equipment' => Equipment::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'buyer_type' => ['required', Rule::in(['Customer', 'Patient'])],
            'buyer_id' => 'required|uuid',
            'items' => 'required|array|min:1',
            'items.*.sellable_type' => ['required', Rule::in(['Medicine', 'Product'])],
            'items.*.sellable_id' => 'required|uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.stock' => 'required|array',
            'total' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
            'balance' => 'required|numeric|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            $stock = Stock::find($item['stock']['id']);
            if (!$stock || $item['quantity'] > $stock->quantity) {
                throw ValidationException::withMessages([
                    'items' => ["The quantity for item {$item['sellable']['name']} exceeds available stock."],
                ]);
            }
        }

        DB::transaction(function () use ($validated) {
            $sale = Sale::create([
                'buyer_type' => "App\\Models\\" . $validated['buyer_type'],
                'buyer_id' => $validated['buyer_id'],
                'total' => $validated['total'],
                'paid' => $validated['paid'],
                'balance' => $validated['balance'],
                'date' => date('Y-m-d H:i:s',now()->timestamp),
            ]); 

            $sale->payments()->create([
                'amount' => $validated['paid'],
                'paid_at' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $sale->items()->create([
                    'sellable_type' => "App\\Models\\" . $item['sellable_type'],
                    'sellable_id' => $item['sellable_id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['quantity'] * $item['price'],
                    'price' => $item['price'],
                ]);

                $stock = Stock::find($item['stock']['id']);
                $stock->update([
                    'quantity' => $stock->quantity - $item['quantity'],
                ]);

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

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return back()->with('success', 'Sale deleted.');
    }

   
}
