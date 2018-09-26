<?php

namespace App\Providers;

use App\Wordpress\Client;
use Illuminate\Support\ServiceProvider;

/**
 * @author jimmiw
 * @since 2018-09-26
 */
class WordpressServiceProvider extends ServiceProvider
{
    public function register()
    {
        // registering our client
        $this->app->singleton(Client::class, function ($app) {
            return new Client(env('WORDPRESS_API_URL'));
        });
    }
}