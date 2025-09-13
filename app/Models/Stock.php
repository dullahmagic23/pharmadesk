<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;

class Stock extends Model
{

     use HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'stockable_type',
        'stockable_id',
        'buying_price',
        'quantity',
        'unit_id',
        'retail_price',
        'wholesale_price',
        'expiration_date',
        'status',
        'location_id',
        'batch_number',
        'expired_at'
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

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->whereNull('expired_at');
    }

    /**
     * Scope: only expired stocks
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expired_at');
    }

    protected static function booted()
    {
        static::addGlobalScope('notExpired', function (Builder $builder) {
            $builder->whereNull('expired_at');
        });
    }

}
