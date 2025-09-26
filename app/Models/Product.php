<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Stock;
use Elastic\ScoutDriverPlus\Searchable;

class Product extends Model
{
    use Searchable;

     public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
        ];
    }

    public function searchableAs(): string
    {
        return 'products_index';
    }

    public function path(): string
    {
        return route('products.show', $this->id);
    }
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'category',
        'description',
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

    public function stock()
    {
        return $this->morphOne(Stock::class, 'stockable');
    }

    public function invoiceItems()
    {
        return $this->morphMany(InvoiceItem::class, 'billable');
    }

    public function purchases()
    {
        return $this->morphToMany(Purchase::class, 'purchasable', 'purchasables')->withPivot(
            'quantity', 'unit_cost', 'subtotal', 'batch_number', 'expiry_date'
        );
    }

    public function saleItems()
    {
        return $this->morphMany(SaleItem::class, 'sellable');
    }

}

