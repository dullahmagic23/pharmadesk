<?php

// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasUuid;

class Patient extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;
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

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
