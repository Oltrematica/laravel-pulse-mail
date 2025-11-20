# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel package called `laravel-pulse-mail` by Oltrematica. It integrates with Laravel Pulse to provide a custom widget that displays sent emails from your Laravel application.

**Purpose**: Track and display email activity in the Laravel Pulse dashboard, showing recipients, subjects, mailable classes, and send counts with built-in filtering capabilities.

The package is designed as a library that can be installed in Laravel applications (versions 10, 11, or 12) and requires PHP 8.3+.

## Package Structure

- **Namespace**: `Oltrematica\Pulse\Mail`
- **Service Provider**: `PulseMailServiceProvider` - Auto-discovered by Laravel
- **Configuration**: `config/pulse-mail.php` - Controls widget behavior, ignored emails, and sampling
- **Recorder**: `src/Recorders/MailSentRecorder.php` - Listens to `MessageSent` events and records email data
- **Livewire Component**: `src/Livewire/MailSent.php` - The Pulse widget card component
- **View**: `resources/views/livewire/mail-sent.blade.php` - Widget UI using Pulse blade components
- **Source directories**: The package follows a standard structure with `src/Enums`, `src/Exceptions`, and `src/Support` subdirectories (currently empty/placeholder)
- **Testing**: Uses Orchestra Testbench for package testing
  - `tests/TestCase.php` - Full integration tests with service provider loaded
  - `tests/UnitTestCase.php` - Unit tests WITHOUT service provider loaded
  - `tests/Pest.php` - Configures TestCase for Feature tests and UnitTestCase for Unit tests

## How It Works

1. **Recording**: The `MailSentRecorder` listens to Laravel's `MessageSent` event and records email data to Pulse
2. **Filtering**: Emails can be filtered by recipient address or Mailable class via configuration
3. **Sampling**: Configure sample rate to track a percentage of emails (useful for high-volume apps)
4. **Display**: The Livewire widget queries Pulse aggregated data and displays it in the dashboard
5. **Pulse Integration**: Uses Pulse's time-based filtering system (automatic with Pulse dashboard filters)

## Development Commands

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Static analysis with PhpStan (level: max)
composer analyse

# Format code with Pint (Laravel preset with custom rules)
composer format

# Refactor code with Rector
composer refactor
```

## Code Quality Standards

### PhpStan Configuration
- Level: max
- Analyzes both `src/` and `tests/` directories
- Excludes: ArchTest.php, TestCase.php, UnitTestCase.php

### Pint Configuration (pint.json)
- Preset: Laravel
- Key rules enforced:
  - `declare_strict_types: true` - All files must have strict types declaration
  - `fully_qualified_strict_types: true`
  - `strict_comparison: true`
  - `global_namespace_import` - Auto-imports classes, constants, and functions
  - `ordered_class_elements` - Enforces specific class member ordering
  - `protected_to_private: true`

### Rector Configuration
- Applies Laravel-specific sets:
  - LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER
  - LARAVEL_CODE_QUALITY
  - LARAVEL_COLLECTION
- Prepared sets: deadCode, codeQuality, typeDeclarations, privatization, earlyReturn, strictBooleans
- Skips: AddOverrideAttributeToOverriddenMethodsRector

### Architecture Tests (ArchTest.php)
- Prohibits debugging functions: `dd`, `dump`, `ray` anywhere in the codebase

## Testing Guidelines

- Use Pest for all tests (configured in composer.json)
- Always use **Arrange / Act / Assert pattern** in Pest tests
- Feature tests (in `tests/Feature/`) use `TestCase` which:
  - Loads the package service provider
  - Sets up a test database with users table
  - Uses Orchestra Testbench
- Unit tests (in `tests/Unit/`) use `UnitTestCase` which:
  - Does NOT load any service provider
  - Provides minimal Laravel application setup

## File Naming Conventions

- All PHP files must start with `<?php` followed by `declare(strict_types=1);`
- Follow PSR-4 autoloading standards
- Service provider classes should extend `Illuminate\Support\ServiceProvider`

## Configuration Options

The `config/pulse-mail.php` file provides several customization options:

- **limit**: Maximum number of emails to display in the widget (default: 10)
- **ignore.to**: Array of email addresses to exclude from tracking
- **ignore_mailables**: Array of Mailable class names to exclude from tracking (use fully qualified class names)
- **sample_rate**: Percentage of emails to track (0-1, default: 1 for 100%)

## Installation & Setup

1. Install the package: `composer require oltrematica/laravel-pulse-mail`
2. Publish configuration: `php artisan vendor:publish --tag=pulse-mail-config`
3. Add the recorder to `config/pulse.php`:
   ```php
   'recorders' => [
       \Oltrematica\Pulse\Mail\Recorders\MailSentRecorder::class => [],
   ]
   ```
4. Add the widget to your Pulse dashboard view:
   ```blade
   <livewire:pulse.mail-sent cols="4" rows="2" />
   ```

## Widget Data

The widget displays:
- **To**: Email recipient(s)
- **Subject**: Email subject line
- **Mailable**: The Mailable class used (if available)
- **Count**: Number of times this email was sent during the selected time period

## Important Notes

- The TestCase classes reference a `ToolkitServiceProvider` which may be from a related package or legacy code
- The widget respects Pulse's built-in date filtering - no custom date filters needed
- Email tracking happens via Laravel's mail events, so it works with any mail driver
- Recorded data is aggregated by Pulse's time-based bucketing system for efficient queries
