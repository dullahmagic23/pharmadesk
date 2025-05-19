<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Stock;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = Stock::with('stockable', 'unit')->latest()->get();
        return Inertia::render('Stocks/Index', [
            'stocks' => $stocks,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Stocks/Create", [
            'medicines' => Medicine::with('units')->get(),
            'products' => Product::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stockable_type' => 'required|in:App\Models\Product,App\Models\Medicine',
            'stockable_id' => 'required|uuid',
            'quantity' => 'required|numeric|min:0.01',
            'retail_price' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'date' => 'nullable|date',
            'selected_unit' => 'nullable|string|exists:medicine_units,id',
        ]);
        // Check if stock already exists
        $query = Stock::where('stockable_type', $validated['stockable_type'])
            ->where('stockable_id', $validated['stockable_id']);

        if ($validated['stockable_type'] === 'App\Models\Medicine') {
            $query->where('unit_id', $validated['selected_unit']);
        }

        $stock = $query->first();

        if ($stock) {
            $stock->update([
                'quantity' => $stock->quantity + $validated['quantity'],
                'retail_price' => $validated['retail_price'], // Optional: update pricing
                'wholesale_price' => $validated['wholesale_price'],
            ]);
        } else {
            $stockData = [
                'stockable_type' => $validated['stockable_type'],
                'stockable_id' => $validated['stockable_id'],
                'quantity' => $validated['quantity'],
                'retail_price' => $validated['retail_price'],
                'wholesale_price' => $validated['wholesale_price'],
            ];

            if ($validated['stockable_type'] === 'App\Models\Medicine') {
                $stockData['unit_id'] = $validated['selected_unit'];
            }

            $stock = Stock::create($stockData);
        }

        // Record in history
        $stock->histories()->create([
            'quantity' => $validated['quantity'],
            'date' => $validated['date'] ?? now()->toDateString(),
        ]);


        return redirect()->route('stocks.index')->with('success', 'Stock saved successfully.');
    }



    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        return Inertia::render('Stocks/Edit', [
            'stock' => $stock,
            'medicines' => Medicine::with('units')->get(),
            'products' => Product::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'stockable_type' => 'required|in:App\Models\Product,App\Models\Medicine',
            'stockable_id' => 'required|uuid',
            'quantity' => 'required|numeric|min:0.01',
            'retail_price' => 'required|numeric|min:0',
            'wholesale_price' => 'required|numeric|min:0',
            'date' => 'nullable|date',
            'selected_unit' => 'nullable|string|exists:medicine_units,id',
        ]);

        $stock->quantity = $validated['quantity'];
        $stock->retail_price = $validated['retail_price'];
        $stock->wholesale_price = $validated['wholesale_price'];

        if ($stock->stockable_type === 'App\Models\Medicine' && isset($validated['selected_unit'])) {
            $stock->unit_id = $validated['selected_unit'];
        }

        $stock->save();

        // Optionally record in history
        $stock->histories()->create([
            'quantity' => $validated['quantity'],
            'date' => $validated['date'] ?? now()->toDateString(),
        ]);

        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        //
    }

    public function getPrice(Request $request)
    {
        $request->validate([
            'sellable_type' => 'required|in:Medicine,Product',
            'sellable_id' => 'required|uuid',
            'sale_type' => 'required|in:retail,wholesale',
        ]);

        $class = 'App\\Models\\' . $request->sellable_type;

        $model = $class::findOrFail($request->sellable_id);
        $stock = $model->stock;

        if (!$stock) {
            return response()->json(['price' => 0]);
        }

        return response()->json([
            'price' => $request->sale_type === 'retail'
                ? $stock->retail_price
                : $stock->wholesale_price
        ]);
    }

}
