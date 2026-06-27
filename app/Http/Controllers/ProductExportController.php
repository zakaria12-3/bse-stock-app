<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductExportController extends Controller
{
    public function __invoke()
    {
        return Excel::download(
            new ProductsExport(),
            'stock_workbook_' . now()->format('Y_m_d_His') . '.xlsx'
        );
    }
}
