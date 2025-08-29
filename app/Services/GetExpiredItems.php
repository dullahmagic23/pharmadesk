<?php

namespace App\Services;

use App\Models\Stock;
use Carbon\Carbon;

class GetExpiredItems
{
    public function getAlerts($days = 30): array
    {
        $today = Carbon::today();
        $limit = Carbon::today()->addDays($days);

        return [
            'expired' => Stock::with('stockable')
                ->whereDate('expiration_date', '<', $today)
                ->get(),

            'expiring' => Stock::with('stockable')
                ->whereBetween('expiration_date', [$today, $limit])
                ->get(),
        ];
    }
}
