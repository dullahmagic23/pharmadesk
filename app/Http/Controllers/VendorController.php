<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->paginate(10);
        return inertia('Vendors/Index', compact('vendors'));
    }

    public function create()
    {
        return inertia('Vendors/Create');
    }

    public function store(Request $request)
    {
//        dd($request->all());
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        Vendor::create($data);

        return redirect()->route('vendors.index')->with('success', 'Vendor created successfully.');
    }

    public function show(Vendor $vendor)
    {
        return inertia('Vendors/Show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        return inertia('Vendors/Edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $vendor->update($data);

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully.');
    }
}

