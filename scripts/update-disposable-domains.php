<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$domainsDir = $projectRoot . '/resources/domains';
$shards = array_merge(range('0', '9'), range('a', 'z'));

$sources = array(
    'https://raw.githubusercontent.com/disposable/disposable-email-domains/master/domains.txt',
    'https://disposable.github.io/disposable-email-domains/domains.txt',
);

function fetchLinesFromSource(string $url): array
{
    $raw = false;

    if (function_exists('curl_init')) {
        $curlHandle = curl_init($url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_USERAGENT, 'email-checker-domain-updater/1.0');
        $raw = curl_exec($curlHandle);
        $curlHandle = null;
    }

    if ($raw === false) {
        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 20,
                'user_agent' => 'email-checker-domain-updater/1.0',
            ),
        ));

        $raw = @file_get_contents($url, false, $context);
    }

    if ($raw === false) {
        throw new RuntimeException('Unable to fetch source: ' . $url);
    }

    return preg_split('/\r\n|\r|\n/', $raw) ?: array();
}

function normalizeDomain(string $domain): string
{
    $normalized = strtolower(trim($domain));
    $normalized = trim($normalized, " \t\n\r\0\x0B.");

    if ($normalized === '' || str_starts_with($normalized, '#')) {
        return '';
    }

    if (str_contains($normalized, '@')) {
        $parts = explode('@', $normalized);
        $normalized = end($parts) ?: '';
    }

    return $normalized;
}

function isValidDomain(string $domain): bool
{
    return (bool) preg_match('/^(?=.{3,253}$)[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)+$/', $domain);
}

$unique = array();
$sourceStats = array();

foreach ($sources as $sourceUrl) {
    try {
        $lines = fetchLinesFromSource($sourceUrl);
    } catch (RuntimeException $exception) {
        $sourceStats[] = array(
            'url' => $sourceUrl,
            'added' => 0,
            'error' => $exception->getMessage(),
        );
        continue;
    }

    $added = 0;

    foreach ($lines as $line) {
        $domain = normalizeDomain($line);
        if ($domain === '' || !isValidDomain($domain)) {
            continue;
        }

        if (!isset($unique[$domain])) {
            $unique[$domain] = true;
            $added++;
        }
    }

    $sourceStats[] = array(
        'url' => $sourceUrl,
        'added' => $added,
    );
}

if (count($unique) === 0) {
    throw new RuntimeException('Could not update dataset: no domains were fetched from configured sources.');
}

$allDomains = array_keys($unique);
sort($allDomains, SORT_STRING);

$sharded = array();
foreach ($shards as $shard) {
    $sharded[$shard] = array();
}

foreach ($allDomains as $domain) {
    $shard = $domain[0] ?? '';
    if (!isset($sharded[$shard])) {
        continue;
    }

    $sharded[$shard][] = $domain;
}

foreach ($shards as $shard) {
    $path = $domainsDir . '/' . $shard . '.json';
    file_put_contents(
        $path,
        json_encode($sharded[$shard], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
    );
}

$metadata = array(
    'generated_at_utc' => gmdate('Y-m-d\\TH:i:s\\Z'),
    'total_domains' => count($allDomains),
    'sources' => $sourceStats,
    'shards' => array_map(static fn(array $entries): int => count($entries), $sharded),
);

file_put_contents(
    $domainsDir . '/metadata.json',
    json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
);

echo 'Updated disposable domain dataset.' . PHP_EOL;
echo 'Total domains: ' . count($allDomains) . PHP_EOL;
