<?php
use Illuminate\Support\Facades\Route;

Route::middleware('api.ocrToken')->prefix('ocr/google')->group(function() : void {

    Route::get('/ocr', [\Auth\Ocr\Google\App\Http\Controllers\OcrController::class, 'index']);

});