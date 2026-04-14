<?php

namespace UccelloLabs\SocialiteUccelloHub;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use UccelloLabs\SocialiteUccelloHub\Console\InstallCommand;

class UccelloHubServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $socialite = $this->app->make(Factory::class);

        $socialite->extend('uccello-hub', function ($app) use ($socialite) {
            $config = $app['config']['services.uccello-hub'];

            return $socialite->buildProvider(UccelloHubProvider::class, $config);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/boost/skills' => base_path('.claude/skills'),
            ], 'claude-skills');

            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
