<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SalePayment;
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
                    'amount' => 'The amount must be less than or equal to  ' . $invoice->balance,
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
        return inertia('Sales/Payments/Show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return back()->with('success', 'Payment deleted successfully.');
    }

    // app/Http/Controllers/PaymentController.php

    public function export(Request $request)
    {
        $query = SalePayment::with('sale.buyer');

        // Apply filters
        if ($request->search) {
            $search = $request->search;
            $query->where('id', 'like', "%{$search}%")
                ->orWhereHas('sale', function ($q) use ($search) {
                    $q->whereHas('buyer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
                });
        }

        if ($request->status) {
            $query->whereHas('sale', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payments = $query->get();

        // Create CSV
        $csvData = "Payment ID,Customer,Amount,Status,Date\n";
        foreach ($payments as $payment) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s\n",
                $payment->id,
                $payment->sale->buyer->name,
                $payment->amount,
                $payment->sale->status,
                $payment->created_at->format('Y-m-d')
            );
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
