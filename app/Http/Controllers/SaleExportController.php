<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class SaleExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        return Excel::download(new SalesExport($request->only(['buyer_name', 'start_date', 'end_date'])), 'sales.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Sale::query()
            ->withCount('items') 
            ->with('buyer');

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        Pdf::setPaper('a4', 'landscape');
        $sales = $query->get();
        $pdf = Pdf::loadView('pdf.sales', ['sales' => $sales]);

        return $pdf->stream('sales.pdf');
    }
}