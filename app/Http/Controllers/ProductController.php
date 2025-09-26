<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use Inertia\Inertia;
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name','asc')->get(); // You can paginate if you prefer
        return Inertia::render('Products/Index', [
            'products' => $products
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Create',[
            'product' => []
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
        ]);

        Product::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'unit' => $request->unit,
        ]);

        return redirect()->back()->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return Inertia::render('Products/Show', [
            'product' => $product->load('saleItems','stock'),
        ]);
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return Inertia::render('Products/Create', [
            'product' => $product
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'unit' => $request->unit,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

}
