<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Http\Request;

class ApiSalesController extends Controller
{
    public function index()
    {
        return Sale::all();
    }

    public function store(Request $request)
    {

    }
    public function destroy(Sale $sale)
    {
        foreach ($sale->items as $item) {
            $stock = Stock::find($item->stock_id);
            $stock?->increment('quantity', $item->quantity);
        }
        $sale->receipt()->delete();
        $sale->delete();

        return redirect()->back()->with('success', 'Sale deleted.');
    }
}
