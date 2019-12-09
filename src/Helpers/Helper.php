<?php

namespace Aman\EmailVerifier\Helpers;

class Helper
{
    public static function deepCheck(String $domain)
    {
        $startingCharacter = strtolower(substr($domain, 0, 1));
        switch ($startingCharacter) {
            case '0':
                $url = "https://gist.githubusercontent.com/aman00323/7550b32d9334313f83fa249b8c72eb42/raw";
                break;
            case '1':
                $url = "https://gist.githubusercontent.com/aman00323/a1d5528be50a326c363d6124a8e5644a/raw";
                break;
            case '2':
                $url = "https://gist.githubusercontent.com/aman00323/e86377dd8a9cf242c21bb04708997564/raw";
                break;
            case '3':
                $url = "https://gist.githubusercontent.com/aman00323/cc0efa32545d22b24d64e7c2d869f192/raw";
                break;
            case '4':
                $url = "https://gist.githubusercontent.com/aman00323/24f90c36d8a4a45bf9da5b3fc0dd10c9/raw";
                break;
            case '5':
                $url = "https://gist.githubusercontent.com/aman00323/06d9c0e0c9f47a33badb1bd69ff3dc77/raw";
                break;
            case '6':
                $url = "https://gist.githubusercontent.com/aman00323/bd5657024f1e378ee92d9ce39074d474/raw";
                break;
            case '7':
                $url = "https://gist.githubusercontent.com/aman00323/9937bdfba667040c4c68da473419982c/raw";
                break;
            case '8':
                $url = "https://gist.githubusercontent.com/aman00323/b3214e77a96fb0a2097612f6197efb2e/raw";
                break;
            case '9':
                $url = "https://gist.githubusercontent.com/aman00323/4616904635e4a2c27b80938cc2df63f0/raw";
                break;
            case 'a':
                $url = "https://gist.githubusercontent.com/aman00323/cabaa64263022478c7c68d42d746e2b5/raw";
                break;
            case 'b':
                $url = "https://gist.githubusercontent.com/aman00323/43333bac0b8f74639d16817fe5135ca9/raw";
                break;
            case 'c':
                $url = "https://gist.githubusercontent.com/aman00323/f4ccfa5b3ad08d6085fca8744737147d/raw";
                break;
            case 'd':
                $url = "https://gist.githubusercontent.com/aman00323/94abf4c0575f55223ff3f9bd57f64da5/raw";
                break;
            case 'e':
                $url = "https://gist.githubusercontent.com/aman00323/b41f61d07c8ee91606a3bc7d0a666af1/raw";
                break;
            case 'f':
                $url = "https://gist.githubusercontent.com/aman00323/ee4d2036450489fb884a2f628f002509/raw";
                break;
            case 'g':
                $url = "https://gist.githubusercontent.com/aman00323/30e766e164b91a31896d98d686aa7881/raw";
                break;
            case 'h':
                $url = "https://gist.githubusercontent.com/aman00323/b170821a66bdd59ecf2083b45a6e25eb/raw";
                break;
            case 'i':
                $url = "https://gist.githubusercontent.com/aman00323/aa4a84e3ef24d6e81b0200650e799bec/raw";
                break;
            case 'j':
                $url = "https://gist.githubusercontent.com/aman00323/f99539b5259668222593bc1f7a0b7bc5/raw";
                break;
            case 'k':
                $url = "https://gist.githubusercontent.com/aman00323/061f74f1d745e6b7b53b9ce887e74bda/raw";
                break;
            case 'l':
                $url = "https://gist.githubusercontent.com/aman00323/81ee01fcecacf40621255e3e944fd466/raw";
                break;
            case 'm':
                $url = "https://gist.githubusercontent.com/aman00323/3569bd5fc1fe62eafa9070478afd6612/raw";
                break;
            case 'n':
                $url = "https://gist.githubusercontent.com/aman00323/d7195298f1b3a43de9add4c02a6ef81e/raw";
                break;
            case 'o':
                $url = "https://gist.githubusercontent.com/aman00323/231dee6631bb9c918b25f459a2807524/raw";
                break;
            case 'p':
                $url = "https://gist.githubusercontent.com/aman00323/6efecb5dc310744fa68920412eaed704/raw";
                break;
            case 'q':
                $url = "https://gist.githubusercontent.com/aman00323/2059b7492b8680991f7a010dc9dc5d1f/raw";
                break;
            case 'r':
                $url = "https://gist.githubusercontent.com/aman00323/8af0d42bd7f7a28f9bce31226005eada/raw";
                break;
            case 's':
                $url = "https://gist.githubusercontent.com/aman00323/a53320b73698cb9f0fd950584ad1306c/raw";
                break;
            case 't':
                $url = "https://gist.githubusercontent.com/aman00323/5dc41bbbebbd7775e6c6a4304debf160/raw";
                break;
            case 'u':
                $url = "https://gist.githubusercontent.com/aman00323/4a58e775d8841037cc2420055b69df7a/raw";
                break;
            case 'v':
                $url = "https://gist.githubusercontent.com/aman00323/e019b8059addce2d3f5ef19fc14abdb1/raw";
                break;
            case 'w':
                $url = "https://gist.githubusercontent.com/aman00323/4947d64ba553d3251275ab74ce3aa607/raw";
                break;
            case 'x':
                $url = "https://gist.githubusercontent.com/aman00323/33bdf608e139be2abfaacfc63d88460f/raw";
                break;
            case 'y':
                $url = "https://gist.githubusercontent.com/aman00323/77ad143b07e73e7f6b7f3baf8c79d305/raw";
                break;
            case 'z':
                $url = "https://gist.githubusercontent.com/aman00323/6c02172e91fe1365d76cdf10ba8248c0/raw";
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
