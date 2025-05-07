<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['amount', 'billing_date', 'status'];
    protected $appends = ['total_paid','type'];

    public function billable()
    {
        return $this->morphTo();
    }

    public function payments()
    {
        return $this->hasMany(BillPayment::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getTypeAttribute()
    {
        return $this->billable_type === 'App\\Models\\Supplier'?'Supplier':'Vendor';
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }


}
