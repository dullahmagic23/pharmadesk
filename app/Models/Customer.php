<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use Elastic\ScoutDriverPlus\Searchable;

class Customer extends Model
{
    use HasFactory;
    use HasUuid;
    use Searchable;

    use Searchable;

     public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    public function searchableAs(): string
    {
        return 'customers_index';
    }

    public function path(): string
    {
        return route('customers.show', $this->id);
    }
     protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name','email','phone','address'];


    public function sales()
    {
        return $this->morphMany(Sale::class, 'buyer');
    }
}
