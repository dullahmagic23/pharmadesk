<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Vendor;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with('billable')->latest()->get();
        return inertia('Bills/Index', compact('bills'));
    }

    public function create()
    {
        $vendors = Vendor::all();
        $suppliers = Supplier::all();
        return inertia('Bills/Create', compact('vendors', 'suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'billable_type' => 'required|in:vendor,supplier',
            'billable_id' => 'required|uuid',
            'amount' => 'required|numeric|min:0.01',
            'billing_date' => 'required|date',
        ]);

        $model = $data['billable_type'] === 'vendor'
            ? Vendor::findOrFail($data['billable_id'])
            : Supplier::findOrFail($data['billable_id']);

        $model->bills()->create([
            'id' => Str::uuid(),
            'amount' => $data['amount'],
            'billing_date' => $data['billing_date'],
        ]);

        return redirect()->route('bills.index')->with('success', 'Bill created successfully.');
    }

    public function show(Bill $bill)
    {
        $bill->load('billable', 'payments');
        return inertia('Bills/Show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $vendors = Vendor::all();
        $suppliers = Supplier::all();
        return inertia('Bills/Edit', compact('bill', 'vendors', 'suppliers'));
    }

    public function update(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'billing_date' => 'required|date',
            'status' => 'in:unpaid,partial,paid',
        ]);

        $bill->update($data);

        return redirect()->route('bills.index')->with('success', 'Bill updated successfully.');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Bill deleted.');
    }
}

