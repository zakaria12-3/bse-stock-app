<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ProductsImport;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls'
        ]);

        $result = (new ProductsImport())->import($request->file('file')->getRealPath());

        return response()->json([
            'message' => 'Import termine avec succes',
            'sheets' => $result['sheets'],
            'products' => $result['products'],
        ]);
    }
}
