<?php

namespace Tests;

use Alexconesap\AddressValidator\AddressValidatorManager;
use Alexconesap\AddressValidator\Contracts\Constants;
use Alexconesap\AddressValidator\Data\Address;
use Alexconesap\AddressValidator\Data\AddressResult;
use Alexconesap\AddressValidator\Exceptions\ProviderNotConfiguredException;
use Alexconesap\AddressValidator\Providers\SmartyStreetsProvider;

class AddressValidatorTest extends TestCase implements Constants
{

    public function testDriverFakeUsingHelper()
    {
        $this->app->forgetInstance('address_validator');
        $this->app['config']->set('address_validator.driver', 'fake');

        self::assertTrue(yaddress_available());
        self::assertFalse(yaddress()->validate(new Address('Test'))->isError());
    }

    public function testDriverFake()
    {
        $this->app->forgetInstance('address_validator');
        $this->app['config']->set('address_validator.driver', 'fake');
        /** @var AddressValidatorManager $manager */
        $manager = $this->app->get('address_validator');
        self::assertFalse($manager->validate(new Address('Test'))->isError());
    }

    public function testDriverNullWhenNoDriverSet()
    {
        $this->app->forgetInstance('address_validator');
        $this->app['config']->set('address_validator.driver', '');

        $this->expectException(ProviderNotConfiguredException::class);

        /** @var AddressValidatorManager $manager */
        $manager = $this->app->get('address_validator');
        $manager->validate(new Address('Test'));
    }

    public function testAddressDatamodelConstructor()
    {
        // The optional full address US format (street, city, state, zip)
        $a = new Address('Street name A1, City name, State Name,000000');

        self::assertEquals('Street name A1', $a->street1);
        self::assertEquals('City name', $a->city);
        self::assertEquals('State Name', $a->state);
        self::assertEquals('000000', $a->zip_code);
        self::assertNull($a->country);
        self::assertNull($a->place_name);
        self::assertNull($a->street2);

        self::assertEquals("Street name A1, City name, State Name, 000000", (string)$a);

        $a->street2 = 'Second str';
        self::assertEquals("Street name A1, Second str, City name, State Name, 000000", (string)$a);
    }

    public function testAddressDatamodelAttributes()
    {
        $a = new Address();

        self::assertEquals("", (string)$a);

        $a->street1 = 'Street name A1';
        self::assertEquals("Street name A1", (string)$a);

        $a->street2 = 'Second str';
        self::assertEquals("Street name A1, Second str", (string)$a);

        $a->city = 'City name';
        self::assertEquals("Street name A1, Second str, City name", (string)$a);

        $a->state = 'State Name';
        self::assertEquals("Street name A1, Second str, City name, State Name", (string)$a);

        $a->zip_code = '000000';
        self::assertEquals("Street name A1, Second str, City name, State Name, 000000", (string)$a);

        self::assertEquals('Street name A1', $a->street1);
        self::assertEquals('Second str', $a->street2);
        self::assertEquals('City name', $a->city);
        self::assertEquals('State Name', $a->state);
        self::assertEquals('000000', $a->zip_code);
        self::assertNull($a->country);
        self::assertNull($a->place_name);
    }

    public function testResultCollectionOfCandidates()
    {
        $candidates_collection = new AddressResult();
        self::assertCount(0, $candidates_collection->getAddresses());
        self::assertEquals(0, $candidates_collection->getCountOfCandidates());
        self::assertFalse($candidates_collection->isValid(), 'An empty list should not be considered valid');

        $candidates_collection->add((new Address('Street name A1, City name, State Name,000000'))->setIndex(0));
        self::assertCount(1, $candidates_collection->getAddresses());
        self::assertEquals(1, $candidates_collection->getCountOfCandidates());
        self::assertEquals(0, $candidates_collection->getAddresses()->first()->index);
        self::assertTrue($candidates_collection->isValid());

        $candidates_collection->add((new Address('Street name A2, City name, State Name, 000001'))->setIndex(33));
        self::assertCount(2, $candidates_collection->getAddresses());
        self::assertEquals(2, $candidates_collection->getCountOfCandidates());
        self::assertEquals(33, $candidates_collection->getAddresses()->last()->index);
        self::assertTrue($candidates_collection->isValid());

        // Test error conditions
        $candidates_collection->setError(true);
        self::assertTrue($candidates_collection->isError());
        self::assertFalse($candidates_collection->isValid());
    }

    public function testSmartyStreetsNotConfigured()
    {
        config(['address_validator.drivers.smartystreets' => [
            'api_url' => 'https://us-street.api.smartystreets.com/street-address',
            'class' => SmartyStreetsProvider::class,
            'api_id' => 'no_api_id',
            'api_auth_token' => 'no_token_set',
            'candidates' => 5,
        ]]);

        $validator = new AddressValidatorManager(new SmartyStreetsProvider());
        $candidates = $validator->validate(
            new Address('One street, in a city, from a state, zip code')
        );

        self::assertTrue($candidates->isError());
        self::assertStringContainsString('Authentication required', $candidates->getMessage());
        self::assertEquals(401, $candidates->getHttp_status_code());
    }

    public function testHelperExists()
    {
        if (!env('PHPUNIT_ADDRESS_VALIDATOR')) {
            $this->markTestSkipped('PHPUNIT abort testing of AddressValidationTest. Set env("PHPUNIT_ADDRESS_VALIDATOR") to true');
        }

        $this->assertTrue(function_exists('yaddress'));
    }

    public function testWrongAddressHelper()
    {
        if (!env('PHPUNIT_ADDRESS_VALIDATOR')) {
            $this->markTestSkipped('PHPUNIT abort testing of AddressValidationTest. Set env("PHPUNIT_ADDRESS_VALIDATOR") to true');
        }

        $address = new Address('1000 WRONG STREET NAME, WRONG CITY, WRONG ZIP');

        $val = yaddress()->validate($address);

        $this->assertNotNull($val);
        $this->assertFalse($val->isError(), $val->getMessage());
        $this->assertFalse($val->isValid(), $val->getMessage());

        $this->assertEquals(0, $val->getCountOfCandidates());
    }

    public function testValidAddressHelper()
    {
        if (!env('PHPUNIT_ADDRESS_VALIDATOR')) {
            $this->markTestSkipped('PHPUNIT abort testing of AddressValidationTest. Set env("PHPUNIT_ADDRESS_VALIDATOR") to true');
        }

        $address = new Address('1 Apple Park Way, Cupertino, CA 95014');

        $val = yaddress()->validate($address);

        $this->assertNotNull($val);

        $this->assertFalse($val->isError(), $val->getMessage());
        $this->assertTrue($val->isValid(), $val->getMessage());

        $this->assertEquals(1, $val->getCountOfCandidates());

        $primary = $val->getPrimaryAddress();

        $this->assertEquals("1 Apple Park Way", $primary->street1);
        $this->assertEquals("Cupertino", $primary->city);
        $this->assertEquals("95014", $primary->zip_code);
    }

}
