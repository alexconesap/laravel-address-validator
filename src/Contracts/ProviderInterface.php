<?php

namespace Alexconesap\AddressValidator\Contracts;

use Alexconesap\AddressValidator\Data\AddressResult;

interface ProviderInterface
{

    /**
     * Validates the address
     *
     * @param AddressValidatorInterface $address The address to validate
     * @return AddressResult
     */
    public function validate(AddressValidatorInterface $address): AddressResult;

    /**
     * Returns last response based on last call to validate()
     * @return AddressResult
     */
    public function getLastResponse(): AddressResult;

    /**
     * Returns last response based on last call to validate()
     * @return string
     */
    public function getLastResponseAsString(): string;
}
