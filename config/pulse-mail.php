<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Emails to Display
    |--------------------------------------------------------------------------
    |
    | This value determines the maximum number of emails that will be
    | displayed in the Pulse mail widget. You can adjust this value
    | based on your needs.
    |
    */
    'limit' => env('PULSE_MAIL_LIMIT', 10),

    /*
    |--------------------------------------------------------------------------
    | Ignored Emails
    |--------------------------------------------------------------------------
    |
    | These email addresses will not be tracked by the mail recorder.
    | Useful for excluding test emails or internal notifications.
    |
    */
    'ignore' => [
        'to' => [
            // 'test@example.com',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored Mailables
    |--------------------------------------------------------------------------
    |
    | These Mailable class names will not be tracked by the mail recorder.
    | Use the fully qualified class name.
    |
    */
    'ignore_mailables' => [
        // \App\Mail\TestEmail::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sample Rate
    |--------------------------------------------------------------------------
    |
    | This value determines the sample rate for email tracking. Set to 1
    | to track all emails, or a lower value to sample a percentage of emails.
    | For example, 0.5 will track approximately 50% of emails.
    |
    */
    'sample_rate' => env('PULSE_MAIL_SAMPLE_RATE', 1),
];
