<?php

namespace Tests;

use Alexconesap\AddressValidator\AddressValidatorServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

class TestCase extends \Orchestra\Testbench\TestCase
{

    /**
     * Setup DB before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

//        $this->app['config']->set('validator.enabled', true);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__ . '/../../');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }

    protected function getPackageProviders($app): array
    {
        return [
            AddressValidatorServiceProvider::class
        ];
    }

}