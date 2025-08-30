<?php

namespace App\Http\Controllers;

use App\Services\GetExpiredItems;
use App\Services\LowStockService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Activity;
use App\Models\Patient;
use App\Models\Sale;
use App\Models\SaleItem;

class AdminDashboardController extends Controller
{
    public function index(GetExpiredItems $getExpiredItems, LowStockService $lowStockService)
    {
        $expiredItems = $getExpiredItems->getAlerts();
        $lowStocks = $lowStockService->getLowStocks();

        $driver = DB::getDriverName();

        // ----- Sales Over Time (last 7 days) -----
        if ($driver === 'mysql') {
            $salesOverTime = Sale::selectRaw('DATE(created_at) as day, SUM(total) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->limit(7)
                ->pluck('total', 'day');
        } else {
            // SQLite
            $salesOverTime = Sale::selectRaw("strftime('%Y-%m-%d', created_at) as day, SUM(total) as total")
                ->groupBy('day')
                ->orderBy('day')
                ->limit(7)
                ->pluck('total', 'day');
        }

        // Format line chart data
        $salesLine = [
            'labels' => $salesOverTime->keys(),
            'values' => $salesOverTime->values(),
        ];

        // ----- Top Products (by units sold) ----
// Get top 5 sellables by quantity sold
        $topProducts = SaleItem::with('sellable:id,name')
            ->get()
            ->groupBy('sellable_id')
            ->map(function ($items, $sellableId) {
                return [
                    'name' => $items->first()->sellable->name ?? 'Unknown',
                    'total' => $items->sum('quantity'),
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values(); // reset keys

        $salesBar = [
            'labels' => $topProducts->pluck('name'),
            'values' => $topProducts->pluck('total'),
        ];


        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'doctors' => User::role('doctor')->count(),
                'pharmacists' => User::role('pharmacist')->count(),
                'patients' => Patient::count(),
            ],
            'activity' => Activity::latest()->take(5)->pluck('description'),
            'user' => auth()->user()->load('roles'),
            'expiredStocks' => $expiredItems['expired'],
            'expiringStocks' => $expiredItems['expiring'],
            'lowStocks' => $lowStocks,

            // ✅ Sales data for charts
            'sales' => [
                'labels' => $salesLine['labels'],
                'values' => $salesLine['values'],
                'topProducts' => $salesBar,
            ],
        ]);
    }
}
