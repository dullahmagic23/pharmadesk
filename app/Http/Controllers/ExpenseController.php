<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Bill;

class ExpenseController extends Controller
{
    public function index()
    {
        return inertia('Expenses/Index', [
            'expenses' => Expense::with('bill')->latest()->get()
        ]);
    }

    public function create()
    {
        return inertia('Expenses/Create', [
            'bills' => Bill::where('status','unpaid')->with('billable')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'bill_id' => 'nullable|exists:bills,id',
            'notes' => 'nullable|string',
        ]);

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

}
