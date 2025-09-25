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
            $salesOverTime = Sale::selectRaw("strftime('%Y-%m-%d', created_at) as day, SUM(total) as total")
                ->groupBy('day')
                ->orderBy('day')
                ->limit(7)
                ->pluck('total', 'day');
        }

        $salesChartData = [
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

        // ----- Top Products -----
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

        $productsChartData = [
            'labels' => $topProducts->pluck('name'),
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => $topProducts->pluck('total'),
                    'backgroundColor' => '#10b981',
                ]
            ],
        ];

        // ----- Recent Activity -----
        $activities = Activity::latest()
            ->take(5)
            ->get(['id', 'description', 'created_at'])
            ->map(fn($a) => $a->description . ' (' . $a->created_at->diffForHumans() . ')');

        return Inertia::render('Admin/Dashboard', [
            'userName' => Auth::user()->name,
            'userRole' => Auth::user()->roles->pluck('name')->implode(', '),
            'dailyQuote' => 'Excellence is not an act but a habit.', // placeholder, can randomize

            'expiredItems' => $expiredItems['expired'],
            'expiringItems' => $expiredItems['expiring'],
            'lowStockItems' => $lowStocks,

            'metrics' => [
                'systemHealth' => 75,
                'activeUsers' => User::count(),
                'revenue' => Sale::sum('total'),
                'orders' => Sale::count(),
            ],

            'salesChartData' => $salesChartData,
            'productsChartData' => $productsChartData,
            'activities' => $activities,
        ]);
    }

}
