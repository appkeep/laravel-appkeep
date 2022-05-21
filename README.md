# Official App:keep SDK for Laravel

_Deploy Laravel confidently._

[![Latest Version on Packagist](https://img.shields.io/packagist/v/appkeep/laravel-appkeep.svg?style=flat-square)](https://packagist.org/packages/appkeep/laravel-appkeep)
[![Total Downloads](https://img.shields.io/packagist/dt/appkeep/laravel-appkeep.svg?style=flat-square)](https://packagist.org/packages/appkeep/laravel-appkeep)
![GitHub Actions](https://github.com/appkeep/laravel-appkeep/actions/workflows/main.yml/badge.svg)

This is the official Laravel SDK for [App:keep](https://appkeep.dev)

## Installation

Install the package via composer:

```bash
composer require appkeep/laravel-appkeep
```

Add the project key you obtain from Appkeep to your .env file:

```dotenv
APPKEEP_PROJECT_KEY=<your-project-key>
```

> 🚨 Make sure you have set up scheduled commands (`php artisan schedule:run`). Appkeep relies on Laravel's schedule runner to work.

https://laravel.com/docs/9.x/scheduling#running-the-scheduler

## Set up health checks

```php
// 1. Import this trait
use Appkeep\Laravel\Concerns\RunsChecks;

class AppServiceProvider extends ServiceProvider
{
    // 2. Add this trait to your class
    use RunsChecks;

    // ...

    public function boot()
    {
        // 3. Insert these lines
        if ($this->app->runningInConsole()) {
            $this->registerDefaultChecks();
        }
    }
```

## Advanced

### List of checks

Run this command to see a list of configured health checks:

```bash
php artisan appkeep:checks

# +----------------+------------+
# | Check          | Expression |
# +----------------+------------+
# | DatabaseCheck  | * * * * *  |
# | DiskUsageCheck | * * * * *  |
# +----------------+------------+
```

### One application, multiple servers

If you are running your Laravel site on multiple hosts behind a load balancer, simply put the respective name of each host in your .env file.

```dotenv
APPKEEP_SERVER_NAME=eu-server1
```

### Writing your own checks

TBD

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

### Security

If you discover any security related issues, please email hello@swiftmade.co instead of using the issue tracker.

## Credits

- [Appkeep](https://github.com/appkeep)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

