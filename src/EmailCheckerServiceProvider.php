<?php

namespace Aman\EmailVerifier;

use Illuminate\Support\ServiceProvider;

class EmailCheckerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('emailchecker', function() {
            return new EmailChecker;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }

     /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['emailchecker'];
    }
}
