<?php

namespace Aman\EmailVerifier\Helpers;

class Helper
{
    public const directory = __DIR__."/../../resources/domains";

    public static function disposableDomains() {
        return [
            "0" => Self::directory."/0.txt",
            "1" => Self::directory."/1.txt",
            "2" => Self::directory."/2.txt",
            "3" => Self::directory."/3.txt",
            "4" => Self::directory."/4.txt",
            "5" => Self::directory."/5.txt",
            "6" => Self::directory."/6.txt",
            "7" => Self::directory."/7.txt",
            "8" => Self::directory."/8.txt",
            "9" => Self::directory."/9.txt",
            "a" => Self::directory."/a.txt",
            "b" => Self::directory."/b.txt",
            "c" => Self::directory."/c.txt",
            "d" => Self::directory."/d.txt",
            "e" => Self::directory."/e.txt",
            "f" => Self::directory."/f.txt",
            "g" => Self::directory."/g.txt",
            "h" => Self::directory."/h.txt",
            "i" => Self::directory."/i.txt",
            "j" => Self::directory."/j.txt",
            "k" => Self::directory."/k.txt",
            "l" => Self::directory."/l.txt",
            "m" => Self::directory."/m.txt",
            "n" => Self::directory."/n.txt",
            "o" => Self::directory."/o.txt",
            "p" => Self::directory."/p.txt",
            "q" => Self::directory."/q.txt",
            "r" => Self::directory."/r.txt",
            "s" => Self::directory."/s.txt",
            "t" => Self::directory."/t.txt",
            "u" => Self::directory."/u.txt",
            "v" => Self::directory."/v.txt",
            "w" => Self::directory."/w.txt",
            "x" => Self::directory."/x.txt",
            "y" => Self::directory."/y.txt",
            "z" => Self::directory."/z.txt",
        ];
    }

    public static function deepCheck(String $domain)
    {
        $startingCharacter = strtolower(substr($domain, 0, 1));
        $disposableDomains = Helper::disposableDomains();

        if (!array_key_exists($startingCharacter, $disposableDomains)) {
            return false;
        }

        $data = json_decode(file_get_contents($disposableDomains[$startingCharacter]), true);

        for ($i = 0; $i < count($data); $i++) {
            if (preg_match("/(" . $data[$i] . ")/i", $domain)) {
                return true;
            }
        }

        return false;
    }
}
