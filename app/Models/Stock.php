<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Stock extends Model
{
    protected $fillable = [
        'stockable_type', 'stockable_id', 'quantity', 'retail_price', 'wholesale_price',
    ];

    /**
     * Get the parent stockable model (Medicine or Product).
     */
    public function stockable()
    {
        return $this->morphTo();
    }

    /**
     * Boot the model to automatically assign UUID to the primary key.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically assign UUID to the model's primary key
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid(); // Generate UUID
            }
        });
    }
}
