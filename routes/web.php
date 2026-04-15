<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WeatherController::class, 'dashboard'])->name('weather.dashboard');
Route::post('/weather/fetch', [WeatherController::class, 'fetch'])->name('weather.fetch');
Route::get('/weather/history', [WeatherController::class, 'history'])->name('weather.history');
Route::get('/weather/{weatherLog}', [WeatherController::class, 'detail'])->name('weather.detail');
Route::get('/status', [WeatherController::class, 'status'])->name('status');
