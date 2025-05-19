<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Customer;

class CustomerCacheService
{
    public function all()
    {
        return Cache::remember('Customers_all', now()->addHours(2), function () {
            return Customer::all();
        });
    }

    // Cache single Customer by ID
    public function find($id)
    {
        return Cache::remember("Customer_{$id}", now()->addHours(2), function () use ($id) {
            return Customer::with('category', 'manufacturer')->findOrFail($id);
        });
    }

    // Cache names for autocomplete
    public function autocompleteList()
    {
        return Cache::remember('Customers_autocomplete', now()->addHour(), function () {
            return Customer::select('id', 'name')->get();
        });
    }

    // Clear all related caches (e.g., after create/update/delete)
    public function clear()
    {
        Cache::forget('Customers_all');
        Cache::forget('Customers_autocomplete');
        // Optional: clear individual Customer entries if needed
    }
}
