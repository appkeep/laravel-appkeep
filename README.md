# Official App:keep SDK for Laravel

_Instantly discover problems with your Laravel sites._

[![Latest Version on Packagist](https://img.shields.io/packagist/v/appkeep/laravel-appkeep.svg?style=flat-square)](https://packagist.org/packages/appkeep/laravel-appkeep)
[![Total Downloads](https://img.shields.io/packagist/dt/appkeep/laravel-appkeep.svg?style=flat-square)](https://packagist.org/packages/appkeep/laravel-appkeep)
![GitHub Actions](https://github.com/appkeep/laravel-appkeep/actions/workflows/main.yml/badge.svg)

This is the official Laravel SDK for [App:keep](https://appkeep.co)

## Installation

#### 1. Install the package via composer:

```bash
composer require appkeep/laravel-appkeep
```

#### 2. Initialize Appkeep

```bash
php artisan appkeep:init
```

This will publish the config file and configure default checks. You can later change these default checks from `app/Providers/AppkeepProvider.php`.

### On Production

#### 3. Set up a cronjob

Make sure you have a cronjob that runs `php artisan schedule:run` every minute. App:keep relies on Laravel's scheduler. See Laravel's [documentation](https://laravel.com/docs/9.x/scheduling#running-the-scheduler) to learn more.

#### 4. Sign in to Appkeep

This step simply helps set your `APPKEEP_KEY` env variable. If you know the key, you can add it into your `.env` file yourself.

To sign in / register and create a project key, simply run:

```bash
php artisan appkeep:login
```

## Commands

Here's other commands that you might find useful:

### List all checks

Run this command to see a list of configured health checks:

```bash
php artisan appkeep:list

# +----------------+------------+
# | Check          | Expression |
# +----------------+------------+
# | DatabaseCheck  | * * * * *  |
# | DiskUsageCheck | * * * * *  |
# +----------------+------------+
```

### Customize checks

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
