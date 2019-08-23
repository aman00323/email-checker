<?php

namespace Aman\EmailVerifier;

use Illuminate\Support\Facades\Facade;

class EmailCheckerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'emailchecker';
    }
}
