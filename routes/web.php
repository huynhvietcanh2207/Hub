<?php

use App\Http\Controllers\HubController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HubController::class, 'index'])->name('hub.index');
Route::get('/admin', [HubController::class, 'admin'])->name('hub.admin');
Route::post('/websites', [HubController::class, 'store'])->name('websites.store');
Route::put('/websites/{website}', [HubController::class, 'update'])->name('websites.update');
Route::delete('/websites/{website}', [HubController::class, 'destroy'])->name('websites.destroy');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
