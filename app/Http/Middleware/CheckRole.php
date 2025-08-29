<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if the user is authenticated and has the required role
        if (!Auth::check()) {
            return redirect()->route('login'); // or any other logic you want for unauthenticated users
        }

        // Check if the authenticated user has the role
        if (!Auth::user()->hasRole($role)) {
            abort(403, 'Unauthorized'); // You can also return a custom response or redirect
        }

        return $next($request);
    }
}
