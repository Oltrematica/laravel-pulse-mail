![GitHub Tests Action Status](https://github.com/Oltrematica/laravel-pulse-mail/actions/workflows/run-tests.yml/badge.svg)
![GitHub PhpStan Action Status](https://github.com/Oltrematica/laravel-pulse-mail/actions/workflows/phpstan.yml/badge.svg)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/oltrematica/laravel-pulse-mail.svg?style=flat-square)](https://packagist.org/packages/oltrematica/laravel-pulse-mail)
[![Total Downloads](https://img.shields.io/packagist/dt/oltrematica/laravel-pulse-mail.svg?style=flat-square)](https://packagist.org/packages/oltrematica/laravel-pulse-mail)

# Laravel Pulse Mail

Track and monitor emails sent from your Laravel application directly in your Laravel Pulse dashboard.

This package provides a custom Pulse widget that displays sent emails with details including recipients, subjects, mailable classes, and send counts. Perfect for monitoring email activity and debugging mail-related issues in production.

## Features

- 📧 **Track all sent emails** - Automatically records emails sent via Laravel Mail
- 🎯 **Detailed information** - Shows recipient, subject, mailable class, and send count
- ⚙️ **Configurable filtering** - Exclude specific emails or mailables from tracking
- 📊 **Sample rate control** - Track a percentage of emails for high-volume applications
- 🕐 **Time-based filtering** - Uses Pulse's built-in date filtering
- 🎨 **Consistent UI** - Matches Pulse's design language

## Requirements

- PHP 8.3+
- Laravel 10.x, 11.x, or 12.x
- Laravel Pulse 1.x

## Installation

Install the package via Composer:

```bash
composer require oltrematica/laravel-pulse-mail
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=pulse-mail-config
```

Add the recorder to your `config/pulse.php`:

```php
'recorders' => [
    // ... other recorders

    \Oltrematica\Pulse\Mail\Recorders\MailSentRecorder::class => [
        'enabled' => env('PULSE_MAIL_ENABLED', true),
    ],
],
```

Add the widget to your Pulse dashboard (`resources/views/vendor/pulse/dashboard.blade.php`):

```blade
<x-pulse>
    {{-- Other widgets --}}

    <livewire:pulse.mail-sent cols="full" rows="2" />
</x-pulse>
```

## Configuration

The `config/pulse-mail.php` file provides several options:

```php
return [
    // Maximum number of emails to display in the widget
    'limit' => env('PULSE_MAIL_LIMIT', 10),

    // Email addresses to exclude from tracking
    'ignore' => [
        'to' => [
            // 'test@example.com',
        ],
    ],

    // Mailable classes to exclude from tracking
    'ignore_mailables' => [
        // \App\Mail\TestEmail::class,
    ],

    // Sample rate (0-1): 1 = track all emails, 0.5 = track 50%
    'sample_rate' => env('PULSE_MAIL_SAMPLE_RATE', 1),
];
```

## Usage

Once installed and configured, the package will automatically start tracking emails sent through Laravel's Mail facade or Mailable classes. The widget will display:

- **To**: Email recipient address(es)
- **Subject**: Email subject line
- **Mailable**: The Mailable class used (if applicable)
- **Count**: Number of times this email was sent during the selected period

### Filtering

The widget respects Pulse's time-based filtering. Use the Pulse dashboard controls to filter emails by time period (last hour, 24 hours, 7 days, etc.).

### Ignoring Specific Emails

To exclude certain emails from tracking, add them to the configuration:

```php
// Ignore by recipient
'ignore' => [
    'to' => [
        'test@example.com',
        'noreply@example.com',
    ],
],

// Ignore by Mailable class
'ignore_mailables' => [
    \App\Mail\TestEmail::class,
    \App\Mail\InternalNotification::class,
],
```

### Sample Rate

For high-volume applications, you can track only a percentage of emails:

```php
// Track 50% of emails
'sample_rate' => 0.5,

// Or use environment variable
'sample_rate' => env('PULSE_MAIL_SAMPLE_RATE', 1),
```

## Code Quality

The project includes automated tests and tools for code quality control.

### Rector

Rector is a tool for automating code refactoring and migrations. It can be run using the following command:

```shell
composer refactor
```

### PhpStan

PhpStan is a tool for static analysis of PHP code. It can be run using the following command:

```shell
composer analyse
```

### Pint

Pint is a tool for formatting PHP code. It can be run using the following command:

```shell
composer format
```

### Automated Tests

The project includes automated tests and tools for code quality control.

```shell
composer test
```

## Contributing

Feel free to contribute to this package by submitting issues or pull requests. We welcome any improvements or bug fixes
you may have.

