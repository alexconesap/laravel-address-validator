<?php

namespace Alexconesap\AddressValidator\Contracts;

/**
 * Constants required by service provider and core functions
 *
 * @author Yakuma, 2020 <alexconesap@gmail.com>
 * @version 1
 */
interface Constants
{

    const CONFIG_KEY = 'address_validator';
    const CONFIG_FILENAME = self::CONFIG_KEY . '.php';

}
