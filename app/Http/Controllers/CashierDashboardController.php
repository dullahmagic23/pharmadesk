<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class CashierDashboardController extends Controller
{
    /**
     * Display the cashier dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
            $salesToday = Sale::whereDate('created_at', now())->sum('total');
            $transactions = SalePayment::whereDate('created_at', now())->count();
            $pendingOrders = Sale::where('status', 'pending')->count();

            $lowStock = Cache::remember('low_stock_count', now()->addMinutes(5), function () {
                return Product::whereColumn('quantity', '<=', 'min_stock')->count();
            });

            $recentTransactions = Sale::with('customer')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

        return inertia('Cashier/Dashboard', [
            'salesToday' => $salesToday,
            'transactions' => $transactions,
            'pendingOrders' => $pendingOrders,
            'lowStock' => $lowStock,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
