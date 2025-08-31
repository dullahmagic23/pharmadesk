<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receipt;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = Receipt::query()
            ->with('sale');
//            ->where('amount', '>', 0);
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('issued_by', 'like', "%{$search}%")
                    ->orWhere('sale_id', 'like', "%{$search}%");
            });
        }

        $receipts = $query->latest()->get();

        return Inertia::render('Receipts/Index', [
            'receipts' => $receipts,
            'filters'  => $request->only('search'),
        ]);
    }
    public function show(Receipt $receipt)
    {
        $receipt->load(['sale','sale.buyer', 'sale.payments','sale.items.sellable']);
//    dd($receipt);
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

    public function destroy(Receipt $receipt)
    {
        $receipt->delete();
        return redirect()->route('receipts.index')->with('success', 'Receipt deleted successfully.');
    }

}
