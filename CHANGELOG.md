# Changelog

All notable changes to `laravel-tooklkit` will be documented in this file.

## v0.1.2 - 2025-05-20

**Full Changelog**: https://github.com/Oltrematica/laravel-pulse-mail/compare/v0.1.1...v0.1.2

## v0.1.1 - 2025-05-19

**Full Changelog**: https://github.com/Oltrematica/laravel-pulse-mail/compare/v0.1.0...v0.1.1

- InvalidConfigurationException class

## v0.1.0 - 2025-05-16

Added the Result value object to the toolkit.
Result provides a standardized way to represent the outcome of operations, encapsulating success, error, failure (with exception), messages, data, and optional exceptions.
Includes factory methods: Result::success(), Result::error(), and Result::failure().
Provides clear API for checking the result state: isSuccess(), isError(), isFailure(), as well as getters for message, data, and throwable.

## Fix user model retrieval in ConfigService to handle missing configuration - 2025-03-09

### What's Changed

* Fix user model retrieval in ConfigService to handle missing configuration by @mirchaemanuel in https://github.com/Oltrematica/laravel-pulse-mail/pull/2

**Full Changelog**: https://github.com/Oltrematica/laravel-pulse-mail/compare/v1.0.1...v1.0.2

## Firing events when a role is assigned or detached - 2025-03-08

### What's Changed

* Add role management events and enhance model properties by @mirchaemanuel in https://github.com/Oltrematica/laravel-pulse-mail/pull/1

### New Contributors

* @mirchaemanuel made their first contribution in https://github.com/Oltrematica/laravel-pulse-mail/pull/1

**Full Changelog**: https://github.com/Oltrematica/laravel-pulse-mail/compare/v1.0.0...v1.0.1

## Initial Release of Oltrematica Laravel Role Lite - 2025-03-08

This is the initial stable release of the Oltrematica Laravel Role Lite package. It provides a simple and configurable way to manage roles in a Laravel application.
