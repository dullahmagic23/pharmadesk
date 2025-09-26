<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Medicine;
use App\Models\Product;
use App\Models\Customer;

class SearchController extends Controller
{
    private const CACHE_TTL = 300; // 5 minutes
    private const MAX_RESULTS_PER_TYPE = 5;
    private const MIN_QUERY_LENGTH = 2;
    private const MAX_QUERY_LENGTH = 100;

    /**
     * Search across multiple models with caching and optimizations
     */
    public function search(Request $request): JsonResponse
    {
        $query = $this->sanitizeQuery($request->input('q'));

        if (!$this->isValidQuery($query)) {
            return response()->json([]);
        }

        // Use caching for better performance
        $cacheKey = 'search:' . md5($query);

        $results = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query) {
            return $this->performSearch($query);
        });

        return response()->json($results);
    }

    /**
     * Advanced search with filters and sorting
     */
    public function advancedSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:' . self::MIN_QUERY_LENGTH . '|max:' . self::MAX_QUERY_LENGTH,
            'type' => 'sometimes|string|in:medicine,product,customer,all',
            'limit' => 'sometimes|integer|min:1|max:50',
            'sort' => 'sometimes|string|in:relevance,name,created_at',
            'order' => 'sometimes|string|in:asc,desc'
        ]);

        $query = $this->sanitizeQuery($validated['q']);
        $type = $validated['type'] ?? 'all';
        $limit = $validated['limit'] ?? self::MAX_RESULTS_PER_TYPE;
        $sort = $validated['sort'] ?? 'relevance';
        $order = $validated['order'] ?? 'asc';

        $cacheKey = 'advanced_search:' . md5(serialize($validated));

        $results = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query, $type, $limit, $sort, $order) {
            return $this->performAdvancedSearch($query, $type, $limit, $sort, $order);
        });

        return response()->json([
            'data' => $results,
            'meta' => [
                'query' => $query,
                'total_results' => count($results),
                'search_type' => $type
            ]
        ]);
    }

    /**
     * Get search suggestions based on partial input
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $this->sanitizeQuery($request->input('q'));

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $cacheKey = 'suggestions:' . md5($query);

        $suggestions = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query) {
            return $this->getSuggestions($query);
        });

        return response()->json($suggestions);
    }

    /**
     * Perform the main search operation
     */
    private function performSearch(string $query): array
    {
        $results = collect();

        // Search medicines with additional fields
        $medicines = $this->searchMedicines($query, self::MAX_RESULTS_PER_TYPE);
        $results = $results->merge($medicines);

        // Search products with additional fields
        $products = $this->searchProducts($query, self::MAX_RESULTS_PER_TYPE);
        $results = $results->merge($products);

        // Search customers with additional fields
        $customers = $this->searchCustomers($query, self::MAX_RESULTS_PER_TYPE);
        $results = $results->merge($customers);

        return $results->values()->toArray();
    }

    /**
     * Perform advanced search with filters
     */
    private function performAdvancedSearch(string $query, string $type, int $limit, string $sort, string $order): array
    {
        $results = collect();

        if ($type === 'all' || $type === 'medicine') {
            $medicines = $this->searchMedicines($query, $limit, $sort, $order);
            $results = $results->merge($medicines);
        }

        if ($type === 'all' || $type === 'product') {
            $products = $this->searchProducts($query, $limit, $sort, $order);
            $results = $results->merge($products);
        }

        if ($type === 'all' || $type === 'customer') {
            $customers = $this->searchCustomers($query, $limit, $sort, $order);
            $results = $results->merge($customers);
        }

        // Sort by relevance if specified
        if ($sort === 'relevance') {
            $results = $this->sortByRelevance($results, $query);
        }

        return $results->take($limit)->values()->toArray();
    }

    /**
     * Search medicines with enhanced query
     */
    private function searchMedicines(string $query, int $limit, string $sort = 'name', string $order = 'asc'): Collection
    {
        $queryBuilder = Medicine::query()
            ->select('id', 'name', 'description', 'created_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });

        if ($sort === 'name') {
            $queryBuilder->orderBy('name', $order);
        } elseif ($sort === 'created_at') {
            $queryBuilder->orderBy('created_at', $order);
        }

        return $queryBuilder->limit($limit)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'type' => 'Medicine',
                'name' => $m->name,
                'description' => $m->description ? substr($m->description, 0, 100) . '...' : null,
                'url' => route('medicines.show', $m->id),
                'relevance_score' => $this->calculateRelevance($m->name, $query),
            ]);
    }

    /**
     * Search products with enhanced query
     */
    private function searchProducts(string $query, int $limit, string $sort = 'name', string $order = 'asc'): Collection
    {
        $queryBuilder = Product::query()
            ->select('id', 'name', 'description', 'category', 'created_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            });

        if ($sort === 'name') {
            $queryBuilder->orderBy('name', $order);
        } elseif ($sort === 'created_at') {
            $queryBuilder->orderBy('created_at', $order);
        }

        return $queryBuilder->limit($limit)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'type' => 'Product',
                'name' => $p->name,
                'description' => $p->description ? substr($p->description, 0, 100) . '...' : null,
                'category' => $p->category,
                'url' => route('products.show', $p->id),
                'relevance_score' => $this->calculateRelevance($p->name, $query),
            ]);
    }

    /**
     * Search customers with enhanced query
     */
    private function searchCustomers(string $query, int $limit, string $sort = 'name', string $order = 'asc'): Collection
    {
        $queryBuilder = Customer::query()
            ->select('id', 'name', 'email', 'phone', 'created_at')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            });

        if ($sort === 'name') {
            $queryBuilder->orderBy('name', $order);
        } elseif ($sort === 'created_at') {
            $queryBuilder->orderBy('created_at', $order);
        }

        return $queryBuilder->limit($limit)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'type' => 'Customer',
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone,
                'url' => route('customers.show', $c->id),
                'relevance_score' => $this->calculateRelevance($c->name, $query),
            ]);
    }

    /**
     * Get search suggestions
     */
    private function getSuggestions(string $query): array
    {
        $suggestions = collect();

        // Get medicine names
        $medicineNames = Medicine::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->pluck('name');
        $suggestions = $suggestions->merge($medicineNames);

        // Get product names
        $productNames = Product::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->pluck('name');
        $suggestions = $suggestions->merge($productNames);

        // Get customer names
        $customerNames = Customer::where('name', 'like', "%{$query}%")
            ->limit(2)
            ->pluck('name');
        $suggestions = $suggestions->merge($customerNames);

        return $suggestions->unique()->take(8)->values()->toArray();
    }

    /**
     * Sanitize the search query
     */
    private function sanitizeQuery(?string $query): string
    {
        if (!$query) {
            return '';
        }

        // Remove special characters that could cause issues
        $sanitized = preg_replace('/[^\p{L}\p{N}\s\-_.]/u', '', $query);

        // Trim and normalize spaces
        return trim(preg_replace('/\s+/', ' ', $sanitized));
    }

    /**
     * Check if query is valid for search
     */
    private function isValidQuery(string $query): bool
    {
        return strlen($query) >= self::MIN_QUERY_LENGTH &&
               strlen($query) <= self::MAX_QUERY_LENGTH;
    }

    /**
     * Calculate relevance score for sorting
     */
    private function calculateRelevance(string $text, string $query): float
    {
        $text = strtolower($text);
        $query = strtolower($query);

        // Exact match gets highest score
        if ($text === $query) {
            return 1.0;
        }

        // Starts with query gets high score
        if (str_starts_with($text, $query)) {
            return 0.9;
        }

        // Contains query gets medium score
        if (str_contains($text, $query)) {
            return 0.7;
        }

        // Similar_text comparison for fuzzy matching
        similar_text($query, $text, $percent);
        return $percent / 100;
    }

    /**
     * Sort results by relevance score
     */
    private function sortByRelevance(Collection $results, string $query): Collection
    {
        return $results->sortByDesc('relevance_score');
    }

    /**
     * Clear search cache (useful for admin operations)
     */
    public function clearCache(): JsonResponse
    {
        Cache::flush(); // Or use more specific cache clearing
        return response()->json(['message' => 'Search cache cleared successfully']);
    }
}
