<?php

namespace Aman\EmailVerifier\Helpers;

class Helper
{
    public const directory = __DIR__ . "/../../resources/domains/";

    public static function deepCheck(String $domain)
    {
        $startingCharacter = strtolower(substr($domain, 0, 1));
        if (preg_match("/^[a-zA-Z0-9]+$/", $startingCharacter) == 1) {
            $data = json_decode(file_get_contents(Self::directory . $startingCharacter . ".json"), true);
            for ($i = 0; $i < count($data); $i++) {
                if (preg_match("/(" . $data[$i] . ")/i", $domain)) {
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }
}
