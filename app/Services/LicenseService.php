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
        if(!$this->checkInternetConnection()) {
            abort(403,"The system needs to perform a licence verification,
            Please connect to the internet and try again");
        }
        // Optional: use cache for offline tolerance
        $cached = Cache::get('license_verified_at');
        if ($cached && now()->diffInHours($cached) < (24*30)) {
            return true;
        }

        $response = Http::post("{$this->serverUrl}/verify", [
            'key' => $this->key,
            'application' => $this->application,
            'hardware_id' => $this->getHardwareId(),
        ]);

//        return $response;

        if ($response->ok() && $response->json('status') === 'active') {
            Cache::put('license_verified_at', now(), 30*24 * 60); // cache 30 days
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

    //function to check for internet connection
    private function checkInternetConnection()
    {
        try{
            $response = @fsockopen("www.google.com", 80, $errno, $errstr, 5);
            if ($response) {
                fclose($response);
                return true;
            }
            return false;
        } catch( \Exception $e){

        }
    }
}
