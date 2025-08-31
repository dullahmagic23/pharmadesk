<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SaleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->date,
            'buyer_name' => $this->buyer->name,
            'buyer_type' => $this->buyer_type,
            'items_count' => $this->items->count(),
            'total' => $this->total,
            'paid' => $this->paid,
            'balance' => $this->total - $this->paid,
            'status' => $this->paid >= $this->total ? 'paid' :
                ($this->paid > 0 ? 'partial' : 'unpaid'),
        ];
    }
}
