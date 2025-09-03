<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LicenseService;
use Illuminate\Support\Facades\Cache;
use PharIo\Version\Exception;

class CheckLicense
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $status = Cache::get('license_status', 'invalid');

        if ($status !== 'active') {

            try {
                $check = $this->licenseService->verify();
                if(!$check){
                    abort(403, 'Licence verification failed. Contact support.');
                }
            }catch (Exception $exception){
                abort(403, 'License is invalid, expired, or not authorized.');
            }
        }
        // Optional: heartbeat asynchronously
        $this->licenseService->heartbeat();

        return $next($request);
    }
}
