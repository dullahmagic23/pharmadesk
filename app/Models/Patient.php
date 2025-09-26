<?php

// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasUuid;

class Patient extends Model
{
    use HasFactory,HasUuid;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $appends = ['name'];
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'address',
    ];

    protected $dates = ['date_of_birth'];

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function sales()
    {
        return $this->morphMany(Sale::class, 'buyer');
    }
}
