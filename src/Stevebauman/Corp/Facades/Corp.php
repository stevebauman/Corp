<?php

namespace Stevebauman\Corp\Facades;

use Illuminate\Support\Facades\Facade;

class Corp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'corp';
    }
}
