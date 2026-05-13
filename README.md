# Kanpen Filament Plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mydnic/kanpen-filament-plugin.svg?style=flat-square)](https://packagist.org/packages/mydnic/kanpen-filament-plugin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mydnic/kanpen-filament-plugin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mydnic/kanpen-filament-plugin/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mydnic/kanpen-filament-plugin/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mydnic/kanpen-filament-plugin/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mydnic/kanpen-filament-plugin.svg?style=flat-square)](https://packagist.org/packages/mydnic/kanpen-filament-plugin)

A [Filament](https://filamentphp.com) plugin for [Kanpen](https://github.com/mydnic/kanpen) — the Laravel newsletter package. Manage your subscribers, build email campaigns with a drag-and-drop editor, and track opens and clicks, all from your Filament admin panel.

## Requirements

- PHP 8.2+
- Laravel 11+
- Filament 5+
- [mydnic/kanpen](https://github.com/mydnic/kanpen)

## Installation

Install the plugin via Composer:

```bash
composer require mydnic/kanpen-filament-plugin
```

Run the migrations:

```bash
php artisan migrate
```

Register the plugin in your Filament panel provider:

```php
use Mydnic\KanpenFilamentPlugin\KanpenFilamentPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(KanpenFilamentPlugin::make());
}
```

## What's included

### Subscribers

- List all subscribers with their email verification status
- View a subscriber's full campaign history — every campaign delivered to them with open and click stats
- Soft delete, restore, and force delete

### Campaigns

- Create and edit campaigns with a drag-and-drop email builder (powered by [Unlayer](https://unlayer.com))
- Load a saved template as the starting point for a campaign
- Send a test email to any address before going live
- Send a campaign to all subscribers (dispatched to the queue)
- Schedule campaigns for a future date
- View per-campaign stats: total sent, unique opens, total clicks
- Status badges: Draft, Sending, Sent, Cancelled

### Email Templates

- Build reusable email templates with the Unlayer drag-and-drop editor
- Templates are stored as both a design JSON (for re-editing) and compiled HTML (for sending)
- Load any template into a campaign with one click

## Email builder

The campaign and template editors use [Unlayer](https://unlayer.com)'s free embeddable editor. Designs are saved as Unlayer JSON alongside the compiled HTML. The HTML is what Kanpen actually sends — so the editor is entirely optional and swappable in the future without touching the sending logic.

Kanpen passes the compiled HTML directly to Laravel's mailer. If the content is a partial HTML snippet rather than a full document, Kanpen automatically wraps it in your application's mail layout (the one configured via `php artisan vendor:publish --tag=laravel-mail`), including an unsubscribe link in the footer.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Mydnic](https://github.com/mydnic)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
