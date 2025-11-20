<?php

declare(strict_types=1);

namespace Oltrematica\Pulse\Mail\Recorders;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Config;
use Laravel\Pulse\Facades\Pulse;
use Symfony\Component\Mime\Address;

use function in_array;

class MailSentRecorder
{
    /**
     * The events to listen for.
     *
     * @var array<int, class-string>
     */
    public array $listen = [
        MessageSent::class,
    ];

    /**
     * Record the email sent event.
     */
    public function record(MessageSent $event): void
    {
        /**
         * @var array{
         *     sample_rate?: float,
         *     ignore?: array{to?: array<int, string>},
         *     ignore_mailables?: array<int, class-string>
         * } $config
         */
        $config = Config::get('pulse-mail', []);

        // Check sample rate
        $sampleRate = $config['sample_rate'] ?? 1.0;
        if ($sampleRate < 1 && mt_rand() / mt_getrandmax() > $sampleRate) {
            return;
        }

        $message = $event->message;
        $to = $this->getRecipients($message->getTo());
        $mailable = $this->getMailableClass($event);

        // Check if email should be ignored by recipient
        /** @var array<int, string> $ignoredRecipients */
        $ignoredRecipients = $config['ignore']['to'] ?? [];
        if ($this->shouldIgnoreRecipient($to, $ignoredRecipients)) {
            return;
        }

        // Check if mailable should be ignored
        /** @var array<int, class-string> $ignoredMailables */
        $ignoredMailables = $config['ignore_mailables'] ?? [];
        if ($mailable !== null && $this->shouldIgnoreMailable($mailable, $ignoredMailables)) {
            return;
        }

        $subject = $message->getSubject() ?? '(no subject)';

        // Record the email with multiple data points
        Pulse::record(
            type: 'mail_sent',
            key: json_encode([
                'to' => $to,
                'subject' => $subject,
                'mailable' => $mailable,
                'status' => 'sent',
            ], JSON_THROW_ON_ERROR),
        )->count();
    }

    /**
     * Get email recipients as a comma-separated string.
     *
     * @param  array<Address>|null  $addresses
     */
    protected function getRecipients(?array $addresses): string
    {
        if ($addresses === null || $addresses === []) {
            return '';
        }

        return implode(', ', array_map(
            static fn (Address $address): string => $address->getAddress(),
            $addresses
        ));
    }

    /**
     * Get the mailable class name if available.
     */
    protected function getMailableClass(MessageSent $event): ?string
    {
        // Check if it's a notification
        if (isset($event->data['__laravel_notification'])) {
            return null;
        }

        // Try to get mailable class from Laravel's metadata
        $mailable = $event->data['__laravel_mailable'] ?? $event->data['mailable'] ?? null;

        return is_string($mailable) ? $mailable : null;
    }

    /**
     * Check if the recipient should be ignored.
     *
     * @param  array<int, string>  $ignoredRecipients
     */
    protected function shouldIgnoreRecipient(string $to, array $ignoredRecipients): bool
    {
        if ($ignoredRecipients === []) {
            return false;
        }

        foreach ($ignoredRecipients as $ignored) {
            if (str_contains($to, $ignored)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the mailable should be ignored.
     *
     * @param  array<int, class-string>  $ignoredMailables
     */
    protected function shouldIgnoreMailable(string $mailable, array $ignoredMailables): bool
    {
        return in_array($mailable, $ignoredMailables, true);
    }
}
