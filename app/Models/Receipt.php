<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Receipt extends Model
{
    use HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'sale_id',
        'amount',
        'issued_at',
        'issued_by',
        'reference',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
