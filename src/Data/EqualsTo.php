<?php

namespace Alexconesap\AddressValidator\Data;

use Alexconesap\AddressValidator\Contracts\AddressValidatorInterface;

trait EqualsTo
{

    /**
     * Verify it current object equals to $to object
     * @param AddressValidatorInterface|null $to Object to compare with
     * @return bool
     */
    public function isEqualsTo(?AddressValidatorInterface $to): bool
    {
        if ($to == null) {
            return false;
        }
        // DO NOT compare the State due to it may be either WI or WISCONSIN
        return strtolower($this->getZip_code()) == strtolower($to->getZip_code()) &&
            strtolower($this->getCity()) == strtolower($to->getCity()) &&
            strtolower($this->getStreet1()) == strtolower($to->getStreet1());
    }

}
