<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Activity;
use App\Models\Patient;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'doctors' => User::role('doctor')->count(),
                'pharmacists' => User::role('pharmacist')->count(),
                'patients' => Patient::count(),
            ],
            'activity' => Activity::latest()->take(5)->pluck('description'),
            'user' => auth()->user()->load('roles'),
        ]);

    }
}
