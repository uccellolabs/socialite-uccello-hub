<?php

namespace UccelloLabs\SocialiteUccelloHub\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'uccello-hub:install';

    protected $description = 'Install the Uccello Hub Socialite provider AI skills';

    public function handle(): int
    {
        $source = __DIR__ . '/../../skills/socialite-uccello-hub';

        $targets = [
            'Claude Code' => base_path('.claude/skills/socialite-uccello-hub'),
            'Cursor'      => base_path('.cursor/rules/socialite-uccello-hub'),
        ];

        $this->components->info('Installing Uccello Hub AI skills...');

        foreach ($targets as $agent => $target) {
            if (is_dir($target)) {
                $this->components->twoColumnDetail($agent, '<fg=yellow>already installed</>');
                continue;
            }

            if (! is_dir(dirname($target))) {
                mkdir(dirname($target), 0755, true);
            }

            $this->copyDirectory($source, $target);

            $this->components->twoColumnDetail($agent, '<fg=green>DONE</>');
        }

        $this->newLine();
        $this->components->info('Uccello Hub skills installed successfully.');

        return self::SUCCESS;
    }

    protected function copyDirectory(string $source, string $target): void
    {
        if (! is_dir($target)) {
            mkdir($target, 0755, true);
        }

        foreach (scandir($source) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            copy($source . DIRECTORY_SEPARATOR . $file, $target . DIRECTORY_SEPARATOR . $file);
        }
    }
}
