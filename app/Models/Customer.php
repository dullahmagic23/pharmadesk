<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Customer extends Model
{
    use HasUuid;
     protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name','email','phone','address'];
}
