<?php

namespace UccelloLabs\SocialiteUccelloHub;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class UccelloHubServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $socialite = $this->app->make(Factory::class);

        $socialite->extend('uccello-hub', function ($app) use ($socialite) {
            $config = $app['config']['services.uccello-hub'];

            return $socialite->buildProvider(UccelloHubProvider::class, $config);
        });
    }
}
