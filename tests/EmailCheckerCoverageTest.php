<?php

declare(strict_types=1);

namespace Aman\EmailVerifier;

final class EmailCheckerFunctionMocks
{
    public static bool $getmxrrReturn = false;

    /** @var array<int, string> */
    public static array $getmxrrHosts = [];

    /** @var array<int, int> */
    public static array $getmxrrWeights = [];

    /** @var array<int, array<string, string>> */
    public static array $dnsRecords = [];

    public static int $lastDnsType = 0;

    public static string $lastDnsHost = '';

    public static bool $fsockopenReturnsFalse = false;

    public static bool $fsockopenThrows = false;

    /** @var array<int, string> */
    public static array $smtpResponses = ['220 greeting', '250 from-ok', '250 rcpt-ok'];

    /** @var array<int, string> */
    public static array $writes = [];

    /** @var array<string, bool> */
    public static array $checkdnsrrMap = [];

    public static function reset(): void
    {
        self::$getmxrrReturn = false;
        self::$getmxrrHosts = [];
        self::$getmxrrWeights = [];
        self::$dnsRecords = [];
        self::$lastDnsType = 0;
        self::$lastDnsHost = '';
        self::$fsockopenReturnsFalse = false;
        self::$fsockopenThrows = false;
        self::$smtpResponses = ['220 greeting', '250 from-ok', '250 rcpt-ok'];
        self::$writes = [];
        self::$checkdnsrrMap = [
            'MX' => false,
            'A' => false,
            'AAAA' => false,
            'CNAME' => false,
        ];
    }
}

/**
 * @param array<int, string> $hosts
 * @param array<int, int> $weights
 */
function getmxrr(string $hostname, array &$hosts, array &$weights): bool
{
    $hosts = EmailCheckerFunctionMocks::$getmxrrHosts;
    $weights = EmailCheckerFunctionMocks::$getmxrrWeights;

    return EmailCheckerFunctionMocks::$getmxrrReturn;
}

/**
 * @return array<int, array<string, string>>
 */
function dns_get_record(string $hostname, int $type): array
{
    EmailCheckerFunctionMocks::$lastDnsHost = $hostname;
    EmailCheckerFunctionMocks::$lastDnsType = $type;

    return EmailCheckerFunctionMocks::$dnsRecords;
}

/**
 * @return resource|false
 */
function fsockopen(string $hostname, int $port, ?int &$errno = null, ?string &$errstr = null, float $timeout = 0)
{
    if (EmailCheckerFunctionMocks::$fsockopenThrows) {
        throw new \RuntimeException('simulated socket failure');
    }

    if (EmailCheckerFunctionMocks::$fsockopenReturnsFalse) {
        $errno = 111;
        $errstr = 'connection refused';

        return false;
    }

    $stream = fopen('php://temp', 'r+');
    if ($stream === false) {
        return false;
    }

    fwrite($stream, implode("\r\n", EmailCheckerFunctionMocks::$smtpResponses) . "\r\n");
    rewind($stream);

    return $stream;
}

/**
 * @param resource $handle
 */
function fgets($handle, ?int $length = null): string|false
{
    if ($length === null) {
        return \fgets($handle);
    }

    if ($length <= 0) {
        return false;
    }

    return \fgets($handle, $length);
}

/**
 * @param resource $handle
 */
function fputs($handle, string $data): int
{
    EmailCheckerFunctionMocks::$writes[] = $data;

    return strlen($data);
}

/**
 * @param resource $handle
 */
function fclose($handle): bool
{
    return \fclose($handle);
}

function checkdnsrr(string $hostname, string $type = 'MX'): bool
{
    return EmailCheckerFunctionMocks::$checkdnsrrMap[$type] ?? false;
}

namespace Aman\EmailVerifier\Tests;

use Aman\EmailVerifier\EmailChecker;
use Aman\EmailVerifier\EmailCheckerFunctionMocks;

class EmailCheckerCoverageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        EmailCheckerFunctionMocks::reset();
        putenv('EMAIL_CHECKER_SET_FROM');
    }

    public function testSetFromEmailFallsBackToEnvironmentAndDefault(): void
    {
        $checker = new EmailChecker();

        putenv('EMAIL_CHECKER_SET_FROM=fallback@example.com');
        $checker->setFromEmail('not-an-email');
        self::assertSame('fallback@example.com', $checker->email_from);

        putenv('EMAIL_CHECKER_SET_FROM=still-invalid');
        $checker->setFromEmail('also-invalid');
        self::assertSame('example@example.com', $checker->email_from);
    }

    public function testInvalidSmtpConfigValuesAreIgnored(): void
    {
        $checker = new EmailChecker();

        $checker->setSmtpPort(0)
            ->setSmtpPort(70000)
            ->setSmtpTimeoutSeconds(0);

        self::assertSame(25, $checker->getSmtpPort());
        self::assertSame(5, $checker->getSmtpTimeoutSeconds());
    }

    public function testCheckEmailRejectsInvalidFormat(): void
    {
        $checker = new EmailChecker();

        $response = $checker->checkEmail('not-an-email');

        self::assertFalse($response['success']);
        self::assertSame('Please enter valid email address', $response['error']);
    }

    public function testCheckDisposableEmailReturnsFalseForInvalidInput(): void
    {
        $checker = new EmailChecker();

        self::assertFalse($checker->checkDisposableEmail('invalid-email', true));
    }

    public function testCheckDisposableEmailDeepCheckPaths(): void
    {
        $checker = new EmailChecker();

        self::assertTrue($checker->checkDisposableEmail('person@mailinator.com', true));
        self::assertFalse($checker->checkDisposableEmail('person@github.com', true));
        self::assertFalse($checker->checkDisposableEmail('person@github.com', false));
    }

    public function testCheckDomainUsesDnsFallbacks(): void
    {
        $checker = new EmailChecker();

        EmailCheckerFunctionMocks::$checkdnsrrMap = [
            'MX' => false,
            'A' => true,
            'AAAA' => false,
            'CNAME' => false,
        ];
        self::assertTrue($checker->checkDomain('user@example.com'));

        EmailCheckerFunctionMocks::$checkdnsrrMap = [
            'MX' => false,
            'A' => false,
            'AAAA' => false,
            'CNAME' => false,
        ];
        self::assertFalse($checker->checkDomain('user@example.com'));
    }

    public function testCheckMxAndDnsRecordReturnsNoSuitableMxWhenNoRecords(): void
    {
        $checker = new EmailChecker();
        EmailCheckerFunctionMocks::$getmxrrReturn = false;
        EmailCheckerFunctionMocks::$dnsRecords = [];

        $result = $checker->checkMxAndDnsRecord('user@example.com');

        self::assertSame(['invalid', 'No suitable MX records found.'], $result);
    }

    public function testCheckMxAndDnsRecordSkipsSmtpWhenDisabled(): void
    {
        $checker = (new EmailChecker())->setSmtpProbeEnabled(false);
        EmailCheckerFunctionMocks::$getmxrrReturn = true;
        EmailCheckerFunctionMocks::$getmxrrHosts = ['mx.example.com'];
        EmailCheckerFunctionMocks::$getmxrrWeights = [10];

        $result = $checker->checkMxAndDnsRecord('user@example.com');

        self::assertSame(['valid', 'MX/DNS records found (SMTP probe skipped).'], $result);
    }

    public function testCheckMxAndDnsRecordReturnsSocketConnectionError(): void
    {
        $checker = new EmailChecker();
        EmailCheckerFunctionMocks::$getmxrrReturn = true;
        EmailCheckerFunctionMocks::$getmxrrHosts = ['mx.example.com'];
        EmailCheckerFunctionMocks::$getmxrrWeights = [10];
        EmailCheckerFunctionMocks::$fsockopenReturnsFalse = true;

        $result = $checker->checkMxAndDnsRecord('user@example.com');

        self::assertSame(['invalid', 'MX record found but could not connect to server'], $result);
    }

    public function testCheckMxAndDnsRecordReturnsHandshakeError(): void
    {
        $checker = new EmailChecker();
        EmailCheckerFunctionMocks::$getmxrrReturn = true;
        EmailCheckerFunctionMocks::$getmxrrHosts = ['mx.example.com'];
        EmailCheckerFunctionMocks::$getmxrrWeights = [10];
        EmailCheckerFunctionMocks::$smtpResponses = ['500 bad greeting'];

        $result = $checker->checkMxAndDnsRecord('user@example.com');

        self::assertSame(['invalid', 'MX record found but SMTP handshake failed'], $result);
        self::assertStringContainsString('QUIT', implode('', EmailCheckerFunctionMocks::$writes));
    }

    public function testCheckMxAndDnsRecordReturnsRecipientError(): void
    {
        $checker = new EmailChecker();
        EmailCheckerFunctionMocks::$getmxrrReturn = true;
        EmailCheckerFunctionMocks::$getmxrrHosts = ['mx.example.com'];
        EmailCheckerFunctionMocks::$getmxrrWeights = [10];
        EmailCheckerFunctionMocks::$smtpResponses = ['220 ready', '250 helo ok', '250 sender ok', '550 invalid rcpt'];

        $result = $checker->checkMxAndDnsRecord('user@example.com');

        self::assertSame(['invalid', 'Invalid email address'], $result);
    }

    public function testCheckMxAndDnsRecordReturnsValidOnSuccessfulSmtpFlow(): void
    {
        $checker = (new EmailChecker())->setSmtpPort(2525)->setSmtpTimeoutSeconds(9);
        EmailCheckerFunctionMocks::$getmxrrReturn = true;
        EmailCheckerFunctionMocks::$getmxrrHosts = ['mx.example.com'];
        EmailCheckerFunctionMocks::$getmxrrWeights = [10];
        EmailCheckerFunctionMocks::$smtpResponses = ['220 ready', '250 helo ok', '250 sender ok', '250 rcpt ok'];

        $result = $checker->checkMxAndDnsRecord('user@example.com');

        self::assertSame(['valid', 'Valid email address'], $result);
        self::assertStringContainsString('HELO', implode('', EmailCheckerFunctionMocks::$writes));
    }

    public function testCheckMxAndDnsRecordHandlesThrownSocketException(): void
    {
        $checker = new EmailChecker();
        EmailCheckerFunctionMocks::$getmxrrReturn = true;
        EmailCheckerFunctionMocks::$getmxrrHosts = ['mx.example.com'];
        EmailCheckerFunctionMocks::$getmxrrWeights = [10];
        EmailCheckerFunctionMocks::$fsockopenThrows = true;

        $result = $checker->checkMxAndDnsRecord('user@example.com');

        self::assertSame(['invalid', 'MX record found but could not connect to server'], $result);
    }

    public function testCheckMxAndDnsRecordUsesDnsIpv6Fallback(): void
    {
        $checker = (new EmailChecker())->setSmtpProbeEnabled(false);
        EmailCheckerFunctionMocks::$getmxrrReturn = false;
        EmailCheckerFunctionMocks::$dnsRecords = [['ipv6' => '2001:db8::1']];

        $result = $checker->checkMxAndDnsRecord('user@example.com');

        self::assertSame(['valid', 'MX/DNS records found (SMTP probe skipped).'], $result);
    }

    public function testCheckMxAndDnsRecordParsesBracketedIpv6Literal(): void
    {
        $checker = (new EmailChecker())->setSmtpProbeEnabled(false);
        EmailCheckerFunctionMocks::$dnsRecords = [['ipv6' => '2001:db8::8']];

        $result = $checker->checkMxAndDnsRecord('user@[IPv6:2001:db8::8]');

        self::assertSame(['valid', 'MX/DNS records found (SMTP probe skipped).'], $result);
        self::assertSame('2001:db8::8', EmailCheckerFunctionMocks::$lastDnsHost);
        self::assertSame(DNS_AAAA, EmailCheckerFunctionMocks::$lastDnsType);
    }

    public function testCheckMxAndDnsRecordUsesDnsAForIpv4Literal(): void
    {
        $checker = (new EmailChecker())->setSmtpProbeEnabled(false);
        EmailCheckerFunctionMocks::$dnsRecords = [['ip' => '203.0.113.77']];

        $result = $checker->checkMxAndDnsRecord('user@[203.0.113.77]');

        self::assertSame(['valid', 'MX/DNS records found (SMTP probe skipped).'], $result);
        self::assertSame('203.0.113.77', EmailCheckerFunctionMocks::$lastDnsHost);
        self::assertSame(DNS_A, EmailCheckerFunctionMocks::$lastDnsType);
    }

    public function testCheckMxAndDnsRecordUsesDnsAAndAaaaForHostnames(): void
    {
        $checker = (new EmailChecker())->setSmtpProbeEnabled(false);
        EmailCheckerFunctionMocks::$dnsRecords = [['ip' => '198.51.100.5']];

        $result = $checker->checkMxAndDnsRecord('user@example.net');

        self::assertSame(['valid', 'MX/DNS records found (SMTP probe skipped).'], $result);
        self::assertSame('example.net', EmailCheckerFunctionMocks::$lastDnsHost);
        self::assertSame(DNS_A + DNS_AAAA, EmailCheckerFunctionMocks::$lastDnsType);
    }
}
