<?php

namespace Alexconesap\AddressValidator;

use Alexconesap\AddressValidator\Contracts\ProviderInterface;
use Alexconesap\AddressValidator\Providers\NullProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * LARAVEL/LUMEN Service Provider
 *
 * Publish configuration (LARAVEL):
 * <code>
 * php artisan vendor:publish --tag=config
 * </code>
 *
 * @author Yakuma, 2020 <alexconesap@gmail.com>
 * @version 1
 */
class AddressValidatorServiceProvider extends ServiceProvider implements Contracts\Constants, DeferrableProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if ($this->isLumen()) {
                $this->app->configure(static::CONFIG_KEY);
            } else {
                $this->publishes([
                    $this->getConfigFile() => config_path(static::CONFIG_FILENAME)
                ], 'config');
            }
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigFile(),
            static::CONFIG_KEY
        );

        $this->app->singleton('address_validator', function () {
            $driver = $this->app['config'][static::CONFIG_KEY . '.driver'];
            $class = $this->app['config'][static::CONFIG_KEY . ".drivers.$driver.class"];
            if (empty($class) || !class_exists($class)) {
                $class = NullProvider::class;
            }
            return new AddressValidatorManager(
                new $class
            );
        });
    }

    protected function getConfigFile(): string
    {
        return __DIR__ . '/../config/' . static::CONFIG_FILENAME;
    }

    /**
     * Check if package is running under Lumen app
     * @return bool
     */
    protected function isLumen(): bool
    {
        return Str::contains($this->app->version(), 'Lumen') === true;
    }

    /**
     * Return the service container bindings registered. Only for deferred services like this one.
     *
     * @return array
     */
    public function provides()
    {
        return [
            ProviderInterface::class,
            'address_validator'
        ];
    }
}
