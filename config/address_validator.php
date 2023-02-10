<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Current Driver for ADDRESS VALIDATIONS
      |--------------------------------------------------------------------------
      |
      | It defines the current address validator driver to be used:
      |
      | 'fake'
      | 'smarty-streets' >> Composer require "smartystreets/phpsdk": "4.7.1"
      |
      | Set to null or keep it as a blank string to disable the system.
      |
     */
    'driver' => env('ADDRESS_VALIDATOR_DRIVER'),
    /*
      |--------------------------------------------------------------------------
      | Drivers configuration
      |--------------------------------------------------------------------------
      |
      | Each provider may have its own configuration entries
      |
     */
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
];
