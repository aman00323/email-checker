<?php

namespace Aman\EmailVerifier\Tests;

use Aman\EmailVerifier\EmailChecker;

class EmailCheckerTest extends TestCase
{
    private function fakeChecker($domainResult = true, $mxResult = array('valid', 'Valid email address'))
    {
        return new class($domainResult, $mxResult) extends EmailChecker {
            private $domainResult;
            private $mxResult;

            public function __construct($domainResult, $mxResult)
            {
                $this->domainResult = $domainResult;
                $this->mxResult = $mxResult;
            }

            public function checkDomain($email)
            {
                return $this->domainResult;
            }

            public function checkMxAndDnsRecord($email)
            {
                return $this->mxResult;
            }
        };
    }

    public function testCheckDisposableEmail()
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

    public function testCheckDomain()
    {
        $emailChecker = $this->fakeChecker(true);
        self::assertTrue($emailChecker->checkDomain('user@example.com'));

        $invalidDomainChecker = $this->fakeChecker(false);
        self::assertFalse($invalidDomainChecker->checkDomain('user@example.com'));

        self::assertFalse((new EmailChecker())->checkDomain('invalid-email'));

    }

    public function testCheckEmailReturnsFalseForDisposableEmail()
    {
        $emailChecker = new EmailChecker();
        $dispossibleEmail = $this->disposableEmailList();
        for ($i = 0; $i < count($dispossibleEmail); $i++) {
            $response = $emailChecker->checkEmail($dispossibleEmail[$i]);
            self::assertFalse($response['success']);
        }
    }

    public function testCheckEmailReturnsSuccessPayloadForValidInputs()
    {
        $emailChecker = $this->fakeChecker(true, array('valid', 'Valid email address'));
        $response = $emailChecker->checkEmail('user@gmail.com');

        self::assertTrue($response['success']);
        self::assertArrayHasKey('disposable', $response);
        self::assertArrayHasKey('dispossable', $response);
        self::assertArrayHasKey('mxrecord', $response);
        self::assertArrayHasKey('domain', $response);
        self::assertSame($response['disposable'], $response['dispossable']);
    }

    public function testSplitEmailKeepsLegacyAndModernDomainPropertiesInSync()
    {
        $emailChecker = new EmailChecker();
        $emailChecker->checkDisposableEmail('temp@yahoo.com');

        self::assertSame('yahoo.com', $emailChecker->domain);
        self::assertSame($emailChecker->domain, $emailChecker->domian);
    }

    public function testCheckEmailReturnsErrorWhenMxValidationFails()
    {
        $emailChecker = $this->fakeChecker(true, array('invalid', 'No suitable MX records found.'));
        $response = $emailChecker->checkEmail('user@gmail.com');

        self::assertFalse($response['success']);
        self::assertSame('Entered email address has no MX and DNS record.', $response['error']);
    }

    public function testCheckEmailReturnsErrorWhenDomainValidationFails()
    {
        $emailChecker = $this->fakeChecker(false, array('valid', 'Valid email address'));
        $response = $emailChecker->checkEmail('user@gmail.com');

        self::assertFalse($response['success']);
        self::assertSame('Unable to verify email domain.', $response['error']);
    }

    public function testCheckMxAndDnsRecordRejectsInvalidEmail()
    {
        $emailChecker = new EmailChecker();
        $response = $emailChecker->checkMxAndDnsRecord('invalid-email');

        self::assertSame(array('invalid', 'Validation error email address.'), $response);
    }

    private function disposableEmailList()
    {
        //TODO : add list support for dispossable emails
        return array(
            'df@mailinator.com',
            'df@spamevader.com',
            'temp@tempmail.com',
            'something@trbvm.com',
            'anything@boximail.com',
        );
    }

    private function emailList()
    {
        //Some email address with the valid domain
        return array(
            'temp@yahoo.com',
            'something@outlook.com',
            'anything@yahoo.com',
        );
    }

}
