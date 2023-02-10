<?php

namespace Alexconesap\AddressValidator\Facade;

use Alexconesap\AddressValidator\AddressValidatorManager;
use Alexconesap\AddressValidator\Contracts\AddressValidatorInterface;
use Alexconesap\AddressValidator\Data\AddressResult;
use Illuminate\Support\Facades\Facade;

/**
 * The facade allows to use the core classes with static method calls.
 *
 * Static methods accessible through the facade:
 * @method static AddressResult validate(AddressValidatorInterface $address) Validates a given object
 * @method static AddressResult getLastResponse()
 * @method static string getLastResponseAsString()
 *
 * @author Yakuma, 2020 <alexconesap@gmail.com>
 * @version 1
 * @see \Alexconesap\AddressValidator\Contracts\ProviderInterface
 */
class AddressValidator extends Facade
{

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return AddressValidatorManager::class;
    }

}
