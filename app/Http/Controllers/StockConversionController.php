<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineUnit;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\StockConversion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockConversionController extends Controller
{
    public function create()
    {
        $medicince = Medicine::whereHas('stock')->with('units')->get();
        return Inertia::render('Stocks/Conversion', [
            'stocks' => $medicince,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'selectedStockId' => 'required|exists:medicines,id',
            'fromUnit' => 'required|string',
            'toUnit' => 'required|string|different:fromUnit',
            'quantity' => 'required|numeric|min:0.0001',
            'rate' => 'nullable|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
        ]);


        // Calculate conversion
        $baseAmount = $validated['quantity'];
        $converted = $baseAmount * $validated['rate'];

        $affected_stock = Stock::where('stockable_type', 'App\\Models\\Medicine')
            ->where('stockable_id', $validated['selectedStockId'])
            ->where('unit_id', $validated['fromUnit'])
            ->first();
        if ($affected_stock->quantity < $validated['quantity']) {
            throw ValidationException::withMessages([
                'quantity' => 'Insufficient stock!'
            ]);
        }

        DB::transaction(function () use ($validated, $baseAmount, $converted, $affected_stock) {

            $stock = Stock::where('stockable_type', 'App\\Models\\Medicine')
                ->where('stockable_id', $validated['selectedStockId'])
                ->where('unit_id', $validated['toUnit'])
                ->first();

            if ($stock) {
                $stock->quantity += $converted;
                $stock->retail_price = $validated['retail_price'];
                $stock->wholesale_price = $validated['wholesale_price'];
                $stock->save();
            } else {
                $stock = Stock::create([
                    'stockable_type' => 'App\\Models\\Medicine',
                    'stockable_id' => $validated['selectedStockId'],
                    'quantity' => $converted,
                    'retail_price' => $validated['retail_price'],
                    'wholesale_price' => $validated['wholesale_price'],
                    'unit_id' => $validated['toUnit'],
                ]);
            }

            $stock->histories()->create([
                'quantity' => $validated['quantity'],
                'date' =>  now()->toDateString(),
            ]);


            StockConversion::create([
                'stock_id' => $stock->id,
                'from_unit_id' => $validated['fromUnit'],
                'to_unit_id' => $validated['toUnit'],
                'conversion_factor' => $validated['rate'],
                'quantity_converted' => $converted,
                'stock_generated' => $validated['quantity'],
                'user_id' => auth()->id(),
            ]);


            $affected_stock->decrement('quantity', $validated['quantity']);

        });
        return redirect()
            ->route('stocks.index')
            ->with('success', "Converted {$validated['quantity']}  successfully.");
    }
}
