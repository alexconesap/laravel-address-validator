<?php

namespace Alexconesap\AddressValidator\Data;

use Alexconesap\Commons\Models\BaseBean;
use Alexconesap\Commons\StringBuilder;
use Alexconesap\Commons\StringLib;
use Alexconesap\AddressValidator\Contracts\AddressValidatorInterface;

/**
 * @property string $street1
 * @property string $street2
 * @property string $city
 * @property string $state
 * @property string $zip_code
 * @property string $country
 * @property float $longitude
 * @property float $latitude
 * @property string $place_name
 * @property int $index
 */
class Address extends BaseBean implements AddressValidatorInterface
{

    use EqualsTo;

    private ?string $street1 = null;
    private ?string $street2 = null;
    private ?string $city = null;
    private ?string $state = null;
    private ?string $zip_code = null;
    private ?string $country = null;
    private ?float $longitude = 0;
    private ?float $latitude = 0;
    private ?string $place_name = null;
    private ?int $index = 0;

    /**
     * Creates a basic instance.
     *
     * $fake_address is intended for testing purposes only (PHPUnit?); not for creating valid models
     *
     * @param string|null $fake_address The optional full address US format (street, city, state, zip)
     */
    public function __construct(?string $fake_address = null)
    {
        if (!is_null($fake_address)) {
            $this->street1 = trim(StringLib::getToken($fake_address, 0, ','));
            $this->city = trim(StringLib::getToken($fake_address, 1, ','));
            $this->state = trim(StringLib::getToken($fake_address, 2, ','));
            $this->zip_code = trim(StringLib::getToken($fake_address, 3, ','));
        }
    }

    /**
     * For ordering purposes when this address is part of a Collection
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    public function getStreet1(): ?string
    {
        return $this->street1;
    }

    public function getStreet2(): ?string
    {
        return $this->street2;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getZip_code(): ?string
    {
        return $this->zip_code;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getLongitude(): float
    {
        return (float)($this->longitude ?? 0);
    }

    public function getLatitude(): float
    {
        return (float)($this->latitude ?? 0);
    }

    public function getPlace_name(): ?string
    {
        return $this->place_name;
    }

    public function setStreet1($street1): Address
    {
        $this->street1 = $street1;
        return $this;
    }

    public function setStreet2($street2): Address
    {
        $this->street2 = $street2;
        return $this;
    }

    public function setCity($city): Address
    {
        $this->city = $city;
        return $this;
    }

    public function setState($state): Address
    {
        $this->state = $state;
        return $this;
    }

    public function setZip_code($zip_code): Address
    {
        $this->zip_code = $zip_code;
        return $this;
    }

    public function setCountry($country): Address
    {
        $this->country = $country;
        return $this;
    }

    public function setLongitude($longitude): Address
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function setLatitude($latitude): Address
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function setPlace_name($place_name): Address
    {
        $this->place_name = $place_name;
        return $this;
    }

    /**
     * For ordering purposes when this address is part of a Collection
     * @param int $index
     * @return $this
     */
    public function setIndex(int $index): Address
    {
        $this->index = $index;
        return $this;
    }

    /**
     * All attributes that must be part of getAddressAsString()
     * @return array
     */
    private function attributes(): array
    {
        return [
            'street1' => $this->street1,
            'street2' => $this->street2,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
        ];
    }

    /**
     * Wrapper for __toString()
     * @return string
     */
    public function getAddressAsString(): string
    {
        return (string)(new StringBuilder)->addArrayValues($this->attributes(), ', ');
    }

    /**
     * Customized to String
     * @return string
     */
    public function __toString()
    {
        return $this->getAddressAsString();
    }

}
