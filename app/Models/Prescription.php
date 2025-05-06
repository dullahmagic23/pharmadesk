<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Prescription extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];


    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'medicine_prescriptions')
            ->withPivot('id', 'dosage_id')
            ->withTimestamps();
    }

    public function dosages()
    {
        return $this->belongsToMany(Dosage::class, 'medicine_prescriptions')
            ->withPivot('id', 'medicine_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }

}
