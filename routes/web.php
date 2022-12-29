<?php

use Illuminate\Support\Facades\Route;
use Appkeep\Laravel\Http\Controllers\ExploreController;

Route::get('/appkeep/explore', ExploreController::class)->name('appkeep.explore');
