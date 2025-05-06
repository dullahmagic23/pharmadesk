<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'price',
    ];

    public function invoiceItems()
    {
        return $this->morphMany(InvoiceItem::class, 'billable');
    }

}
