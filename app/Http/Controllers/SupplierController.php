<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function create()
    {
        return Inertia::render('Suppliers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ]);

        Supplier::create($validated);

        return Redirect::route('suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    public function show(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ]);

        $supplier->update($validated);

        return Redirect::route('suppliers.index')
            ->with('success', 'Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return Redirect::route('suppliers.index')
            ->with('success', 'Supplier deleted successfully!');
    }
}
