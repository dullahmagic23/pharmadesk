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
        $stocks = Stock::with('stockable')->latest()->get();
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
            'medicines' => Medicine::all(),
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
        ]);
        // Check if stock already exists
        $stock = Stock::where('stockable_type', $validated['stockable_type'])
            ->where('stockable_id', $validated['stockable_id'])
            ->first();

        if ($stock) {
            $stock->update([
                'quantity' => $stock->quantity + $validated['quantity'],
                'retail_price' => $validated['retail_price'], // Optional: update pricing
                'wholesale_price' => $validated['wholesale_price'],
            ]);
        } else {
            $stock = Stock::create([
                'stockable_type' => $validated['stockable_type'],
                'stockable_id' => $validated['stockable_id'],
                'quantity' => $validated['quantity'],
                'retail_price' => $validated['retail_price'],
                'wholesale_price' => $validated['wholesale_price'],
            ]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        //
    }
}
