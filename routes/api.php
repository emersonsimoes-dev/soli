<?php

use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\BulletinController;
use App\Http\Controllers\Api\V1\ChurchController;
use App\Http\Controllers\Api\V1\ContributionController;
use App\Http\Controllers\Api\V1\MemberController;
use App\Http\Controllers\Api\V1\RosterEntryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('/churches', [ChurchController::class, 'index'])->name('churches.index');
    Route::get('/churches/current', [ChurchController::class, 'current'])->name('churches.current');
    Route::get('/churches/{church:slug}', [ChurchController::class, 'show'])->name('churches.show');

    Route::get('/bulletins/current', [BulletinController::class, 'current'])
        ->name('bulletins.current');

    Route::get('/bulletins/{year}/{month}', [BulletinController::class, 'show'])
        ->whereNumber('year')
        ->where('month', '0?[1-9]|1[0-2]')
        ->name('bulletins.show');

    Route::prefix('churches/{church:slug}')->group(function (): void {
        Route::get('/bulletins/current', [BulletinController::class, 'currentForChurch'])
            ->name('church.bulletins.current');

        Route::get('/bulletins/{year}/{month}', [BulletinController::class, 'showForChurch'])
            ->whereNumber('year')
            ->where('month', '0?[1-9]|1[0-2]')
            ->name('church.bulletins.show');

        Route::get('/announcements', [AnnouncementController::class, 'index'])
            ->name('church.announcements.index');

        Route::get('/roster', [RosterEntryController::class, 'index'])
            ->name('church.roster.index');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get('/members', [MemberController::class, 'index'])
                ->name('church.members.index');

            Route::get('/contributions', [ContributionController::class, 'index'])
                ->name('church.contributions.index');
        });
    });
});
