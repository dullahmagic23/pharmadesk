<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $stock = $item->stock;
            $stock->increment('quantity', $item->quantity);
            $stock->save();
        }
        $sale->receipt()->delete();
        $sale->payments()->delete();
        $sale->items()->delete();
        $sale->delete();

        return redirect()->back()->with('success', 'Sale deleted.');
    }
}
