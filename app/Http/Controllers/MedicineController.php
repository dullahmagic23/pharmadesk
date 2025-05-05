<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::with([
            'category' => function ($query) {
                $query->select('id', 'name');
            },
            'units' => function ($query) {
                $query->select('id', 'unit_name');
            }
        ])->get();
        return Inertia::render('Medicines/Index', [
            'medicines' => $medicines,
        ]);
    }

    public function create()
    {
        $categories = MedicineCategory::all();
        return Inertia::render('Medicines/Create', [
            'categories' => $categories,
            'units' => MedicineUnit::all()
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'medicine_category_id' => 'required|exists:medicine_categories,id',
            'description' => 'nullable|string',
            'units' => 'required|array|min:1',
            'units.*.medicine_unit_id' => [
                'required',
                'string',
                Rule::exists('medicine_units', 'id'),
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $medicine = Medicine::create([
                'name' => $validated['name'],
                'brand' => $validated['brand'],
                'medicine_category_id' => $validated['medicine_category_id'],
                'description' => $validated['description'] ?? null,
            ]);

            // Attach units
            $unitIds = collect($validated['units'])->pluck('medicine_unit_id')->toArray();
            $medicine->units()->sync($unitIds); // assuming belongsToMany
        });
        return redirect()->back()->with('success', 'Medicine created successfully.');
    }


    public function show(Medicine $medicine)
    {
        return Inertia::render('Medicines/Show', [
            'medicine' => $medicine
        ]);
    }

    public function edit(Medicine $medicine)
    {
        $medicine->load(['category:id,name', 'units:id,unit_name']);
        return Inertia::render('Medicines/Edit', [
            'medicine' => $medicine,
            'categories' => MedicineCategory::select('id', 'name')->get(),
            'units' => MedicineUnit::select('id', 'unit_name')->get(),
        ]);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'medicine_category_id' => 'required|exists:medicine_categories,id',
            'unit_ids' => 'array',
            'unit_ids.*' => 'exists:medicine_units,id',
        ]);

        $medicine->update([
            'name' => $data['name'],
            'brand' => $data['brand'],
            'medicine_category_id' => $data['medicine_category_id'],
        ]);

        $medicine->units()->sync($data['unit_ids'] ?? []);

        return redirect()->route('medicines.index')->with('success', 'Medicine updated successfully.');
    }
    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return redirect()->route('medicines.index')->with('success', 'Medicine deleted successfully.');
    }
}
