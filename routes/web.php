<?php
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('rooms.index'));

Route::resource('rooms', RoomController::class);

// Nueva: setear ocupación de una sala
Route::patch('/rooms/{room}/occupancy', [RoomController::class, 'setOccupancy'])
    ->name('rooms.setOccupancy');
    