<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            $subject = null;

            foreach ($request->route()?->parameters() ?? [] as $key => $value) {
                if ($value instanceof Model) {
                    $subject = $value;
                    break;
                } elseif (is_numeric($value)) {
                    $class = 'App\\Models\\' . Str::studly(Str::singular($key));
                    if (class_exists($class)) {
                        $subject = $class::find($value);
                        break;
                    }
                }
            }

            activity('user-activity')
                ->causedBy(Auth::user())
                ->when($subject, fn($log) => $log->performedOn($subject))
                ->withProperties([
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ])
                ->event($request->method())
                ->log('Accessed ' . $request->path());
        }

        return $response;
    }
}
