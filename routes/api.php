<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

Route::post('/import-products', [ImportController::class, 'import']);
