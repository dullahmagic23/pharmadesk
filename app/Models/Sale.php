<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['buyer_id','buyer_type', 'total', 'paid', 'balance','date'];
    protected $appends = ['type'];


    public function buyer()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }
    public function getTypeAttribute()
    {
        return $this->buyer_type === 'App\\Models\\Customer'?'Customer':'Patient';
    }
}
