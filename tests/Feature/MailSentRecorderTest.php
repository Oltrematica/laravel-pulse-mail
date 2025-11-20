<?php

declare(strict_types=1);

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\Config;
use Oltrematica\Pulse\Mail\Recorders\MailSentRecorder;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

function createSentMessage(Email $email): SentMessage
{
    // Ensure email has required headers to satisfy Symfony validation
    if ($email->getTextBody() === null && $email->getHtmlBody() === null) {
        $email->text('Test email body');
    }

    if ($email->getFrom() === []) {
        $email->from(new Address('sender@example.com', 'Test Sender'));
    }

    $recipients = $email->getTo() !== [] ? $email->getTo() : [new Address('test@example.com')];

    $envelope = new Envelope(
        new Address('sender@example.com'),
        $recipients
    );
    $symfonySentMessage = new SymfonySentMessage($email, $envelope);

    return new SentMessage($symfonySentMessage);
}

test('it records email sent event without errors', function (): void {
    // Arrange
    $recorder = new MailSentRecorder;
    $message = new Email;
    $message->to(new Address('test@example.com', 'Test User'));
    $message->subject('Test Subject');

    $sentMessage = createSentMessage($message);
    $event = new MessageSent($sentMessage, ['mailable' => 'TestMailable']);

    // Act & Assert
    expect(fn () => $recorder->record($event))->not->toThrow(Exception::class);
});

test('it records multiple recipients without errors', function (): void {
    // Arrange
    $recorder = new MailSentRecorder;
    $message = new Email;
    $message->to(
        new Address('test1@example.com', 'Test User 1'),
        new Address('test2@example.com', 'Test User 2')
    );
    $message->subject('Test Subject');

    $sentMessage = createSentMessage($message);
    $event = new MessageSent($sentMessage);

    // Act & Assert
    expect(fn () => $recorder->record($event))->not->toThrow(Exception::class);
});

test('it ignores emails by recipient', function (): void {
    // Arrange
    Config::set('pulse-mail.ignore.to', ['ignored@example.com']);

    $recorder = new MailSentRecorder;
    $message = new Email;
    $message->to(new Address('ignored@example.com'));
    $message->subject('Test Subject');

    $sentMessage = createSentMessage($message);
    $event = new MessageSent($sentMessage);

    // Act & Assert - Should not throw exception even when ignoring
    expect(fn () => $recorder->record($event))->not->toThrow(Exception::class);
});

test('it ignores emails by mailable class', function (): void {
    // Arrange
    Config::set('pulse-mail.ignore_mailables', ['TestMailable']);

    $recorder = new MailSentRecorder;
    $message = new Email;
    $message->to(new Address('test@example.com'));
    $message->subject('Test Subject');

    $sentMessage = createSentMessage($message);
    $event = new MessageSent($sentMessage, ['mailable' => 'TestMailable']);

    // Act & Assert - Should not throw exception even when ignoring
    expect(fn () => $recorder->record($event))->not->toThrow(Exception::class);
});

test('it respects sample rate of zero', function (): void {
    // Arrange
    Config::set('pulse-mail.sample_rate', 0);

    $recorder = new MailSentRecorder;
    $message = new Email;
    $message->to(new Address('test@example.com'));
    $message->subject('Test Subject');

    $sentMessage = createSentMessage($message);
    $event = new MessageSent($sentMessage);

    // Act & Assert - Should not throw exception
    expect(fn () => $recorder->record($event))->not->toThrow(Exception::class);
});

test('it handles email without subject', function (): void {
    // Arrange
    $recorder = new MailSentRecorder;
    $message = new Email;
    $message->to(new Address('test@example.com'));

    $sentMessage = createSentMessage($message);
    $event = new MessageSent($sentMessage);

    // Act & Assert
    expect(fn () => $recorder->record($event))->not->toThrow(Exception::class);
});

test('it extracts mailable class from event data', function (): void {
    // Arrange
    $recorder = new MailSentRecorder;
    $message = new Email;
    $message->to(new Address('test@example.com'));
    $message->subject('Test Subject');

    $sentMessage = createSentMessage($message);
    $event = new MessageSent($sentMessage, ['__laravel_mailable' => 'App\\Mail\\WelcomeEmail']);

    // Act & Assert
    expect(fn () => $recorder->record($event))->not->toThrow(Exception::class);
});

test('it handles notifications correctly', function (): void {
    // Arrange
    $recorder = new MailSentRecorder;
    $message = new Email;
    $message->to(new Address('test@example.com'));
    $message->subject('Test Notification');

    $sentMessage = createSentMessage($message);
    $event = new MessageSent($sentMessage, ['__laravel_notification' => true]);

    // Act & Assert
    expect(fn () => $recorder->record($event))->not->toThrow(Exception::class);
});

test('it records duplicate emails without errors', function (): void {
    // Arrange
    $recorder = new MailSentRecorder;
    $message = new Email;
    $message->to(new Address('test@example.com'));
    $message->subject('Test Subject');

    $sentMessage = createSentMessage($message);
    $event = new MessageSent($sentMessage);

    // Act & Assert - Recording same email multiple times should not throw
    $recorder->record($event);
    expect(fn () => $recorder->record($event))->not->toThrow(Exception::class);
});
