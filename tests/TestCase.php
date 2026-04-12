<?php

declare(strict_types=1);

namespace Aman\EmailVerifier\Tests;

use Aman\EmailVerifier\EmailCheckerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            EmailCheckerServiceProvider::class,
        ];
    }
}
