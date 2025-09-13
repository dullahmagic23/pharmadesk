<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        // Optional: use cache for offline tolerance
        $cached = Cache::get('license_verified_at');
        if ($cached && now()->diffInHours($cached) < 24) {
            return true;
        }

        $response = Http::post("{$this->serverUrl}/verify", [
            'key' => $this->key,
            'application' => $this->application,
            'hardware_id' => $this->getHardwareId(),
        ]);

//        return $response;

        if ($response->ok() && $response->json('status') === 'active') {
            Cache::put('license_verified_at', now(), 24 * 60); // cache 24h
            return true;
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
}
