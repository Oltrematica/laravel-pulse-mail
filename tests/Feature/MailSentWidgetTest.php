<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Laravel\Pulse\Facades\Pulse;
use Livewire\Livewire;
use Oltrematica\Pulse\Mail\Livewire\MailSent;

test('it can render the mail sent widget', function (): void {
    // Arrange & Act
    $component = Livewire::test(MailSent::class);

    // Assert
    $component->assertSuccessful();
});

test('it gets emails from pulse aggregated data', function (): void {
    // Arrange
    Pulse::record(
        type: 'mail_sent',
        key: json_encode([
            'to' => 'test@example.com',
            'subject' => 'Test Subject',
            'mailable' => 'TestMailable',
            'status' => 'sent',
        ], JSON_THROW_ON_ERROR),
    )->count();

    Pulse::ingest();

    $widget = new MailSent;

    // Act
    $emails = $widget->getEmails(10);

    // Assert
    expect($emails)->toHaveCount(1);
    $email = $emails->first();
    expect($email)->not->toBeNull()
        ->and($email->to)->toBe('test@example.com')
        ->and($email->subject)->toBe('Test Subject')
        ->and($email->mailable)->toBe('TestMailable')
        ->and($email->status)->toBe('sent')
        ->and($email->count)->toBe(1);
});

test('it respects the configured limit', function (): void {
    // Arrange
    Config::set('pulse-mail.limit', 2);

    // Record 3 different emails
    foreach (range(1, 3) as $i) {
        Pulse::record(
            type: 'mail_sent',
            key: json_encode([
                'to' => "test{$i}@example.com",
                'subject' => "Test Subject {$i}",
                'mailable' => 'TestMailable',
                'status' => 'sent',
            ], JSON_THROW_ON_ERROR),
        )->count();
    }

    Pulse::ingest();

    $widget = new MailSent;

    // Act
    $emails = $widget->getEmails(2);

    // Assert
    expect($emails)->toHaveCount(2);
});

test('it handles emails without subject', function (): void {
    // Arrange
    Pulse::record(
        type: 'mail_sent',
        key: json_encode([
            'to' => 'test@example.com',
            'subject' => null,
            'mailable' => null,
            'status' => 'sent',
        ], JSON_THROW_ON_ERROR),
    )->count();

    Pulse::ingest();

    $widget = new MailSent;

    // Act
    $emails = $widget->getEmails(10);

    // Assert
    $email = $emails->first();
    expect($email)->not->toBeNull();
    expect($email->subject)->toBe('(no subject)');
});

test('it displays email count correctly', function (): void {
    // Arrange
    $emailKey = json_encode([
        'to' => 'test@example.com',
        'subject' => 'Test Subject',
        'mailable' => 'TestMailable',
        'status' => 'sent',
    ], JSON_THROW_ON_ERROR);

    // Record the same email 5 times
    foreach (range(1, 5) as $i) {
        Pulse::record(
            type: 'mail_sent',
            key: $emailKey,
        )->count();
    }

    Pulse::ingest();

    $widget = new MailSent;

    // Act
    $emails = $widget->getEmails(10);

    // Assert
    expect($emails)->toHaveCount(1);
    $email = $emails->first();
    expect($email)->not->toBeNull();
    expect($email->count)->toBe(5);
});

test('it handles empty email list', function (): void {
    // Arrange
    $widget = new MailSent;

    // Act
    $emails = $widget->getEmails(10);

    // Assert
    expect($emails)->toHaveCount(0);
});

test('it displays mailable class name', function (): void {
    // Arrange
    Pulse::record(
        type: 'mail_sent',
        key: json_encode([
            'to' => 'test@example.com',
            'subject' => 'Test Subject',
            'mailable' => 'App\\Mail\\WelcomeEmail',
            'status' => 'sent',
        ], JSON_THROW_ON_ERROR),
    )->count();

    Pulse::ingest();

    $widget = new MailSent;

    // Act
    $emails = $widget->getEmails(10);

    // Assert
    $email = $emails->first();
    expect($email)->not->toBeNull();
    expect($email->mailable)->toBe('App\\Mail\\WelcomeEmail');
});

test('it handles email without mailable class', function (): void {
    // Arrange
    Pulse::record(
        type: 'mail_sent',
        key: json_encode([
            'to' => 'test@example.com',
            'subject' => 'Test Subject',
            'mailable' => null,
            'status' => 'sent',
        ], JSON_THROW_ON_ERROR),
    )->count();

    Pulse::ingest();

    $widget = new MailSent;

    // Act
    $emails = $widget->getEmails(10);

    // Assert
    $email = $emails->first();
    expect($email)->not->toBeNull();
    expect($email->mailable)->toBeNull();
});

test('it handles multiple recipients correctly', function (): void {
    // Arrange
    Pulse::record(
        type: 'mail_sent',
        key: json_encode([
            'to' => 'test1@example.com, test2@example.com',
            'subject' => 'Test Subject',
            'mailable' => 'TestMailable',
            'status' => 'sent',
        ], JSON_THROW_ON_ERROR),
    )->count();

    Pulse::ingest();

    $widget = new MailSent;

    // Act
    $emails = $widget->getEmails(10);

    // Assert
    $email = $emails->first();
    expect($email)->not->toBeNull();
    expect($email->to)->toContain('test1@example.com')
        ->and($email->to)->toContain('test2@example.com');
});
