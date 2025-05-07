<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;

    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone_number',
        'address',
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function bills()
    {
        return $this->morphMany(Bill::class, 'billable');
    }

}
