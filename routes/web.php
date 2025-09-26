<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('rooms.index'));
Route::resource('rooms', RoomController::class);

