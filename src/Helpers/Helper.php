<?php

namespace Aman\EmailVerifier\Helpers;

class Helper
{
    public const directory = __DIR__ . "/../../resources/domains/";

    /** @var array<string, array<string, bool>> */
    private static $shardCache = array();

    public static function deepCheck(String $domain)
    {
        $normalizedDomain = self::normalizeDomain($domain);
        if ($normalizedDomain === '') {
            return false;
        }

        $parts = explode('.', $normalizedDomain);
        $candidates = array();

        for ($i = 0; $i < count($parts); $i++) {
            $candidate = implode('.', array_slice($parts, $i));
            if ($candidate !== '') {
                $candidates[] = $candidate;
            }
        }

        foreach ($candidates as $candidateDomain) {
            $startingCharacter = strtolower(substr($candidateDomain, 0, 1));

            if (!preg_match('/^[a-z0-9]$/', $startingCharacter)) {
                continue;
            }

            $domainSet = self::loadShardAsSet($startingCharacter);
            if (isset($domainSet[$candidateDomain])) {
                return true;
            }
        }

        return false;
    }

    private static function normalizeDomain($domain)
    {
        $normalizedDomain = strtolower(trim($domain));

        if ($normalizedDomain === '') {
            return '';
        }

        if (strpos($normalizedDomain, '@') !== false) {
            $atPos = strrpos($normalizedDomain, '@');
            $normalizedDomain = substr($normalizedDomain, $atPos + 1);
        }

        return rtrim($normalizedDomain, '.');
    }

    /**
     * @return array<string, bool>
     */
    private static function loadShardAsSet($shardKey)
    {
        if (isset(self::$shardCache[$shardKey])) {
            return self::$shardCache[$shardKey];
        }

        $path = self::directory . $shardKey . '.json';
        if (!is_file($path)) {
            self::$shardCache[$shardKey] = array();
            return self::$shardCache[$shardKey];
        }

        $decoded = json_decode(file_get_contents($path), true);
        if (!is_array($decoded)) {
            self::$shardCache[$shardKey] = array();
            return self::$shardCache[$shardKey];
        }

        $domainSet = array();
        foreach ($decoded as $item) {
            if (is_string($item)) {
                $domainSet[strtolower($item)] = true;
            }
        }

        self::$shardCache[$shardKey] = $domainSet;
        return self::$shardCache[$shardKey];
    }
}
