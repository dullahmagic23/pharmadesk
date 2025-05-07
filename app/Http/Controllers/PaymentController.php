<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('invoice.patient')->latest()->get();
        return inertia('Payments/Index', compact('payments'));
    }

    public function create()
    {
        $invoices = Invoice::with('patient')->where('status', 'unpaid')->get();
        return inertia('Payments/Create', [
            'invoices' => $invoices,
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,card,insurance',
            'paid_at' => 'nullable|date',
        ]);

        DB::transaction(function () use ($data) {
            $invoice = Invoice::findOrFail($data['invoice_id']);

            if ($invoice->balance < $data['amount']) {
                throw ValidationException::withMessages([
                    'amount' => 'The amount must be less than or equal to  '.$invoice->balance ,
                ]);
            }

            Payment::create([
                'id' => Str::uuid(),
                'invoice_id' => $data['invoice_id'],
                'amount' => $data['amount'],
                'method' => $data['method'],
                'paid_at' => $data['paid_at'] ?? now(),
            ]);
            $invoice->decrement('balance', $data['amount']);
            if ($invoice->balance <= 0) {
                $invoice->update(['status' => 'paid']);
            }
        });
        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('invoice');
        return inertia('Payments/Show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return back()->with('success', 'Payment deleted successfully.');
    }
}
