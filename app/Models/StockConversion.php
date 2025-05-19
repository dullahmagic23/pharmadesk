<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class StockConversion extends Model
{
    use HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded =[];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
