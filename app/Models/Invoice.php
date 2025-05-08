<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'patient_id', 'invoice_number','invoice_date', 'balance', 'total', 'status',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

}
