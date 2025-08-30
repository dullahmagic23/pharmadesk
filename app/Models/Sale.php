<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['buyer_id', 'buyer_type', 'total', 'paid', 'balance', 'date','user_id','customer_id'];
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
        return $this->buyer_type === 'App\\Models\\Customer' ? 'Customer' : 'Patient';
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
