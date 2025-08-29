<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfHasRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && ($request->is('/') || $request->is('dashboard'))) {
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('pharmacist')) {
                return redirect()->route('pharmacist.dashboard');
            }

            if ($user->hasRole('doctor')) {
                return redirect()->route('doctor.dashboard');
            }

            if ($user->hasRole('cashier')){
                return to_route('sales.create');
            }
        }

        return $next($request);
    }
}

