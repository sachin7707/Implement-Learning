<?php

namespace App\Providers;

use App\Newsletter\NewsletterService;
use App\Newsletter\SignupService;
use Illuminate\Support\ServiceProvider;

/**
 * Adds the newsletter service to the app
 * @author jimmiw
 * @since 2019-02-06
 */
class NewsletterServiceProvider extends ServiceProvider
{
    public function register()
    {
        // registering our client
        $this->app->singleton(NewsletterService::class, function ($app) {
            return new SignupService(env('NEWSLETTER_SIGNUP_URL'));
        });
    }
}
