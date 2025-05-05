<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Stock;

class Medicine extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'brand',
        'medicine_category_id',
        'description',
    ];
     protected $keyType = 'string';
    public $incrementing = false;

    // Generate UUID for new records
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid(); // Automatically assign UUID when creating a record
        });
    }

    public function units()
    {
        return $this->belongsToMany(MedicineUnit::class, 'medicine_medicine_unit', 'medicine_id', 'medicine_unit_id');
    }
    public function category()
    {
        return $this->belongsTo(MedicineCategory::class, 'medicine_category_id', 'id');
    }

    public function stocks()
    {
        return $this->morphMany(Stock::class, 'stockable');
    }
}
