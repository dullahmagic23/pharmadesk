<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Medicine;
use App\Models\Vendor;
use Inertia\Inertia;
use App\Models\MedicineCategory;

class ReportController extends Controller
{
    // Sales Report
    public function sales(Request $request)
    {
        $sales = Sale::with(['items.sellable.stock'])
            ->when($request->from && $request->to, fn($q) => $q->whereBetween('created_at', [$request->from, $request->to]))
            ->latest()
            ->get();

        return Inertia::render('Reports/Sales', [
            'sales' => $sales,
            'filters' => $request->only(['from', 'to']),
        ]);
    }

    // Purchase Report
    public function purchaseReport()
    {
        $purchases = Purchase::with(['vendor', 'purchasables.purchasable.stock'])
            ->latest()
            ->get();

        return Inertia::render('Reports/Purchases', [
            'purchases' => $purchases,
            'vendors' => Vendor::all()
        ]);
    }

    public function medicineReport(Request $request)
    {
        $query = Medicine::with('category', 'stock');

        // Optional category filter
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $medicines = $query->get()->map(function ($med) {
            $soldItems = $med->saleItems()->get();

            $unitsSold = $soldItems->sum('quantity');
            $revenue = $soldItems->sum(fn($item) => $item->quantity * $item->price);
            $cost = $soldItems->sum(fn($item) => $item->quantity * ($med->stock->buying_price ?? 0));
            $profit = $revenue - $cost;

            return [
                'id' => $med->id,
                'name' => $med->name,
                'category' => $med->category?->name,
                'stock_qty' => $med->stock?->quantity ?? 0,
                'buying_price' => $med->stock?->buying_price ?? 0,
                'selling_price' => $med->stock?->selling_price ?? 0,
                'units_sold' => $unitsSold,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
            ];
        });

        return Inertia::render('Reports/Medicines', [
            'medicines' => $medicines,
            'categories' => MedicineCategory::all(),
            'filters' => $request->only('category'),
        ]);
    }

    public function productReport()
    {
        $products = Product::with('stock')->get()->map(function ($prod) {
            $soldItems = $prod->saleItems()->get();

            $unitsSold = $soldItems->sum('quantity');
            $revenue = $soldItems->sum(fn($item) => $item->quantity * $item->price);
            $cost = $soldItems->sum(fn($item) => $item->quantity * ($prod->stock->buying_price ?? 0));
            $profit = $revenue - $cost;

            return [
                'id' => $prod->id,
                'name' => $prod->name,
                'stock_qty' => $prod->stock?->quantity ?? 0,
                'buying_price' => $prod->stock?->buying_price ?? 0,
                'selling_price' => $prod->stock?->selling_price ?? 0,
                'units_sold' => $unitsSold,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
            ];
        });

        return Inertia::render('Reports/ProductsReport', [
            'products' => $products,
        ]);
    }

}
