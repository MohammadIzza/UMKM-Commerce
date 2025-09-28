<?php

namespace App\Providers;

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('admin', function ($app) {
            return new AdminMiddleware;
        });
    }

    public function boot()
    {
        //
    }
}