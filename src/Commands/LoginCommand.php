<?php

namespace Appkeep\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Appkeep\Laravel\Commands\Concerns\VerifiesProjectKey;

class LoginCommand extends Command
{
    use VerifiesProjectKey;

    protected $signature = 'appkeep:login';
    protected $description = 'Sign in/register to Appkeep and create a project key.';

    public function handle()
    {
        if (config('appkeep.key')) {
            $this->info('You already have a project key set.');

            return;
        }

        $this->logo();

        $this->line('Welcome to App:keep 🎉');
        $this->line('We are happy to see you\'ve made this far. Let\'s get you set up.');

        $choice = $this->choice('How should we begin?', [
            'Sign up and create a new project.',
            'I\'ve got an existing project. I want to enter my key.',
        ], 0);

        $choice == 'Sign up and create a new project.'
            ? $this->signUpAndCreateProject()
            : $this->enterProjectKey();
    }

    private function logo()
    {
        $art = file_get_contents(__DIR__ . '/../../ascii-art.txt');
        $lines = explode("\n", $art);

        foreach ($lines as $line) {
            $this->line($line);
        }
    }

    private function signUpAndCreateProject()
    {
        $this->line('You can create a new account here:');
        $this->line('https://appkeep.co/register');
        $this->line('');

        $this->line('Once you\'re signed up, create your project, and grab your key from the project settings page.');
        $this->line('Run this command again to enter your key.');
    }

    private function enterProjectKey()
    {
        do {
            $key = trim($this->secret('Enter your project key'));
        } while (! $key);

        $this->comment('Verifying your key...');
        $this->line('');

        $status = Http::withHeaders(['Authorization' => 'Bearer ' . $key])
            ->post(config('appkeep.endpoint'), [])
            ->status();

        if (! $this->verifyProjectKey($key)) {
            $this->error('Invalid project key.');
            $this->line('');
            $this->line('Make sure your project key is valid.');
            $this->line('Reach us at hello@appkeep.co for support.');

            return;
        }

        $this->info('Project key is valid, writing it to your .env file...');

        $handler = fopen(base_path('.env'), 'a');
        fputs($handler, "\n\nAPPKEEP_KEY={$key}\n");
        fclose($handler);

        if (file_exists(base_path('bootstrap/cache/config.php'))) {
            Artisan::call('config:cache');
            $this->info('Configuration cache cleared!');
            $this->info('Configuration cached successfully!');
        }
    }
}
