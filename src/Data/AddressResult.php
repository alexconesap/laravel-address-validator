<?php

/**
 * @author Yakuma <alexconesap@gmail.com>
 */

namespace Alexconesap\AddressValidator\Data;

use Alexconesap\Commons\Models\BaseBean;
use Alexconesap\Commons\StringBuilder;
use Illuminate\Support\Collection;

/**
 * Manages a Collection of addresses (it may contain just one element ;-))
 *
 * To be considered a valid object it must contain no error flag and at least
 * ONE candidate that it is usually the validated address (even being the same being validated).
 *
 * Create the AddressResult and manage it using the following methods:
 *
 * <code>
 * <b>Add data</b>
 * add(Address $object): $this
 * setAddresses(Collection<Address> $collection): $this
 *
 * <b>Retrieve data</b>
 * getPrimaryAddress(): Address
 * getAddresses(): Collection
 * isUniqueCandidate(): bool
 * getCountOfCandidates(): int
 * hasCandidates(): bool
 *
 * <b>Verify data</b>
 * isValid(): bool Refers to the 'validity' of the main address
 * isError(): bool Refers to the HTTP request involved (if any) or to any other error during the processing of addresses
 * </code>
 */
class AddressResult extends BaseBean
{

    /**
     * Collection of resulting AddressValidation\Data\Address objects
     */
    private ?Collection $addresses;

    /**
     * HTTP headers
     * @var array $http_headers
     */
    private $http_headers = [];

    /**
     * HTTP response code
     * @var int $http_status_code
     */
    private $http_status_code = 200;

    /**
     * Contains the raw body of the HTTP response
     * @var string
     */
    private $http_raw_body = '';

    /**
     * It is set to true when the object carries an error
     * @var bool $error
     */
    private $error = false;

    /**
     * Used for tracing errors
     * @var string $message
     */
    private $message = '';

    /**
     * Creates the model with either a collection or nothing. It will always set
     * an internal Collection
     *
     * @param Collection|null $addresses Collection of AddressValidation\Data\Address objects
     */
    public function __construct(?Collection $addresses = null)
    {
        $this->addresses = $addresses ?? new Collection();
    }

    /**
     * Add one address
     * @param Address $address The address object
     * @return $this
     */
    public function add(Address $address): AddressResult
    {
        $this->addresses->add($address);
        return $this;
    }

    /**
     * Sets the collection of addresses
     * @param Collection $addresses Collection of AddressValidation\Data\Address objects
     * @return $this
     */
    public function setAddresses(Collection $addresses): AddressResult
    {
        $this->addresses = $addresses;
        return $this;
    }

    /**
     * Returns the first element (first candidate: the most accurate)
     * It is usually called when isValid is true or when getCountOfCandidates() == 1
     * to retrieve the unique element
     *
     * @return Address|null
     */
    public function getPrimaryAddress(): ?Address
    {
        return $this->addresses->first();
    }

    /**
     * Returns the current collection of addresses
     * @return Collection
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    /**
     * Returns true when it is only one candidate in the internal list of addresses
     * @return bool
     */
    public function isUniqueCandidate(): bool
    {
        return $this->getCountOfCandidates() == 1;
    }

    /**
     * Returns true when they are candidates with correct address
     * @return bool
     */
    public function hasCandidates(): bool
    {
        return $this->getCountOfCandidates() > 0;
    }

    /**
     * Returns true when they are candidates with correct address
     * @return int
     */
    public function getCountOfCandidates(): int
    {
        return count($this->addresses);
    }

    /**
     * Returns true when the contained address (primary one) is valid
     * @return bool
     */
    public function isValid(): bool
    {
        return !$this->error && $this->getCountOfCandidates() > 0;
    }

    /**
     * Returns true when an HTTP error has been produced. It is not regarding to
     * the validity of the contained address/addresses.
     *
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * Set to true when the current object holds an Error produced during the
     * process of validation.
     *
     * @param bool $error
     * @return $this
     */
    public function setError(bool $error): AddressResult
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Get error message
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set error message
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): AddressResult
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Returns the HTTP request headers
     * @return array
     */
    public function getHttp_headers(): array
    {
        return $this->http_headers;
    }

    /**
     * Returns the HTTP result code
     * @return int
     */
    public function getHttp_status_code(): int
    {
        return $this->http_status_code;
    }

    public function setHttp_headers(array $http_headers): AddressResult
    {
        $this->http_headers = $http_headers;
        return $this;
    }

    public function setHttp_status_code(int $http_status_code): AddressResult
    {
        $this->http_status_code = $http_status_code;
        return $this;
    }

    public function getHttp_raw_body(): string
    {
        return $this->http_raw_body;
    }

    public function setHttp_raw_body(string $http_raw_body): AddressResult
    {
        $this->http_raw_body = $http_raw_body;
        return $this;
    }

    /**
     * Basic conversion of current object to a string representation
     *
     * Intended for logging and NOT for parsing
     *
     * @return string
     */
    public function __toString()
    {
        $sb = new StringBuilder();

        $sb->addKeyValue('http_code', $this->http_status_code);
        $sb->addKeyValueWhen($this->isError(), 'error', $this->isError());
        $sb->addKeyValueWhen($this->isError(), 'message', $this->message);
        $sb->addKeyValue('valid', $this->isValid());
        $sb->addKeyValue('candidates', $this->getCountOfCandidates());

        // Addresses collection
        if ($this->getCountOfCandidates() > 0) {
            $sb_add = new StringBuilder();
            foreach ($this->addresses as $e => $add) {
                $sb_add->addKeyValue($e, (string)$add, ', ');
            }
            $sb->add(', data=[', '')->add($sb_add, '')->add(']', '');
        }

        return $sb->toString();
    }

}
