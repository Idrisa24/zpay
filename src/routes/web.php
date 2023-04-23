<?php

use Illuminate\Support\Facades\Route;
use Saidtech\Zpay\Http\Controllers\ZpayController;

Route::get('/pay', [ZpayController::class,'show'])->middleware('web')->name('zpay.payment-token');

Route::post('/pay', [ZpayController::class,'pay'])->middleware('web')->name('zpay.pay');
