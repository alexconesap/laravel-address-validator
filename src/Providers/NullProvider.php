<?php

namespace Alexconesap\AddressValidator\Providers;

use Alexconesap\AddressValidator\Contracts\AddressValidatorInterface;
use Alexconesap\AddressValidator\Data\AddressResult;
use Alexconesap\AddressValidator\Exceptions\ProviderNotConfiguredException;

/**
 * NULL INTERFACE - Just raise an Exception. Used when the driver/provider is not set in the configuration file.
 *
 * @author Yakuma, 2020 <alexconesap@gmail.com>
 */
class NullProvider extends AbstractProvider
{

    /**
     * Directly raises an Exception
     *
     * @param AddressValidatorInterface $address
     * @return AddressResult
     * @throws ProviderNotConfiguredException
     */
    public function api_call(AddressValidatorInterface $address): AddressResult
    {
        throw new ProviderNotConfiguredException("Address Validator provider not configured. Check .env settings.");
    }

}
