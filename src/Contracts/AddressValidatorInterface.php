<?php

namespace Alexconesap\AddressValidator\Contracts;

interface AddressValidatorInterface
{

    /**
     * Required field
     * @return string
     */
    public function getStreet1(): ?string;

    /**
     * Optional field
     * @return string
     */
    public function getStreet2(): ?string;

    /**
     * Required field
     * @return string
     */
    public function getCity(): ?string;

    /**
     * Required field
     * @return string
     */
    public function getState(): ?string;

    /**
     * Required field
     * @return string
     */
    public function getZip_code(): ?string;

    /**
     * Optional field
     * @return string
     */
    public function getCountry(): ?string;

    /**
     * Optional field
     * @return float
     */
    public function getLongitude(): float;

    /**
     * Optional field
     * @return float
     */
    public function getLatitude(): float;

    /**
     * Optional field
     * @return string
     */
    public function getPlace_name(): ?string;

    /**
     * Returns a "US formatted" address formed by:
     *
     * street, city, state, zip code
     *
     * @return string
     */
    public function getAddressAsString(): string;

    /**
     * Compares to Addresses and returns true when they are the same
     *
     * It exists an implementation on a trait EqualsTo.php
     *
     * @param AddressValidatorInterface|null $to
     * @return bool
     */
    public function isEqualsTo(?AddressValidatorInterface $to): bool;
}
