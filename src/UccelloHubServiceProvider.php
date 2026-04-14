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

            $this->installSkills();
        }
    }

    protected function installSkills(): void
    {
        $source = __DIR__ . '/../resources/boost/skills/socialite-uccello-hub';

        foreach ([
            base_path('.claude/skills/socialite-uccello-hub'),
            base_path('.cursor/rules/socialite-uccello-hub'),
        ] as $target) {
            if (is_dir($target)) {
                continue;
            }

            if (! is_dir(dirname($target))) {
                mkdir(dirname($target), 0755, true);
            }

            mkdir($target, 0755, true);

            foreach (scandir($source) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                copy($source . DIRECTORY_SEPARATOR . $file, $target . DIRECTORY_SEPARATOR . $file);
            }
        }
    }
}
