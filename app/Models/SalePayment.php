<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class SalePayment extends Model
{
    use HasUuid;
     protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'sale_id','amount', 'paid_at',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
