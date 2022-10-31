<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiHomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/hc-compliance', [ApiHomeController::class, 'getComplianceSeries']);
Route::post('/table-compliance', [ApiHomeController::class, 'getComplianceTable']);
Route::get('/area', [ApiHomeController::class, 'getArea']);
