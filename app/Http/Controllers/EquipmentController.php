<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class EquipmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Equipment/Index', [
            'equipment' => Equipment::with('supplier')->latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Equipment/Create', [
            'suppliers' => Supplier::select('id', 'name')->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,maintenance',
            'price' => 'nullable|numeric|min:0',
        ]);

        Equipment::create($validated);

        return Redirect::route('equipments.index')
            ->with('success', 'Equipment added successfully');
    }

    public function edit(Equipment $equipment)
    {
        return Inertia::render('Equipment/Edit', [
            'equipment' => $equipment,
            'suppliers' => Supplier::select('id', 'name')->orderBy('name')->get(),
            'isEdit' => true
        ]);
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,maintenance',
            'price' => 'nullable|numeric|min:0',
        ]);

        $equipment->update($validated);

        return Redirect::route('equipments.index')
            ->with('success', 'Equipment updated successfully');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return Redirect::route('equipments.index')
            ->with('success', 'Equipment deleted successfully');
    }
}
