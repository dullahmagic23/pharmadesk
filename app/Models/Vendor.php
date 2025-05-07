<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'contact_person', 'email', 'phone', 'address'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
