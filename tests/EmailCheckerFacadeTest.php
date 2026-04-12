<?php

declare(strict_types=1);

namespace Aman\EmailVerifier\Tests;

use Aman\EmailVerifier\EmailChecker;
use Aman\EmailVerifier\EmailCheckerFacade;
use Illuminate\Support\Facades\Facade;

class EmailCheckerFacadeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Facade::setFacadeApplication($this->app);
    }

    public function testFacadeAccessorIsEmailchecker(): void
    {
        $method = new \ReflectionMethod(EmailCheckerFacade::class, 'getFacadeAccessor');
        $method->setAccessible(true);

        self::assertSame('emailchecker', $method->invoke(null));
    }

    public function testFacadeResolvesUnderlyingEmailCheckerInstance(): void
    {
        $root = EmailCheckerFacade::getFacadeRoot();

        self::assertInstanceOf(EmailChecker::class, $root);
        self::assertTrue($root->isSmtpProbeEnabled());
    }
}
