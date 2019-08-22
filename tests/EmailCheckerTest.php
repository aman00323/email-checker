<?php

namespace Aman\EmailVerifier\Tests;

use Aman\EmailVerifier\EmailChecker;

class EmailCheckerTest extends TestCase
{
    public function testCheckDisposableEmail()
    {
        $emailChecker = new EmailChecker();
        $dispossibleEmail = $this->disposableEmailList();
        for ($i = 0; $i < count($dispossibleEmail); $i++) {
            self::assertTrue(
                $emailChecker->checkDisposableEmail($dispossibleEmail[$i])
            );
        }
        $emailList = $this->emailList();
        for ($i = 0; $i < count($emailList); $i++) {
            self::assertFalse(
                $emailChecker->checkDisposableEmail($emailList[$i])
            );
        }
    }

    public function testCheckMxAndDnsRecord()
    {
        //This test takes time due to fsockopen()
        $emailChecker = new EmailChecker();
        $emailList = $this->emailList();
        for ($i = 0; $i < count($emailList); $i++) {
            $response = $emailChecker->checkMxAndDnsRecord($emailList[$i]);
            var_dump($response,$emailList[$i]);

            //self::assertEquals($response[0], 'valid');
        }

    }

    public function testCheckEmail()
    {
        $emailChecker = new EmailChecker();
        $emailList = $this->emailList();
        for ($i = 0; $i < count($emailList); $i++) {
            $response = $emailChecker->checkEmail($emailList[$i]);
            self::assertTrue($response['success']);
            self::assertTrue($response['dispossable']['success']);
            self::assertTrue($response['mxrecord']['success']);
            self::assertTrue($response['domain']['success']);
        }
        $dispossibleEmail = $this->disposableEmailList();
        for ($i = 0; $i < count($dispossibleEmail); $i++) {
            $response = $emailChecker->checkEmail($dispossibleEmail[$i]);
            self::assertFalse($response['success']);
        }
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
            'aman@improwised.com',
            'jakehowlet@gmail.com',
            'temp@yahoo.com',
            'something@outlook.com',
            'anything@yahoo.com',
        );
    }

}
