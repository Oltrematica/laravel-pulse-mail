<?php

declare(strict_types=1);

namespace Oltrematica\Pulse\Mail\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

use function json_decode;

#[Lazy]
class MailSent extends Card
{
    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        /** @var int $limit */
        $limit = Config::get('pulse-mail.limit', 10);

        [$emails] = $this->remember(
            fn (): array => $this->getEmails($limit),
            'mail-sent',
        );

        return View::make('pulse-mail::livewire.mail-sent', [
            'emails' => $emails,
        ]);
    }

    /**
     * Get the emails from Pulse data.
     *
     * @phpstan-return array{0: \Illuminate\Support\Collection<int, object>}
     */
    public function getEmails(int $limit): array
    {
        $emails = $this->aggregate('mail_sent', ['count'])
            ->map(function (mixed $entry): object {
                /** @var object{key: string, count: int} $entry */
                /** @var array{to?: string, subject?: string, mailable?: string, status?: string} $data */
                $data = json_decode($entry->key, true, 512, JSON_THROW_ON_ERROR);

                return (object) [
                    'to' => $data['to'] ?? '',
                    'subject' => $data['subject'] ?? '(no subject)',
                    'mailable' => $data['mailable'] ?? null,
                    'status' => $data['status'] ?? 'sent',
                    'count' => $entry->count,
                ];
            })
            ->take($limit);

        /** @phpstan-ignore return.type */
        return [$emails];
    }
}
