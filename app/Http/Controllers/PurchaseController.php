<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Medicine;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['vendor', 'purchasables.purchasable'])
            ->latest()
            ->get();

        return Inertia::render('Purchases/Index', [
            'purchases' => $purchases
        ]);
    }
    public function create()
    {
        $vendors = Vendor::all();
        $products = Product::all();
        $medicines = Medicine::all();

        return Inertia::render('Purchases/Create', [
            'vendors' => $vendors,
            'products' => $products,
            'medicines' => $medicines,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'total_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.purchasable_type' => 'required|in:product,medicine',
            'items.*.purchasable_id' => 'required|uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string|max:255',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        $purchase = Purchase::create([
            'vendor_id' => $validated['vendor_id'],
            'purchase_date' => $validated['date'] ?? now()->toDateString(),
            'total_amount' => 0, // will be recalculated
            'reference_number' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $total = 0;

        foreach ($validated['items'] as $item) {
            $modelClass = match ($item['purchasable_type']) {
                'product' => Product::class,
                'medicine' => Medicine::class,
            };

            $subtotal = $item['unit_cost'] * $item['quantity'];

            $purchase->purchasables()->create([
                'id' => Str::uuid(),
                'purchasable_type' => $modelClass,
                'purchasable_id' => $item['purchasable_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'subtotal' => $subtotal,
                'batch_number' => $item['batch_number'] ?? null,
                'expiry_date' => $item['expiry_date'] ?? null,
            ]);

            $total += $subtotal;
        }

        $purchase->update(['total_amount' => $total]);

        return redirect()->route('purchases.index')->with('success', 'Purchase added successfully!');
    }

    public function show(Purchase $purchase)
    {
        return Inertia::render('Purchases/Show', [
            'purchase' => $purchase->load(['vendor', 'purchasables.purchasable']),
        ]);
    }


    public function print(Purchase $purchase)
    {
        $purchase->load(['vendor', 'purchasables.purchasable']);

        $pdf = Pdf::loadView('pdf.purchase', [
            'purchase' => $purchase
        ]);

        return $pdf->stream('purchase_' . $purchase->id . '.pdf'); // or ->download() to download
    }
}
