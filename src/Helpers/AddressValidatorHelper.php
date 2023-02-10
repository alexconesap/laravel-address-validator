<?php

/**
 * This is a helper to avoid manually inject the address validation service provider used by the
 * system.
 *
 * Exported functions:
 * yaddress()
 *
 * Must be registered at the composer.json file
 * "autoload": {
 * ...
 * "files": [
 *       "app/Helpers/AddressValidatorHelper.php",
 * ]
 *
 * @author Yakuma <alexconesap@gmail.com>
 * @version 1.0
 */

use Alexconesap\AddressValidator\AddressValidatorManager;
use Alexconesap\AddressValidator\Contracts\Constants;
use Alexconesap\AddressValidator\Providers\NullProvider;

if (!function_exists('yaddress_available')) {

    function yaddress_available(): bool
    {
        return !empty(config(Constants::CONFIG_KEY . '.driver')
            && get_class(app('address_validator')) != NullProvider::class
        );
    }

}

if (!function_exists('yaddress')) {

    /**
     * @return AddressValidatorManager|null
     */
    function yaddress(): ?AddressValidatorManager
    {
        return app('address_validator');
    }

}
