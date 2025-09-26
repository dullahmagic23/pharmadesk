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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{

    public function index(GetExpiredItems $getExpiredItems, LowStockService $lowStockService)
    {
        $expiredItems = $getExpiredItems->getAlerts();
        $lowStocks = $lowStockService->getLowStocks();

        $driver = DB::getDriverName();

        // ----- Sales Over Time (last 7 days) -----
        $salesChartData = Cache::remember('dashboard_sales_7days', 300, function () use ($driver) {
            if ($driver === 'mysql') {
                $salesOverTime = Sale::selectRaw('DATE(created_at) as day, SUM(total) as total')
                    ->groupBy('day')
                    ->orderBy('day')
                    ->limit(7)
                    ->pluck('total', 'day');
            } else {
                $salesOverTime = Sale::selectRaw("strftime('%Y-%m-%d', created_at) as day, SUM(total) as total")
                    ->groupBy('day')
                    ->orderBy('day')
                    ->limit(7)
                    ->pluck('total', 'day');
            }

            return [
                'labels' => $salesOverTime->keys(),
                'datasets' => [
                    [
                        'label' => 'Sales',
                        'data' => $salesOverTime->values(),
                        'fill' => false,
                        'borderColor' => '#3b82f6',
                        'tension' => 0.1,
                    ]
                ],
            ];
        });

        // ----- Top Products -----
        $productsChartData = Cache::remember('dashboard_top_products', 300, function () {
            $topProducts = SaleItem::with('sellable:id,name')
                ->get()
                ->groupBy('sellable_id')
                ->map(function ($items) {
                    return [
                        'name' => $items->first()->sellable->name ?? 'Unknown',
                        'total' => $items->sum('quantity'),
                    ];
                })
                ->sortByDesc('total')
                ->take(5)
                ->values();

            return [
                'labels' => $topProducts->pluck('name'),
                'datasets' => [
                    [
                        'label' => 'Units Sold',
                        'data' => $topProducts->pluck('total'),
                        'backgroundColor' => '#10b981',
                    ]
                ],
            ];
        });

        // ----- Recent Activity -----
        $activities = Cache::remember('dashboard_recent_activity', 120, function () {
            return Activity::latest()
                ->take(5)
                ->get(['id', 'description', 'created_at'])
                ->map(fn($a) => $a->description . ' (' . $a->created_at->diffForHumans() . ')');
        });

        // ----- Metrics -----
        $metrics = Cache::remember('dashboard_metrics', 300, function () {
            return [
                'systemHealth' => 100,
                'activeUsers' => User::count(),
                'revenue' => Sale::sum('total'),
                'orders' => Sale::count(),
            ];
        });

        return Inertia::render('Admin/Dashboard', [
            'userName' => Auth::user()->name,
            'userRole' => Auth::user()->roles->pluck('name')->implode(', '),
            'dailyQuote' => 'Excellence is not an act but a habit.',
            'expiredItems' => $expiredItems['expired'],
            'expiringItems' => $expiredItems['expiring'],
            'lowStockItems' => $lowStocks,
            'metrics' => $metrics,
            'salesChartData' => $salesChartData,
            'productsChartData' => $productsChartData,
            'activities' => $activities,
        ]);
    }

}
