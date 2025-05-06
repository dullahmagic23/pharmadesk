<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Dosage extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;
}
