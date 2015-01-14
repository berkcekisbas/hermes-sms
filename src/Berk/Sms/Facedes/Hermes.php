<?php namespace Berk\Sms\Facades;

use Illuminate\Support\Facades\Facade;

class Hermes extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'hermes'; }

}