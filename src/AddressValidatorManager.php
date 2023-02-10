<?php

namespace Alexconesap\AddressValidator;

use Alexconesap\AddressValidator\Contracts\AddressValidatorInterface;
use Alexconesap\AddressValidator\Contracts\ProviderInterface;
use Alexconesap\AddressValidator\Data\AddressResult;

/**
 * Address validation Manager
 *
 * Bases the validation logic on an injected provider.
 *
 * @author Yakuma, 2020 <alexconesap@gmail.com>
 */
class AddressValidatorManager
{

    private ?ProviderInterface $provider;

    /**
     * Default constructor - Required for DI
     * @param ProviderInterface $provider The address validation provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Validates an address
     *
     * @param AddressValidatorInterface $address The object to validate. It may be any object that matches the interface.
     * @return AddressResult
     */
    public function validate(AddressValidatorInterface $address): AddressResult
    {
        return $this->provider->validate($address);
    }

    /**
     * Returns last response based on last call to send()
     * @return AddressResult
     */
    public function getLastResponse(): AddressResult
    {
        return $this->provider->getLastResponse();
    }

    /**
     * Returns last response based on last call to send()
     * @return string
     */
    public function getLastResponseAsString(): string
    {
        return $this->provider->getLastResponseAsString();
    }

}
