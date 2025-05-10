<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Purchase;

class PurchaseReportController extends Controller
{


    public function exportPdf(Request $request)
    {
        $purchases = Purchase::with(['vendor', 'purchasables.purchasable'])
            ->when($request->startDate, fn($q) => $q->whereDate('created_at', '>=', $request->startDate))
            ->when($request->endDate, fn($q) => $q->whereDate('created_at', '<=', $request->endDate))
            ->when($request->vendorId, fn($q) => $q->where('vendor_id', $request->vendorId))
            ->get();
        $pdf = Pdf::loadView('pdf.purchase-report', compact('purchases'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('purchase-report.pdf');
    }

}
