<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('admin', function ($app) {
            return new \App\Services\Admin\AdminService();
        });
    }

    public function boot()
    {
        //
    }
} 