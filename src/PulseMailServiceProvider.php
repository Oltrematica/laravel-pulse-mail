<?php

declare(strict_types=1);

namespace Oltrematica\Pulse\Mail;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Livewire\Livewire;
use Oltrematica\Pulse\Mail\Livewire\MailSent;

class PulseMailServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/pulse-mail.php' => config_path('pulse-mail.php'),
        ], 'pulse-mail-config');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pulse-mail');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/pulse-mail'),
        ], 'pulse-mail-views');

        // Register Livewire component
        Livewire::component('pulse.mail-sent', MailSent::class);
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/pulse-mail.php',
            'pulse-mail'
        );
    }
}
