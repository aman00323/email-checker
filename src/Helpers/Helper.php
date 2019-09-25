<?php

namespace Aman\EmailVerifier\Helpers;

class Helper
{
    public static function deepCheck(String $domain)
    {
        $startingCharacter = strtolower(substr($domain, 0, 1));
        switch ($startingCharacter) {
            case '0':
                $url = "https://gist.githubusercontent.com/aman00323/7550b32d9334313f83fa249b8c72eb42/raw/d6c34c69facfd4a746a60839977af5a595744508/0.json";
                break;
            case '1':
                $url = "https://gist.githubusercontent.com/aman00323/a1d5528be50a326c363d6124a8e5644a/raw/5bc33a72d2a96d5f0342527cef4be2425f608bf7/1.json";
                break;
            case '2':
                $url = "https://gist.githubusercontent.com/aman00323/e86377dd8a9cf242c21bb04708997564/raw/2693b5d91b18b7299deef825d976e873b8b6fd0b/2.json";
                break;
            case '3':
                $url = "https://gist.githubusercontent.com/aman00323/cc0efa32545d22b24d64e7c2d869f192/raw/e697bced78526477e3407e323200f70e5b14b85c/3.json";
                break;
            case '4':
                $url = "https://gist.githubusercontent.com/aman00323/24f90c36d8a4a45bf9da5b3fc0dd10c9/raw/5abf47393585b98a6b688fc2189bc1800ae1c26b/4.json";
                break;
            case '9':
                $url = "https://gist.githubusercontent.com/aman00323/4616904635e4a2c27b80938cc2df63f0/raw/86aca2961cd102b94bb6f604665a066a83768823/9.json";
                break;
            case 'a':
                $url = "https://gist.githubusercontent.com/aman00323/cabaa64263022478c7c68d42d746e2b5/raw/b9508986160ccb2417339955a36927549a3acc82/a.json";
                break;
            case 'b':
                $url = "https://gist.githubusercontent.com/aman00323/43333bac0b8f74639d16817fe5135ca9/raw/5abd474e76094389a8ec98aac667bd76311751d4/b.json";
                break;
            case 'c':
                $url = "https://gist.githubusercontent.com/aman00323/f4ccfa5b3ad08d6085fca8744737147d/raw/dd451a873a2c238586048f6330c63dc22f264a08/c.json";
                break;
            case 'd':
                $url = "https://gist.githubusercontent.com/aman00323/94abf4c0575f55223ff3f9bd57f64da5/raw/25cffcfdcebd5a26113943268fe15dcd79445347/d.json";
                break;
            case 'e':
                $url = "https://gist.githubusercontent.com/aman00323/b41f61d07c8ee91606a3bc7d0a666af1/raw/7ae40e1507827e61061185c6c0ff5ab7c400a824/e.json";
                break;
            case 'f':
                $url = "https://gist.githubusercontent.com/aman00323/ee4d2036450489fb884a2f628f002509/raw/8912481d022aae5426ab579f7cfe6f2cc6ea0f6b/f.json";
                break;
            case 'g':
                $url = "https://gist.githubusercontent.com/aman00323/30e766e164b91a31896d98d686aa7881/raw/6bc196476a3bc30b9be6b344877438cdec1f949f/g.json";
                break;
            case 'h':
                $url = "https://gist.githubusercontent.com/aman00323/b170821a66bdd59ecf2083b45a6e25eb/raw/e02e7aec9bcbe2365b016e8199fb4a976edf2eea/h.json";
                break;
            case 'i':
                $url = "https://gist.githubusercontent.com/aman00323/aa4a84e3ef24d6e81b0200650e799bec/raw/e952769a03192500dbbe961502cb9922f298c336/i.json";
                break;
            case 'j':
                $url = "https://gist.githubusercontent.com/aman00323/f99539b5259668222593bc1f7a0b7bc5/raw/a26598657e53729a0e1ea63b5472521401748c8d/j.json";
                break;
            case 'k':
                $url = "https://gist.githubusercontent.com/aman00323/061f74f1d745e6b7b53b9ce887e74bda/raw/82c4707a7566895bbfdca9e1a66bfe84fb3ebd41/k.json";
                break;
            case 'l':
                $url = "https://gist.githubusercontent.com/aman00323/81ee01fcecacf40621255e3e944fd466/raw/5e1a051535c487edea0d8bac317dc2daf34ba6c2/l.json";
                break;
            case 'm':
                $url = "https://gist.githubusercontent.com/aman00323/3569bd5fc1fe62eafa9070478afd6612/raw/ef7300d388ed60ec6cc63c854a60e5e57046ff49/m.json";
                break;
            case 'n':
                $url = "https://gist.githubusercontent.com/aman00323/d7195298f1b3a43de9add4c02a6ef81e/raw/41a4d8d949b22e279ce1a6225ff7ebcc28d22ca6/n.json";
                break;
            case 'o':
                $url = "https://gist.githubusercontent.com/aman00323/231dee6631bb9c918b25f459a2807524/raw/47fa82693853f1b068a4072c52d495d196ba1418/o.json";
                break;
            case 'p':
                $url = "https://gist.githubusercontent.com/aman00323/6efecb5dc310744fa68920412eaed704/raw/f82b80e4538d76fc90a413c4fdbad2766fcc3c36/p.json";
                break;
            case 'q':
                $url = "https://gist.githubusercontent.com/aman00323/2059b7492b8680991f7a010dc9dc5d1f/raw/e06414c193fab55aeabb9b397f79d670e75444d9/q.json";
                break;
            case 'r':
                $url = "https://gist.githubusercontent.com/aman00323/8af0d42bd7f7a28f9bce31226005eada/raw/8bbc0b1475ef1eccff67720f431a339f11193df0/r.json";
                break;
            case 's':
                $url = "https://gist.githubusercontent.com/aman00323/a53320b73698cb9f0fd950584ad1306c/raw/80afa84b8ac6561a3717717554e386a8ab5f0975/s.json";
                break;
            case 't':
                $url = "https://gist.githubusercontent.com/aman00323/5dc41bbbebbd7775e6c6a4304debf160/raw/87d281b8c33ce5c5eee8918933e8adf1e52abd43/t.json";
                break;
            case 'u':
                $url = "https://gist.githubusercontent.com/aman00323/4a58e775d8841037cc2420055b69df7a/raw/af84ed99dccab7fb4f7cb7ec9a5bae23f7096ee0/u.json";
                break;
            case 'v':
                $url = "https://gist.githubusercontent.com/aman00323/e019b8059addce2d3f5ef19fc14abdb1/raw/d1d7c32c9c7ff30d1339dc6e9ea79d7ef4f2aebc/v.json";
                break;
            case 'w':
                $url = "https://gist.githubusercontent.com/aman00323/4947d64ba553d3251275ab74ce3aa607/raw/96dd2f4adfa82e4eae84692ffb196cdd10948e44/w.json";
                break;
            case 'x':
                $url = "https://gist.githubusercontent.com/aman00323/33bdf608e139be2abfaacfc63d88460f/raw/043571a7849c4ae5872d26b9328b88d213a03ba9/x.json";
                break;
            case 'y':
                $url = "https://gist.githubusercontent.com/aman00323/77ad143b07e73e7f6b7f3baf8c79d305/raw/c84d6145105bc4b3c50c9e65bf36401c508380b9/y.json";
                break;
            case 'z':
                $url = "https://gist.githubusercontent.com/aman00323/6c02172e91fe1365d76cdf10ba8248c0/raw/1d3cb8c05a0dae9eaec332d70ba5ebf9c769345c/z.json";
                break;
            default:
                return false;
        }
        $data = json_decode(file_get_contents($url), true);
        for ($i = 0; $i < count($data); $i++) {
            if (preg_match("/(" . $data[$i] . ")/i", $domain)) {
                return true;
            }
        }
        return false;

    }
}
