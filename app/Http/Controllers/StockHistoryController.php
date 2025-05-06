<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockHistory;
use Inertia\Inertia;

class StockHistoryController extends Controller
{
    public function index()
    {
        $histories = StockHistory::with('stock.stockable')->latest()->get();
        return Inertia::render('StockHistory/Index', ['stockHistories' => $histories]);

    }
}
