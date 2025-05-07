<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use App\Models\Medicine;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('patient')->latest()->paginate(10);
        return inertia('Invoices/Index', compact('invoices'));
    }

    public function create()
    {
        return inertia('Invoices/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name')->get(),
            'medicines' => Medicine::whereHas('stock')->with('stock')->get(),
            'products' => Product::whereHas('stock')->with('stock')->get(),
            'services' => Service::select('id', 'name', 'price')->get(),
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'items' => 'required|array|min:1',
            'items.*.billable_type' => 'required|string',
            'items.*.billable_id' => 'required|uuid',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.price_type' => 'nullable|string|in:retail,wholesale',
            'total' => 'required|numeric',
        ]);

        DB::transaction(function () use ($data) {
            $invoice_number = Str::random(6);
            $invoice = Invoice::create([
                'id' => Str::uuid(),
                'patient_id' => $data['patient_id'],
                'invoice_date' => now(),
                'status' => 'unpaid',
                'balance' => $data['total'],
                'total' => $data['total'],
                'invoice_number' => $invoice_number,
            ]);

            foreach ($data['items'] as $item) {
                // Normalize class name
                $normalizedClass = str_replace('\\\\', '\\', $item['billable_type']);
                $billable = $normalizedClass::with('stock')->findOrFail($item['billable_id']);

                // Stock check for Product or Medicine
                if (in_array($normalizedClass, [Product::class, Medicine::class])) {
                    if (!$billable->stock || $billable->stock->quantity < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => ["Insufficient stock for item: {$billable->name}"],
                        ]);
                    }
                }

                $price = $item['price_type'] === 'wholesale'
                    ? ($billable->wholesale_price ?? $billable->price ?? 0)
                    : ($billable->retail_price ?? $billable->price ?? 0);

                InvoiceItem::create([
                    'id' => Str::uuid(),
                    'invoice_id' => $invoice->id,
                    'billable_id' => $billable->id,
                    'billable_type' => $normalizedClass, // ✅ Use normalized class here
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'price_type' => $item['price_type'],
                    'total_price' => $item['quantity'] * $item['price'],
                ]);

                // Update stock quantity
                if (in_array($normalizedClass, [Product::class, Medicine::class])) {
                    $billable->stock->decrement('quantity', $item['quantity']);
                }
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'items.billable']);
        return Inertia::render('Invoices/Show', ['invoice' => $invoice]);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return back()->with('success', 'Invoice deleted.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['patient', 'items.billable']);

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

        return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }
}
