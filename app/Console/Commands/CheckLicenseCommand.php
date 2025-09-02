<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LicenseService;
use Illuminate\Support\Facades\Cache;

class CheckLicenseCommand extends Command
{
    protected $signature = 'license:check';
    protected $description = 'Check the validity of the license key with the LicenseServer';

    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        parent::__construct();
        $this->licenseService = $licenseService;
    }

    public function handle()
    {
        $this->info('Checking license...');

        if ($this->licenseService->verify()) {
            $this->info('License is valid.');
            Cache::put('license_status', 'active', now()->addDay());
        } else {
            $this->warn('License is invalid or expired.');
            Cache::put('license_status', 'invalid', now()->addDay());
        }
    }
}
