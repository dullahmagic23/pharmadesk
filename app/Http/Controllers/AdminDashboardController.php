<?php

namespace App\Http\Controllers;

use App\Services\GetExpiredItems;
use App\Services\LowStockService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Activity;
use App\Models\Patient;

class AdminDashboardController extends Controller
{
    public function index(GetExpiredItems $getExpiredItems, LowStockService $lowStockService)
    {
        $expiredItems = $getExpiredItems->getAlerts();
        $lowStocks = $lowStockService->getLowStocks();
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
        ]);

    }
}
