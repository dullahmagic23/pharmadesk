<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receipt;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function show(Sale $sale)
{
    $receipt = $sale->receipt;
    $receipt->load(['sale.buyer', 'sale.payments','sale.items.sellable']);

    $pdf = Pdf::loadView('receipts.show', compact('receipt'))
        ->setPaper([0, 0, 226.77, 420]); // 8cm x 29.7cm in points

    return response($pdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => "inline; filename=Receipt-{$receipt->reference}.pdf",
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
    ]);
}

}
