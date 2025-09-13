<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\Sale;
use Inertia\Inertia;
use App\Models\Purchase;
use App\Models\Vendor;

class ReportController extends Controller
{
    // ReportController.php

    public function sales(Request $request)
    {
        $sales = Sale::with(['items.sellable.stock'])
            ->when(
                $request->from && $request->to,
                fn($q) =>
                $q->whereBetween('created_at', [$request->from, $request->to])
            )
            ->latest()
            ->get();

        return Inertia::render('Reports/Sales', [
            'sales' => $sales,
            'filters' => $request->only(['from', 'to']),
        ]);
    }

    public function purchaseReport()
    {
        $purchases = Purchase::with(['vendor', 'purchasables.purchasable.stock'])
            ->latest()
            ->get();

        return Inertia::render('Reports/Purchases', [
            'purchases' => $purchases,
            'vendors' => Vendor::all()
        ]);
    }



}
