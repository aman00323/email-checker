<?php

declare(strict_types=1);

namespace Aman\EmailVerifier\Tests;

use Aman\EmailVerifier\Helpers\Helper;

class HelperTest extends TestCase
{
    private const DOMAIN_SHARDS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

    public function testDeepCheck(): void
    {
        $dispossibleEmail = $this->disposableEmailList();
        for ($i = 0; $i < count($dispossibleEmail); $i++) {
            self::assertTrue(
                Helper::deepCheck($this->splitEmail($dispossibleEmail[$i]))
            );
        }
    }

    public function testDeepCheckSupportsSubdomainsAndNormalization(): void
    {
        self::assertTrue(Helper::deepCheck('sub.mailinator.com'));
        self::assertTrue(Helper::deepCheck('MAILINATOR.COM.'));
    }

    public function testDeepCheckReturnsFalseForInvalidInput(): void
    {
        self::assertFalse(Helper::deepCheck(''));
        self::assertFalse(Helper::deepCheck('invalid-domain'));
    }

    public function testDisposableDomainFileExists(): void
    {
        $path = __DIR__ . '/../resources/domains/';
        foreach (self::DOMAIN_SHARDS as $row) {
            self::assertFileExists($path . $row . '.json');
        }
    }

    public function testDisposableDomainDatasetHasNoDuplicatesAndLooksValid(): void
    {
        $seen = [];
        $path = __DIR__ . '/../resources/domains/';

        foreach (self::DOMAIN_SHARDS as $shard) {
            $raw = file_get_contents($path . $shard . '.json');
            self::assertNotFalse($raw);
            $domains = json_decode($raw, true);

            self::assertIsArray($domains);

            foreach ($domains as $domain) {
                self::assertIsString($domain);
                self::assertSame(strtolower($domain), $domain);
                self::assertMatchesRegularExpression('/^[a-z0-9](?:[a-z0-9-]{0,253}[a-z0-9])?(?:\.[a-z0-9-]{1,63})+$/', $domain);

                $firstCharacter = substr($domain, 0, 1);
                self::assertSame($shard, $firstCharacter);

                self::assertArrayNotHasKey($domain, $seen, 'Duplicate disposable domain found: ' . $domain);
                $seen[$domain] = true;
            }
        }

        self::assertGreaterThan(1000, count($seen));
    }

    public function testDisposableDomainMetadataExistsAndMatchesShardTotals(): void
    {
        $path = __DIR__ . '/../resources/domains/';
        $metadataPath = $path . 'metadata.json';

        self::assertFileExists($metadataPath);

        $metadataRaw = file_get_contents($metadataPath);
        self::assertNotFalse($metadataRaw);
        $metadata = json_decode($metadataRaw, true);
        self::assertIsArray($metadata);
        self::assertArrayHasKey('generated_at_utc', $metadata);
        self::assertArrayHasKey('total_domains', $metadata);
        self::assertArrayHasKey('shards', $metadata);

        self::assertNotFalse(strtotime($metadata['generated_at_utc']));
        self::assertIsInt($metadata['total_domains']);
        self::assertIsArray($metadata['shards']);

        $calculatedTotal = 0;
        foreach (self::DOMAIN_SHARDS as $shard) {
            self::assertArrayHasKey($shard, $metadata['shards']);
            $calculatedTotal += (int) $metadata['shards'][$shard];
        }

        self::assertSame($calculatedTotal, $metadata['total_domains']);
    }

    private function splitEmail(string $email): string
    {
        $domain = strrchr($email, '@');

        return $domain === false ? '' : substr($domain, 1);
    }

    /**
     * @return array<int, string>
     */
    private function disposableEmailList(): array
    {
        return [
            'df@mailinator.com',
            'df@guerrillamail.com',
            'temp@yopmail.com',
            'something@trbvm.com',
            'anything@boximail.com',
        ];
    }

}
