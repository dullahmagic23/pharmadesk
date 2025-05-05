<?php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'category',
        'description',
        'price',
        'stock',
        'unit',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}

