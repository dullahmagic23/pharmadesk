<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
class StockHistory extends Model
{
    use HasUlids;
     protected $keyType = 'string';
     public $incrementing = false;
    protected $fillable = ['stock_id', 'quantity', 'date'];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }


}

