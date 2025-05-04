<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Supplier extends Model
{
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
