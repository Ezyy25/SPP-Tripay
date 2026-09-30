<?php

use App\Http\Controllers\TripayCallbackController;

Route::post('/tripay/callback', [TripayCallbackController::class, 'handle']);