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
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%$search%")
                    ->orWhere('issued_by', 'like', "%$search%")
                    ->orWhere('sale_id', 'like', "%$search%");
            });
        }

        $receipts = $query->latest()->get();

        return Inertia::render('Receipts/Index', [
            'receipts' => $receipts,
            'filters'  => $request->only('search'),
        ]);
    }

    public function create()
    {
        $sales = Sale::with('buyer')->get();
        return Inertia::render('Receipts/Create',[
            'sales' => $sales
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
        ]);
        if(Receipt::where('sale_id', $request['sale_id'])->exists()){
            return redirect()->back()->with('warning', 'A receipt has already been issued for this sale.')->withInput(['sale_id' => $request['sale_id']]);
        }
        $sale = Sale::find($request['sale_id']);
        $sale->receipt()->create([
            'issued_at' => now(),
            'issued_by' => auth()->user()?->name ?? 'System',
            'reference' => 'RCT-'.mt_rand(100000, 999999),
        ]);

        return redirect()->route('receipts.index')->with('success', 'Receipt created successfully.');

    }

    public function show(Receipt $receipt)
    {
        $receipt->load(['sale','sale.buyer', 'sale.payments','sale.items.sellable']);
        $pdf = Pdf::loadView('receipts.show', compact('receipt'))
            ->setPaper([0, 0, 226.77, 420]); // 8cm x 29.7cm in points

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=Receipt-$receipt->reference.pdf",
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
