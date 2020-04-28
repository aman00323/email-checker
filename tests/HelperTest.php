<?php

namespace Aman\EmailVerifier\Tests;

use Aman\EmailVerifier\Helpers\Helper;

class HelperTest extends TestCase
{
    public function testDeepCheck()
    {
        $dispossibleEmail = $this->disposableEmailList();
        for ($i = 0; $i < count($dispossibleEmail); $i++) {
            self::assertTrue(
                Helper::deepCheck($this->splitEmail($dispossibleEmail[$i]))
            );
        }
    }

    public function testDisposableDomainFileExists()
    {
        $fileName = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $path = __DIR__ . "/../resources/domains/";
        foreach ($fileName as $row) {
            self::assertFileExists($path . $row . '.json');
        }
    }

    private function splitEmail($email)
    {
        return substr(strrchr($email, "@"), 1);
    }

    private function disposableEmailList()
    {
        return array(
            'df@mailinator.com',
            'df@safe-mail.net',
            'temp@tempmail.com',
            'something@trbvm.com',
            'anything@boximail.com',
        );
    }

}
