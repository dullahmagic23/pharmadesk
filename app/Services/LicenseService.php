<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class LicenseService
{
    protected string $key;
    protected string $serverUrl;
    protected string $application;

    public function __construct()
    {
        $this->key = config('license.key');
        $this->serverUrl = config('license.server_url');
        $this->application = config('license.application');
    }

    public function verify(): bool
    {
        // Check cache first
        $cached = Cache::get('license_verified_at');
        if ($cached && now()->diffInHours($cached) < 24) {
            return true;
        }

        // Abort the verification if there's no internet connection'
        if (! $this->hasInternetConnection()) {
            abort(403, 'make sure you are connected to the internet to verify your license');
        }

        try {
            $response = Http::post("{$this->serverUrl}/verify", [
                'key' => $this->key,
                'application' => $this->application,
                'hardware_id' => $this->getHardwareId(),
            ]);

            if ($response->ok() && $response->json('status') === 'active') {
                Cache::put('license_verified_at', now(), 24 * 60); // cache 24h
                return true;
            } elseif ($response->json('status') === 'invalid') {
                abort(403, $response->json('message'));
            }
        } catch (\Throwable $e) {
            // log and fallback
            \Log::warning('License verification failed: '.$e->getMessage());
        }

        return false;
    }


    public function heartbeat(): void
    {
        try{
            Http::post("{$this->serverUrl}/heartbeat", [
                'key' => $this->key,
                'application' => $this->application,
                'version' => config('app.version', '1.0'),
            ]);
        } catch (\Exception $exception){
//            Log::log($exception->getMessage());
        }
    }

    protected function getHardwareId(): string
    {
        // Generate a unique machine ID. Example: hostname or MAC
        return md5(gethostname() . php_uname());
    }

    protected function hasInternetConnection(): bool
    {
        try {
            // Attempt to open a socket to Google DNS
            $connected = @fsockopen("8.8.8.8", 53, $errno, $errstr, 2);
            if ($connected) {
                fclose($connected);
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }
        return false;
    }
}
