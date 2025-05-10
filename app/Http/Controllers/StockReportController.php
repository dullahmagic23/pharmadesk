<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $stocks = Stock::with(['stockable', 'histories'])->get();

        $data = $stocks->map(function ($stock) {
            $stockIn = $stock->histories->sum('quantity');
            $currentQuantity = $stock->quantity;
            $stockOut = $stockIn - $currentQuantity;

            return [
                'id' => $stock->id,
                'item_name' => optional($stock->stockable)->name ?? '—',
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'quantity' => $currentQuantity,
            ];
        });

        return Inertia::render('Reports/Stock', [
            'stocks' => $data,
        ]);
    }

}
