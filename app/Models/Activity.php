<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = "activity_log";
    protected $guarded = [];

     protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
