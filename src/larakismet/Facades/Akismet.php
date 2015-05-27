<?php
/**
 * Larakismet
 *
 * Akismet Client for Laravel 5.
 *
 * Ed Lomonaco
 * https://github.com/eman1986/larakismet
 * MIT License
 */

namespace larakismet\Facades;

use Illuminate\Support\Facades\Facade;

class Akismet extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Akismet';
    }
}