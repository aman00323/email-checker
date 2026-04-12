<?php

declare(strict_types=1);

namespace Aman\EmailVerifier\Tests;

use Aman\EmailVerifier\EmailCheckerServiceProvider;

class EmailCheckerServiceProviderTest extends TestCase
{
    public function testProvidesReturnsEmailcheckerBinding(): void
    {
        $provider = new EmailCheckerServiceProvider($this->app);

        self::assertSame(['emailchecker'], $provider->provides());
    }
}
