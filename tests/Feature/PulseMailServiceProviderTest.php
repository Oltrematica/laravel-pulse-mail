<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Oltrematica\Pulse\Mail\Livewire\MailSent;

test('it registers the configuration', function (): void {
    // Arrange & Act
    $limit = Config::get('pulse-mail.limit');
    $sampleRate = Config::get('pulse-mail.sample_rate');

    // Assert
    expect($limit)->toBe(10)
        ->and($sampleRate)->toBe(1);
});

test('it registers the livewire component', function (): void {
    // Arrange & Act
    $component = Livewire::test('pulse.mail-sent');

    // Assert
    $component->assertSuccessful();
});

test('it loads the views', function (): void {
    // Arrange & Act
    $viewExists = View::exists('pulse-mail::livewire.mail-sent');

    // Assert
    expect($viewExists)->toBeTrue();
});

test('configuration has correct structure', function (): void {
    // Arrange & Act
    /** @var array{limit: int, ignore: array{to: array<int, string>}, ignore_mailables: array<int, class-string>, sample_rate: float} $config */
    $config = Config::get('pulse-mail');

    // Assert
    expect($config)->toBeArray()
        ->and($config)->toHaveKeys(['limit', 'ignore', 'ignore_mailables', 'sample_rate'])
        ->and($config['ignore'])->toHaveKey('to')
        ->and($config['ignore']['to'])->toBeArray()
        ->and($config['ignore_mailables'])->toBeArray();
});

test('default ignore lists are empty', function (): void {
    // Arrange & Act
    $ignoredRecipients = Config::get('pulse-mail.ignore.to');
    $ignoredMailables = Config::get('pulse-mail.ignore_mailables');

    // Assert
    expect($ignoredRecipients)->toBeArray()->toBeEmpty()
        ->and($ignoredMailables)->toBeArray()->toBeEmpty();
});

test('sample rate is set to 1 by default', function (): void {
    // Arrange & Act
    $sampleRate = Config::get('pulse-mail.sample_rate');

    // Assert
    expect($sampleRate)->toBe(1);
});

test('limit is set to 10 by default', function (): void {
    // Arrange & Act
    $limit = Config::get('pulse-mail.limit');

    // Assert
    expect($limit)->toBe(10);
});

test('it can override configuration values', function (): void {
    // Arrange & Act
    Config::set('pulse-mail.limit', 20);
    Config::set('pulse-mail.sample_rate', 0.5);
    Config::set('pulse-mail.ignore.to', ['test@example.com']);
    Config::set('pulse-mail.ignore_mailables', ['TestMailable']);

    // Assert
    expect(Config::get('pulse-mail.limit'))->toBe(20)
        ->and(Config::get('pulse-mail.sample_rate'))->toBe(0.5)
        ->and(Config::get('pulse-mail.ignore.to'))->toBe(['test@example.com'])
        ->and(Config::get('pulse-mail.ignore_mailables'))->toBe(['TestMailable']);
});

test('livewire component is registered and can be tested', function (): void {
    // Arrange & Act
    $component = Livewire::test('pulse.mail-sent');

    // Assert
    $component->assertSuccessful();
    expect($component->instance())->toBeInstanceOf(MailSent::class);
});
