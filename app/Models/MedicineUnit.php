<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MedicineUnit extends Model
{
    use HasFactory;
    protected $table = 'medicine_units';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'medicine_id',
        'unit_name',
        'retail_price',
        'wholesale_price',
        'wholesale_min_quantity',
    ];

    protected static function booted(): void
    {
        static::creating(function ($unit) {
            $unit->id = (string) Str::uuid();
        });
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class);
    }
}
