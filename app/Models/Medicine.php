<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicine extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'brand',
        'category',
        'price',
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
}
