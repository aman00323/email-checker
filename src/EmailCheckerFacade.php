<?php

declare(strict_types=1);

namespace Aman\EmailVerifier;

use Illuminate\Support\Facades\Facade;

class EmailCheckerFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'emailchecker';
    }
}
