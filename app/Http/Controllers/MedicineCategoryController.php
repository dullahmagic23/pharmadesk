<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicineCategory;
use Inertia\Inertia;

class MedicineCategoryController extends Controller
{
    public function index()
    {
        $categories = MedicineCategory::select('id', 'name','description')->orderBy('name')->get();

        return Inertia::render('MedicineCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:medicine_categories,name',
            'description' => 'nullable',
        ]);

        MedicineCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Category added successfully.');
    }

    public function destroy(MedicineCategory $medicine_category)
    {
        $medicine_category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

}
