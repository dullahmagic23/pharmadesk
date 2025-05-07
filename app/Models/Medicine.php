<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicine extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'brand',
        'medicine_category_id',
        'description',
    ];
     protected $keyType = 'string';
    public $incrementing = false;

    // Generate UUID for new records
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid(); // Automatically assign UUID when creating a record
        });
    }

    public function stock()
    {
        return $this->morphOne(Stock::class, 'stockable');
    }

    public function units()
    {
        return $this->belongsToMany(MedicineUnit::class, 'medicine_medicine_unit', 'medicine_id', 'medicine_unit_id');
    }
    public function category()
    {
        return $this->belongsTo(MedicineCategory::class, 'medicine_category_id', 'id');
    }

    public function stocks()
    {
        return $this->morphMany(Stock::class, 'stockable');
    }

    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'medicine_prescriptions')
            ->withPivot('id', 'dosage_id')
            ->withTimestamps();
    }
    public function dosages()
    {
        return $this->belongsToMany(Dosage::class, 'medicine_prescriptions')
            ->withPivot('id', 'prescription_id');
    }

    public function invoiceItems()
    {
        return $this->morphMany(InvoiceItem::class, 'billable');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function purchases()
    {
        return $this->morphToMany(Purchase::class, 'purchasable', 'purchasables')->withPivot(
            'quantity', 'unit_cost', 'subtotal', 'batch_number', 'expiry_date'
        );
    }
}
