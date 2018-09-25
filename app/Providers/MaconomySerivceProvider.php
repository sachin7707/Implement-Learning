<?php

namespace App\Providers;

use App\Maconomy\Client\Maconomy;
use App\Maconomy\Client\SoapClient;
use Illuminate\Support\ServiceProvider;

/**
 * @author jimmiw
 * @since 2018-09-25
 */
class MaconomySerivceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SoapClient::class, function ($app) {
            return new Maconomy(env('MACONOMY_URL'), env('MACONOMY_LOCATION'));
        });
    }
}