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

        $this->publishes([
            __DIR__ . '/../skills' => base_path('.claude/skills'),
        ], 'claude-skills');

        $this->installClaudeSkill();
    }

    protected function installClaudeSkill(): void
    {
        $target = base_path('.claude/skills/socialite-uccello-hub/SKILL.md');

        if (file_exists($target)) {
            return;
        }

        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }

        copy(__DIR__ . '/../skills/socialite-uccello-hub/SKILL.md', $target);
    }
}
