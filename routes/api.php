<?php

use App\Http\Controllers\Api\MarketingController;
use App\Http\Controllers\Api\PembayaranController;
use App\Http\Controllers\Api\PenjualanController;
use App\Http\Controllers\Api\PerhitunganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('marketing', [MarketingController::class, 'index']);
Route::get('marketing/{id}', [MarketingController::class, 'show']);
Route::post('marketing', [MarketingController::class, 'store']);
Route::put('marketing/{id}', [MarketingController::class, 'update']);
Route::delete('marketing/{id}', [MarketingController::class, 'destroy']);

Route::get('penjualan', [PenjualanController::class, 'index']);
Route::get('penjualan/{id}', [PenjualanController::class, 'show']);
Route::post('penjualan', [PenjualanController::class, 'store']);
Route::put('penjualan/{id}', [PenjualanController::class, 'update']);
Route::delete('penjualan/{id}', [PenjualanController::class, 'destroy']);

Route::apiResource('perhitungan', PerhitunganController::class);

Route::get('pembayaran', [PembayaranController::class, 'index']);
Route::post('pembayaran', [PembayaranController::class, 'store']);
Route::get('pembayaran/{id}', [PembayaranController::class, 'show']);
Route::get('pembayaran/terhutang', [PembayaranController::class, 'getBelumLunas']);
Route::get('pembayaran/terhutang', [PembayaranController::class, 'getBelumLunas']);
Route::put('pembayaran/{id}', [PembayaranController::class, 'update']);
Route::delete('pembayaran/{id}', [PembayaranController::class, 'destroy']);

