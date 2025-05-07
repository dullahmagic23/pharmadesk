<?php
namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchasables()
    {
        return $this->hasMany(Purchasable::class);
    }

    public function products()
    {
        return $this->purchasables()->where('purchasable_type', Product::class);
    }

    public function medicines()
    {
        return $this->purchasables()->where('purchasable_type', Medicine::class);
    }
}
