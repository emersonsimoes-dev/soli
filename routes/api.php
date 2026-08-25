<?php

use App\Http\Controllers\Api\V1\BulletinController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('/bulletins/current', [BulletinController::class, 'current'])
        ->name('bulletins.current');

    Route::get('/bulletins/{year}/{month}', [BulletinController::class, 'show'])
        ->whereNumber('year')
        ->where('month', '0?[1-9]|1[0-2]')
        ->name('bulletins.show');
});
