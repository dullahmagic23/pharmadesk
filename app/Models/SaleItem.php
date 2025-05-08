<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id',
        'sellable_id',
        'sellable_type',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $appends = ['type'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function sellable()
    {
        return $this->morphTo();
    }

    public function getTypeAttribute()
    {
        return $this->sellable_type === 'App\Models\Product'?'Product':'Medicine';
    }
}
