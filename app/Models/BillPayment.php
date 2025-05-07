<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    protected $table = "bill_payments";
    use HasUuid;
    protected $fillable = [
        'bill_id','amount','payment_date'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function bill()
    {
        return $this->belongsTo(Bill::class,'bill_id','id');
    }
}
