# Package laravel-address-validator

Easily extensible Address Validation manager for Laravel.

It provides two default 'providers' `Smarty Streets` and `Fake` but any other may be easily supported extending
the `AbstractProvider` class.

## Dependencies

This package requires 'alexconesap/laravel-commons'

## Setup

Install using composer:

```
composer require alexconesap/laravel-address-validator
```

Since it is not currently available at `https://packagist.org/`, follow up these steps:

- 1 Clone this repository in a local folder. A simple idea is to create a folder in the parent folder for your project.
  Let us say you clone the repo in a folder named `dev-address-validator` (or any other you want) following this structure:

```text
   | your_project_folder
   | composer_local_repos
    --| dev-address-validator
```

- 2 Reference the package locally at the host project `composer.json` file as follows (extract of the file):
```json
{
  ...
  "require": {
    "alexconesap/laravel-address-validator": "~1.0",
    "alexconesap/laravel-commons": "~1.0"
  },
  "repositories": [
    {
      "type": "path",
      "url": "../composer_local_repos/dev-laravel-commons"
    },
    {
      "type": "path",
      "url": "../composer_local_repos/dev-address-validator"
    }
  ],
  ...
}
```

## Use examples

```
 try {
    $validator = new AddressValidationManager(new Providers\SmartyStreetsProvider());
    $candidates = $validator->validate(
       new Address('One street, in a city, from a state, zip code')
    );
    info($candidates)
 } catch (Exception $ex) {
    // ...
 }
```

Using the helper `yaddress`:

```
 try {
    $candidates = yaddress()->validate(
       new Address('One street, in a city, from a state, zip code')
    );
    info($candidates)
 } catch (Exception $ex) {
    // ...
 }
```

## .env

```ini
# Available drivers: fake, smartystreets (or keep it empty to disable it)
ADDRESS_VALIDATOR_DRIVER = smartystreets

# Smarty API. Keys required
SMARTY_STREETS_API_ID = "Set Here your smarty streets API ID"
SMARTY_STREETS_API_AUTH_TOKEN = "Set here your auth token provided by Smarty Streets"
# Number of candidates to be returned by the provider
SMARTY_STREETS_CANDIDATES = 5

# Use only in development env to enable tests
PHPUNIT_ADDRESS_VALIDATOR = true
```

The [configuration file](config/address_validator.php) contains one key that will match the `ADDRESS_VALIDATOR_DRIVER`
in its internal drivers definition array.

Example:

```php
    'drivers' => [
        'smartystreets' => [
            'api_url' => 'https://us-street.api.smartystreets.com/street-address',
            'class' => \Alexconesap\AddressValidator\Providers\SmartyStreetsProvider::class,
            'api_id' => env('SMARTY_STREETS_API_ID'),
            'api_auth_token' => env('SMARTY_STREETS_API_AUTH_TOKEN'),
            'candidates' => env('SMARTY_STREETS_CANDIDATES', 5),
        ],
        'fake' => [
            'class' => \Alexconesap\AddressValidator\Providers\FakeProvider::class,
        ]
    ]
```

For extending the package just create a class that extends from `AbstractProvider` and that implements the communication
with a different street validation provider.

You can then add your own `.env` keys as well as a new entry to the `address_validator.php` configuration file.

## Laravel auto-binding

Since the AddressValidationManager expects a `AddressValidationInterface` compliant' class, the Laravel IoC auto-binding
may be easily used.

## Contributions

Any comments or contributions are welcomed!