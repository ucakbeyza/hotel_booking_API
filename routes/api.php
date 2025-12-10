<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);
Route::post('/hotels/create', [HotelController::class, 'create']);
Route::post('/hotels/delete', [HotelController::class, 'delete']);
Route::post('/hotels/update', [HotelController::class, 'update']);