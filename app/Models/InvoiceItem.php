<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'invoice_id', 'billable_id', 'billable_type', 'quantity', 'unit_price', 'total_price','price_type',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function billable()
    {
        return $this->morphTo();
    }

}
