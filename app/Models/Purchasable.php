<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Purchasable extends Model
{
    use HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'purchase_id',
        'purchasable_id',
        'purchasable_type',
        'quantity',
        'unit_cost',
        'subtotal',
        'batch_number',
        'expiry_date'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchasable()
    {
        return $this->morphTo();
    }
}
