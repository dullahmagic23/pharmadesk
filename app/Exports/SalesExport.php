<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesExport implements FromView
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $sales = Sale::query()
            ->when($this->filters['start_date'], fn($q) => $q->whereDate('created_at', '>=', $this->filters['start_date']))
            ->when($this->filters['end_date'], fn($q) => $q->whereDate('created_at', '<=', $this->filters['end_date']))
            ->withCount('items')
            ->get();

        return view('exports.sales', ['sales' => $sales]);
    }
}
