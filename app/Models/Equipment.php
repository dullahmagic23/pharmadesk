<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    /** @use HasFactory<\Database\Factories\EquipmentFactory> */
    use HasFactory;

    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'supplier_id',
        'name',
        'serial_number',
        'manufacturer',
        'purchase_date',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
