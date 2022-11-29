<?php

use Illuminate\Support\Facades\Route;
use Appkeep\Laravel\Http\Controllers\InsightsController;

Route::get('/appkeep/insights', InsightsController::class)->name('appkeep.insights');
