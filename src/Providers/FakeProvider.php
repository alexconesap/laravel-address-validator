<?php

namespace Alexconesap\AddressValidator\Providers;

use Alexconesap\AddressValidator\Contracts\AddressValidatorInterface;
use Alexconesap\AddressValidator\Data\Address;
use Alexconesap\AddressValidator\Data\AddressResult;

/**
 * FAKE INTERFACE
 *
 * @author Yakuma, 2020 <alexconesap@gmail.com>
 */
class FakeProvider extends AbstractProvider
{

    /**
     * It will return the given $address "as is"
     *
     * @param AddressValidatorInterface $address
     * @return AddressResult
     */
    public function api_call(AddressValidatorInterface $address): AddressResult
    {
        $a = new Address($address->getAddressAsString());

        $result = new AddressResult();
        $result->add($a);

        return $result;
    }

}
