<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/{church:slug}', [HomeController::class, 'show'])
    ->where('church', '^(?!admin$|up$|livewire$|storage$|api$)[A-Za-z0-9\-]+')
    ->name('church.home');
