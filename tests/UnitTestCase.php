<?php

declare(strict_types=1);

namespace Oltrematica\Pulse\Mail\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Oltrematica\Pulse\Mail\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class UnitTestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName): string => 'Oltrematica\\Toolkit\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        //        config()->set('oltrematica-Toolkit.prefix', 'custom-prefix');
        //        config()->set('oltrematica-Toolkit.protected', true);

        /*
        $migration = include __DIR__.'/../database/migrations/create_Toolkit_table.php.stub';
        $migration->up();
        */
    }

    protected function getPackageProviders($app)
    {
        return [
            //            ServiceProvider::class,
        ];
    }
}
