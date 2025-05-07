<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasUuid;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['bill_id', 'title', 'amount', 'expense_date', 'notes'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

}
