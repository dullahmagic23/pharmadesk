<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Traits\HasUuid;

class Stock extends Model
{

     use HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'stockable_type', 'stockable_id', 'quantity', 'unit_id','retail_price', 'wholesale_price',
    ];

    /**
     * Get the parent stockable model (Medicine or Product).
     */
    public function stockable()
    {
        return $this->morphTo();
    }

    public function histories():HasMany
    {
        return $this->hasMany(StockHistory::class);
    }

    public function unit()
    {
        return $this->hasOne(MedicineUnit::class, 'id', 'unit_id');
    }

}
