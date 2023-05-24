<?php

namespace Auth\Ocr\Google;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class GoogleOcrServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register() : void
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() : void
    {
        //web route
        Route::middleware('web')
        ->group(__DIR__.'/routes/web.php');

        //route add
        Route::middleware('api')
        ->prefix('api')
        ->group(__DIR__.'/routes/api.php');

        //resource view add
        view()->addNamespace('View', __DIR__.'/resources/views');

        //config add
        app('config')->set('google', require __DIR__.'/config/google.php');

        //middleware add
        app('router')->aliasMiddleware('api.ocrToken', \Auth\Ocr\Google\App\Http\Middleware\ApiOcrToken::class);
    }
}