<?php

use App\Services\GetExpiredItems;
use App\Services\LowStockService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Services\LicenseService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('expiry:check', function (GetExpiredItems $service) {
    $alerts = $service->getAlerts();

    $this->info('⚠️ Expired Batches: ' . $alerts['expired']->count());
    $this->info('📅 Batches Expiring Soon: ' . $alerts['expiring']->count());

    foreach ($alerts['expired'] as $stock) {
        $this->warn("{$stock->stockable->name} - Batch {$stock->batch_number} expired on {$stock->expires_at}");
    }
})->describe('Check for expired and soon-to-expire stock batches');

Schedule::command('expiry:check')->daily();


Artisan::command('stock:low', function (LowStockService $service) {
    $lowStocks = $service->getLowStocks();

    $this->info('⚠️ Low Stock Batches: ' . $lowStocks->count());

    foreach ($lowStocks as $stock) {
        $this->warn("{$stock->stockable->name} - Batch {$stock->batch_number} has only {$stock->quantity} left");
    }
})->describe('Check for low stock quantities');

Schedule::command('stock:low')->daily();



Artisan::command('license:check', function () {
    $licenseService = app(LicenseService::class);

    $this->info('Checking license...');

    if ($licenseService->verify()) {
        $this->info('License is valid.');
        Cache::put('license_status', 'active', now()->addDay());
    } else {
        $this->warn('License is invalid or expired.');
        Cache::put('license_status', 'invalid', now()->addDay());
    }

})->describe('Check the validity of the license key with LicenseServer');
Schedule::command('license:check')->daily();
