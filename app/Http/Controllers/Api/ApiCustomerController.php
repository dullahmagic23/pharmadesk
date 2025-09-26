<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApiCustomerController extends Controller
{
    public function index()
    {
        return Customer::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $customer = Customer::create($request->all());
        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        return Inertia::render('Customers/Show', [
            'customer' => $customer->load('sales.items.sellable')]);
    }
}
