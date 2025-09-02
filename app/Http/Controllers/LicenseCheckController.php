<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Inertia\Inertia;

class LicenseCheckController extends Controller
{

    public function index()
    {
        return Inertia::render('License/Check');
    }
    public function check()
    {
        Artisan::command('license:check');
    }
}
