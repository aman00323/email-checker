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

    public function testDeepCheckHandlesEmailLikeInputAndNonAlnumCandidates(): void
    {
        // Covers normalizeDomain() branch that strips text before the last '@'.
        self::assertTrue(Helper::deepCheck('User@MAILINATOR.COM'));

        // Covers deepCheck() loop continue branch for candidates starting with non [a-z0-9].
        self::assertTrue(Helper::deepCheck('.mailinator.com'));
    }

    public function testLoadShardAsSetReturnsEmptyArrayWhenFileIsMissing(): void
    {
        $this->resetShardCache();

        $result = $this->invokeLoadShardAsSet('!');

        self::assertSame([], $result);
    }

    public function testLoadShardAsSetReturnsEmptyArrayWhenJsonIsInvalidType(): void
    {
        $this->resetShardCache();

        $path = __DIR__ . '/../resources/domains/_.json';
        file_put_contents($path, '"not-an-array"');

        try {
            $result = $this->invokeLoadShardAsSet('_');
            self::assertSame([], $result);
        } finally {
            @unlink($path);
            $this->resetShardCache();
        }
    }

    public function testLoadShardAsSetReturnsEmptyArrayWhenFileCannotBeRead(): void
    {
        $this->resetShardCache();

        $path = __DIR__ . '/../resources/domains/~.json';
        file_put_contents($path, "mailinator.com\n");
        @chmod($path, 0000);

        try {
            set_error_handler(static function (): bool {
                return true;
            });

            $result = $this->invokeLoadShardAsSet('~');
            self::assertSame([], $result);
        } finally {
            restore_error_handler();
            @chmod($path, 0644);
            @unlink($path);
            $this->resetShardCache();
        }
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
        $invalidDomains = [];
        $invalidShardAssignments = [];
        $duplicateDomains = [];

        foreach (self::DOMAIN_SHARDS as $shard) {
            $raw = file_get_contents($path . $shard . '.json');
            self::assertNotFalse($raw);
            $domains = json_decode($raw, true);

            self::assertIsArray($domains);

            foreach ($domains as $domain) {
                if (!is_string($domain)) {
                    $invalidDomains[] = '[non-string domain in shard ' . $shard . ']';
                    continue;
                }

                if (
                    $domain !== strtolower($domain) ||
                    !preg_match('/^[a-z0-9](?:[a-z0-9-]{0,253}[a-z0-9])?(?:\.[a-z0-9-]{1,63})+$/', $domain)
                ) {
                    $invalidDomains[] = $domain;
                }

                $firstCharacter = substr($domain, 0, 1);
                if ($firstCharacter !== $shard) {
                    $invalidShardAssignments[] = $domain . ' => ' . $shard;
                }

                if (isset($seen[$domain])) {
                    $duplicateDomains[] = $domain;
                }

                $seen[$domain] = true;
            }
        }

        self::assertSame([], $invalidDomains, 'Invalid domains found: ' . implode(', ', array_slice($invalidDomains, 0, 10)));
        self::assertSame([], $invalidShardAssignments, 'Wrong shard assignment found: ' . implode(', ', array_slice($invalidShardAssignments, 0, 10)));
        self::assertSame([], $duplicateDomains, 'Duplicate disposable domains found: ' . implode(', ', array_slice($duplicateDomains, 0, 10)));

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

    /**
     * @return array<string, bool>
     */
    private function invokeLoadShardAsSet(string $shardKey): array
    {
        $method = new \ReflectionMethod(Helper::class, 'loadShardAsSet');
        $method->setAccessible(true);

        /** @var array<string, bool> $result */
        $result = $method->invoke(null, $shardKey);

        return $result;
    }

    private function resetShardCache(): void
    {
        $property = new \ReflectionProperty(Helper::class, 'shardCache');
        $property->setAccessible(true);
        $property->setValue([]);
    }

}
