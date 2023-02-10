<?php

/**
 * Common Support libraries
 *
 * @author Yakuma, 2020 <alexconesap@gmail.com>
 * @version 1.0
 */

namespace Alexconesap\AddressValidator\Providers;

use Alexconesap\AddressValidator\Contracts\AddressValidatorInterface;
use Alexconesap\AddressValidator\Contracts\ProviderInterface;
use Alexconesap\AddressValidator\Data\AddressResult;
use Alexconesap\AddressValidator\Exceptions\WrongAddressException;

/**
 * Address Validation Providers - Base abstract class following the template design pattern
 *
 * Extend this class with a proper implementation of methods defined at the ProviderInterface contract.
 */
abstract class AbstractProvider implements ProviderInterface
{

    /**
     * Contains the last API call result
     */
    private ?AddressResult $last_response;

    /**
     * Calls the remote API to validate the address
     *
     * @param AddressValidatorInterface $address The address to be validated
     * @return AddressResult
     */
    abstract function api_call(AddressValidatorInterface $address): AddressResult;

    /**
     * Validates the address
     *
     * Result structure defined at
     *
     * @param AddressValidatorInterface $address
     * @return AddressResult
     * @throws WrongAddressException
     */
    public function validate(AddressValidatorInterface $address): AddressResult
    {
        if (empty($address->getAddressAsString())) {
            throw new WrongAddressException('Unable to validate an empty address');
        }

        $this->last_response = $this->api_call($address);

        return $this->last_response;
    }

    /**
     * Returns last response based on last call to send()
     * @return AddressResult
     */
    public function getLastResponse(): AddressResult
    {
        return $this->last_response ?? new AddressResult();
    }

    /**
     * Returns last response based on last call to send()
     * @return string
     */
    public function getLastResponseAsString(): string
    {
        return (string)$this->getLastResponse();
    }

}
