<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerCacheService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(CustomerCacheService $customerCache)
    {
        $customerCache->all();
        return Inertia::render('Customers/Index', [
            'customers' => Customer::latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Customers/Create');
    }

    public function store(Request $request, CustomerCacheService $customerCache)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        Customer::create($data);
        $customerCache->clear();
        $customerCache->all();
        return redirect()->back()->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        return Inertia::render('Customers/Show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer,CustomerCacheService $customerCache)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $customer->update($data);
        $customerCache->clear();
        $customerCache->all();
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
