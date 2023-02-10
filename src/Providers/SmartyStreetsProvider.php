<?php

namespace Alexconesap\AddressValidator\Providers;

use Alexconesap\AddressValidator\Contracts\AddressValidatorInterface;
use Alexconesap\AddressValidator\Contracts\Constants;
use Alexconesap\AddressValidator\Data\Address;
use Alexconesap\AddressValidator\Data\AddressResult;
use Alexconesap\Commons\StringBuilder;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * SMARTY STREETS INTERFACE
 *
 * API Documentation: https://smartystreets.com/docs/cloud/us-street-api#http-response-status
 */
class SmartyStreetsProvider extends AbstractProvider implements Constants
{

    const DRIVER_CONFIG_KEY = Constants::CONFIG_KEY . '.drivers.smartystreets.';

    /**
     * The API will return a single candidate for every properly submitted address, even if invalid or ambiguous.
     * @var string MATCH_INVALID
     */
    const MATCH_INVALID = 'invalid';

    /**
     * The API will ONLY return candidates that are valid USPS addresses.
     * @var string MATCH_STRICT
     */
    const MATCH_STRICT = 'strict';

    /**
     * The API will return candidates that are valid USPS addresses, as well as invalid addresses with primary numbers that fall within a valid range for the street.
     * @var string MATCH_STRICT
     */
    const MATCH_RANGE = 'range';

    /**
     * The match to be used
     * @var string MATCH
     */
    const MATCH = self::MATCH_RANGE;

    /**
     * Validates an address
     *
     * Curl example GET url encoded:
     * <code>
     * curl -v 'https://us-street.api.smartystreets.com/street-address?
     *          auth-id=YOUR+AUTH-ID+HERE&
     *          auth-token=YOUR+AUTH-TOKEN+HERE&
     *          street=1600+amphitheatre+pkwy&
     *          city=mountain+view&
     *          state=CA&
     *          candidates=10'
     * </code>
     *
     * The API returns [] as body when the address is invalid
     *
     * Otherwise, it returns an array of objects when it matches the records
     *
     * @param AddressValidatorInterface $address An object with the address details
     * @return AddressResult
     * @throws GuzzleException
     */
    public function api_call(AddressValidatorInterface $address): AddressResult
    {
        $auth_id = config(self::DRIVER_CONFIG_KEY . 'api_id');
        $auth_token = config(self::DRIVER_CONFIG_KEY . 'api_auth_token');
        $uri = config(self::DRIVER_CONFIG_KEY . 'api_url');

        $params = new StringBuilder('', '&', true);

        // Options
        $params->addKeyValue('auth-id', $auth_id);
        $params->addKeyValue('auth-token', $auth_token);
        $params->addKeyValue('candidates', config(self::DRIVER_CONFIG_KEY . 'candidates'));
        $params->addKeyValue('match', self::MATCH);

        // Address
        $params->addKeyValue('street', $address->getStreet1());
        $params->addKeyValue('secondary', '');
        $params->addKeyValue('state', $address->getState());
        $params->addKeyValue('city', $address->getCity());
        $params->addKeyValue('zipcode', $address->getZip_code());

        $ps = (string)$params;

        $uri_with_params = "$uri?$ps";

        $client = new Client();
        try {
            return $this->parse_results($client->get($uri_with_params));
        } catch (Exception $ex) {
            return $this->parse_error($uri_with_params, $ex);
        }
    }

    /**
     * Parses an error
     * @param string $uri
     * @param Exception $exception
     * @return AddressResult
     */
    private function parse_error(string $uri, Exception $exception): AddressResult
    {
        return (new AddressResult())
            ->setError(true)
            ->setHttp_status_code($exception->getCode())
            ->setMessage($exception->getMessage() . ". API: $uri");
    }

    /**
     * Parses the result got from the API into a valid local AddressResult
     *
     * Possible response (JSON):
     * <code>
     * [
     *    {
     *       "input_index":0,
     *       "candidate_index":0,
     *       "delivery_line_1":"1211 Washington Street",
     *       "last_line":"Milwaukee WI 53295-0001",
     *       "delivery_point_barcode":"532950001113",
     *       "components":{
     *          "primary_number":"1211",
     *          "street_predirection":"W",
     *          "street_name":"National",
     *          "street_suffix":"Ave",
     *          "city_name":"Milwaukee",
     *          "default_city_name":"Milwaukee",
     *          "state_abbreviation":"WI",
     *          "zipcode":"53295",
     *          "plus4_code":"0001",
     *          "delivery_point":"11",
     *          "delivery_point_check_digit":"3"
     *       },
     *       "metadata":{
     *          "record_type":"S",
     *          "zip_type":"Unique",
     *          "county_fips":"55079",
     *          "county_name":"Milwaukee",
     *          "carrier_route":"C000",
     *          "congressional_district":"04",
     *          "rdi":"Commercial",
     *          "elot_sequence":"0001",
     *          "elot_sort":"A",
     *          "latitude":43.02185,
     *          "longitude":-87.97685,
     *          "precision":"Zip9",
     *          "time_zone":"Central",
     *          "utc_offset":-6,
     *          "dst":true
     *       },
     *       "analysis":{
     *          "dpv_match_code":"Y",
     *          "dpv_footnotes":"AAU1",
     *          "footnotes":"Q#X#"
     *       }
     *    }
     * ]
     * </code>
     *
     * @param ResponseInterface $response What we got from the API
     * @return AddressResult
     */
    private function parse_results(ResponseInterface $response): AddressResult
    {
        $body = $response->getBody();

        $result = (new AddressResult())
            ->setHttp_status_code($response->getStatusCode())
            ->setHttp_headers($response->getHeaders())
            ->setHttp_raw_body((string)$body);

        if (!is_null($body)) {
            $elements = json_decode($body, false);

            foreach ($elements as $e) {
                $address = new Address();
                $address->index = (int)$e->input_index;

                $address->street1 = (string)$e->delivery_line_1;
                $address->city = (string)$e->components->city_name;
                $address->state = (string)$e->components->state_abbreviation;
                $address->zip_code = (string)$e->components->zipcode;

                $address->latitude = (float)$e->metadata->latitude;
                $address->longitude = (float)$e->metadata->longitude;

                $result->add($address);
            }
        }

        return $result;
    }

}
