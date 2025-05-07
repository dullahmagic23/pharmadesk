<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BillPaymentController extends Controller
{
    public function index()
    {
        $payments = BillPayment::with('bill.billable')->get();
        return Inertia::render('BillPayments/Index', [
            'payments' => $payments
        ]);
            
    }

    public function create()
    {
        $bills = Bill::with('billable')->get();
        return Inertia::render('BillPayments/Create',[
            'bills' => $bills
        ]);
    }
    public function store(Request $request)
    {
        $bill = Bill::find($request['bill_id']);

        $data = $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'amount' => 'required|numeric|min:0.01|max:' . ($bill->amount - $bill->payments()->sum('amount')),
            'payment_date' => 'required|date',
        ]);

        $bill->payments()->create([
            'id' => Str::uuid(),
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
        ]);
        $bill->expenses()->create([
            'id'=> Str::uuid(),
            'amount'=> $data['amount'],
            'expense_date'=> $data['payment_date'],
            'title' => $bill->billable->name
        ]);
        $bill->refresh();
        $total_paid = $bill->payments()->sum('amount');
        if($bill->amount <= $total_paid){
            $bill->status = 'paid';
            $bill->save();
        }
        return redirect()->route('bill-payments.index')->with('success', 'Payment recorded.');
    }
}