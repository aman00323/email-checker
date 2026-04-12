<?php

declare(strict_types=1);

namespace Aman\EmailVerifier\Tests;

use Aman\EmailVerifier\EmailChecker;

class EmailCheckerTest extends TestCase
{
    /**
     * @param array{0: string, 1: string} $mxResult
     */
    private function fakeChecker(bool $domainResult = true, array $mxResult = ['valid', 'Valid email address']): EmailChecker
    {
        return new class ($domainResult, $mxResult) extends EmailChecker {
            private bool $domainResult;

            /** @var array{0: string, 1: string} */
            private array $mxResult;

            /**
             * @param array{0: string, 1: string} $mxResult
             */
            public function __construct(bool $domainResult, array $mxResult)
            {
                $this->domainResult = $domainResult;
                $this->mxResult = $mxResult;
            }

            public function checkDomain(string $email): bool
            {
                return $this->domainResult;
            }

            /**
             * @return array{0: string, 1: string}
             */
            public function checkMxAndDnsRecord(string $email): array
            {
                return $this->mxResult;
            }
        };
    }

    public function testCheckDisposableEmail(): void
    {
        $emailChecker = new EmailChecker();
        $dispossibleEmail = $this->disposableEmailList();
        for ($i = 0; $i < count($dispossibleEmail); $i++) {
            self::assertTrue(
                $emailChecker->checkDisposableEmail($dispossibleEmail[$i], true)
            );
        }
        $emailList = $this->emailList();
        for ($i = 0; $i < count($emailList); $i++) {
            self::assertFalse(
                $emailChecker->checkDisposableEmail($emailList[$i])
            );
        }
    }

    public function testCheckDomain(): void
    {
        $emailChecker = $this->fakeChecker(true);
        self::assertTrue($emailChecker->checkDomain('user@example.com'));

        $invalidDomainChecker = $this->fakeChecker(false);
        self::assertFalse($invalidDomainChecker->checkDomain('user@example.com'));

        self::assertFalse((new EmailChecker())->checkDomain('invalid-email'));

    }

    public function testCheckEmailReturnsFalseForDisposableEmail(): void
    {
        $emailChecker = new EmailChecker();
        $dispossibleEmail = $this->disposableEmailList();
        for ($i = 0; $i < count($dispossibleEmail); $i++) {
            $response = $emailChecker->checkEmail($dispossibleEmail[$i]);
            self::assertFalse($response['success']);
        }
    }

    public function testCheckEmailReturnsSuccessPayloadForValidInputs(): void
    {
        $emailChecker = $this->fakeChecker(true, ['valid', 'Valid email address']);
        $response = $emailChecker->checkEmail('user@gmail.com');

        self::assertTrue($response['success']);
        self::assertArrayHasKey('disposable', $response);
        self::assertArrayHasKey('dispossable', $response);
        self::assertArrayHasKey('mxrecord', $response);
        self::assertArrayHasKey('domain', $response);
        self::assertSame($response['disposable'], $response['dispossable']);
    }

    public function testSplitEmailKeepsLegacyAndModernDomainPropertiesInSync(): void
    {
        $emailChecker = new EmailChecker();
        $emailChecker->checkDisposableEmail('temp@yahoo.com');

        self::assertSame('yahoo.com', $emailChecker->domain);
        self::assertSame($emailChecker->domain, $emailChecker->domian);
    }

    public function testCheckEmailReturnsErrorWhenMxValidationFails(): void
    {
        $emailChecker = $this->fakeChecker(true, ['invalid', 'No suitable MX records found.']);
        $response = $emailChecker->checkEmail('user@gmail.com');

        self::assertFalse($response['success']);
        self::assertSame('Entered email address has no MX and DNS record.', $response['error']);
    }

    public function testCheckEmailReturnsErrorWhenDomainValidationFails(): void
    {
        $emailChecker = $this->fakeChecker(false, ['valid', 'Valid email address']);
        $response = $emailChecker->checkEmail('user@gmail.com');

        self::assertFalse($response['success']);
        self::assertSame('Unable to verify email domain.', $response['error']);
    }

    public function testCheckMxAndDnsRecordRejectsInvalidEmail(): void
    {
        $emailChecker = new EmailChecker();
        $response = $emailChecker->checkMxAndDnsRecord('invalid-email');

        self::assertSame(['invalid', 'Validation error email address.'], $response);
    }

    public function testSmtpProbeConfigurationMethods(): void
    {
        $emailChecker = new EmailChecker();

        self::assertTrue($emailChecker->isSmtpProbeEnabled());

        $emailChecker->setSmtpProbeEnabled(false)
            ->setSmtpPort(2525)
            ->setSmtpTimeoutSeconds(10);

        self::assertFalse($emailChecker->isSmtpProbeEnabled());
        self::assertSame(2525, $emailChecker->getSmtpPort());
        self::assertSame(10, $emailChecker->getSmtpTimeoutSeconds());
    }

    public function testContainerResolvedCheckerAppliesConfigDefaults(): void
    {
        $this->app['config']->set('emailchecker', [
            'smtp_probe' => false,
            'smtp_port' => 2526,
            'smtp_timeout_seconds' => 9,
            'from_email' => 'package@test.dev',
        ]);

        /** @var EmailChecker $checker */
        $checker = $this->app->make('emailchecker');

        self::assertFalse($checker->isSmtpProbeEnabled());
        self::assertSame(2526, $checker->getSmtpPort());
        self::assertSame(9, $checker->getSmtpTimeoutSeconds());
        self::assertSame('package@test.dev', $checker->email_from);
    }

    /**
     * @return array<int, string>
     */
    private function disposableEmailList(): array
    {
        //TODO : add list support for dispossable emails
        return [
            'df@mailinator.com',
            'df@spamevader.com',
            'temp@tempmail.com',
            'something@trbvm.com',
            'anything@boximail.com',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function emailList(): array
    {
        //Some email address with the valid domain
        return [
            'temp@yahoo.com',
            'something@outlook.com',
            'anything@yahoo.com',
        ];
    }

}
