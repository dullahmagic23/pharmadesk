<?php

namespace App\Services;

use App\Models\Stock;

class LowStockService
{
    /**
     * Get stocks that are below a threshold
     *
     * @param int $threshold Default minimum quantity
     */
    public function getLowStocks(int $threshold = 10)
    {
        return Stock::with('stockable')
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity', 'asc')
            ->get();
    }
}
