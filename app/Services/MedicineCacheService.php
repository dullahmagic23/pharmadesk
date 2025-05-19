<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Medicine;

class MedicineCacheService
{
    public function all()
    {
        return Cache::remember('medicines_all', now()->addHours(2), function () {
            return Medicine::with('category', 'units','stock')->get();
        });
    }

    // Cache single medicine by ID
    public function find($id)
    {
        return Cache::remember("medicine_{$id}", now()->addHours(2), function () use ($id) {
            return Medicine::with('category', 'manufacturer')->findOrFail($id);
        });
    }

    // Cache names for autocomplete
    public function autocompleteList()
    {
        return Cache::remember('medicines_autocomplete', now()->addHour(), function () {
            return Medicine::select('id', 'name')->get();
        });
    }

    // Clear all related caches (e.g., after create/update/delete)
    public function clear()
    {
        Cache::forget('medicines_all');
        Cache::forget('medicines_autocomplete');
        // Optional: clear individual medicine entries if needed
    }
}
