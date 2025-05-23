<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuid;
     protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'invoice_id', 'amount', 'method', 'paid_at', 'status', 'reference',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }


}
