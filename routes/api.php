<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{id}', [HotelController::class, 'show']);
Route::post('/hotels/create', [HotelController::class, 'create']);
Route::post('/hotels/delete', [HotelController::class, 'delete']);
Route::post('/hotels/update', [HotelController::class, 'update']);

Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::post('/rooms/create', [RoomController::class, 'create']);
Route::post('/rooms/delete', [RoomController::class, 'delete']);
Route::post('/rooms/update', [RoomController::class, 'update']);  