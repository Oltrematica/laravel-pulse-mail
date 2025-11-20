<?php

declare(strict_types=1);

namespace Oltrematica\Pulse\Mail\Tests;

use Laravel\Pulse\PulseServiceProvider;
use Livewire\LivewireServiceProvider;
use Oltrematica\Pulse\Mail\PulseMailServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Configure Pulse
        config()->set('pulse.storage.driver', 'database');
        config()->set('pulse.ingest.driver', 'storage');

        // Configure pulse-mail package
        config()->set('pulse-mail.limit', 10);
        config()->set('pulse-mail.ignore.to', []);
        config()->set('pulse-mail.ignore_mailables', []);
        config()->set('pulse-mail.sample_rate', 1);
    }

    protected function getPackageProviders($app): array
    {
        return [
            PulseServiceProvider::class,
            LivewireServiceProvider::class,
            PulseMailServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();

        // Load Pulse migrations
        $this->loadMigrationsFrom(__DIR__.'/../vendor/laravel/pulse/database/migrations');
    }
}
