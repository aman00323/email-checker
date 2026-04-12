<?php

declare(strict_types=1);

namespace Aman\EmailVerifier;

use Aman\EmailVerifier\Helpers\Helper;

class EmailChecker
{
    public const RESPONSE_KEY_DISPOSABLE = 'disposable';

    public const RESPONSE_KEY_DISPOSSABLE_LEGACY = 'dispossable';

    private const STATUS_VALID = 'valid';

    private const STATUS_INVALID = 'invalid';

    private const DEFAULT_SMTP_PORT = 25;

    private const DEFAULT_SMTP_TIMEOUT_SECONDS = 5;

    private const DEFAULT_EMAIL_FROM = 'example@example.com';

    private const ERROR_INVALID_EMAIL = 'Please enter valid email address';

    private const ERROR_DISPOSABLE_EMAIL = 'Entered email address is disposable';

    private const ERROR_MX_DNS = 'Entered email address has no MX and DNS record.';

    private const ERROR_DOMAIN = 'Unable to verify email domain.';

    /**
     * @deprecated Kept for backward compatibility. Use $domain instead.
     */
    public string $domian = '';

    public string $domain = '';

    public string $details = '';

    public string $result = '';

    public string $email_from = '';

    private bool $smtpProbeEnabled = true;

    private int $smtpPort = self::DEFAULT_SMTP_PORT;

    private int $smtpTimeoutSeconds = self::DEFAULT_SMTP_TIMEOUT_SECONDS;

    /*
    ==============================================================

    This method will set from email address

    ==============================================================

    @return String
     */
    public function setFromEmail(string $email_from): void
    {
        if (filter_var($email_from, FILTER_VALIDATE_EMAIL)) {
            $this->email_from = $email_from;
        } elseif (filter_var(getenv('EMAIL_CHECKER_SET_FROM'), FILTER_VALIDATE_EMAIL)) {
            $this->email_from = (string) getenv('EMAIL_CHECKER_SET_FROM');
        } else {
            $this->email_from = self::DEFAULT_EMAIL_FROM;
        }
    }

    public function setSmtpProbeEnabled(bool $enabled): self
    {
        $this->smtpProbeEnabled = $enabled;

        return $this;
    }

    public function isSmtpProbeEnabled(): bool
    {
        return $this->smtpProbeEnabled;
    }

    public function setSmtpPort(int $port): self
    {
        if ($port > 0 && $port <= 65535) {
            $this->smtpPort = $port;
        }

        return $this;
    }

    public function getSmtpPort(): int
    {
        return $this->smtpPort;
    }

    public function setSmtpTimeoutSeconds(int $timeoutSeconds): self
    {
        if ($timeoutSeconds > 0) {
            $this->smtpTimeoutSeconds = $timeoutSeconds;
        }

        return $this;
    }

    public function getSmtpTimeoutSeconds(): int
    {
        return $this->smtpTimeoutSeconds;
    }

    /*
    ==============================================================

    This method will check all possibilities of email verification

    ==============================================================

    @return array
     */
    /**
     * @return array<string, mixed>
     */
    public function checkEmail(string $email, bool $deepCheck = false): array
    {
        $disposable = $mxrecord = $domain = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->failureResponse(self::ERROR_INVALID_EMAIL);
        }

        if ($this->checkDisposableEmail($email, $deepCheck)) {
            return $this->failureResponse(self::ERROR_DISPOSABLE_EMAIL);
        }

        $disposable = [
            'success' => true,
            'detail' => 'Email address is not disposable',
        ];

        $verify = $this->checkMxAndDnsRecord($email);
        if ($verify[0] !== self::STATUS_VALID) {
            return $this->failureResponse(self::ERROR_MX_DNS);
        }

        $mxrecord = [
            'success' => true,
            'detail' => $verify[1],
        ];

        if (!$this->checkDomain($email)) {
            return $this->failureResponse(self::ERROR_DOMAIN);
        }

        $domain = [
            'success' => true,
            'detail' => 'Domain exists.',
        ];

        return [
            'success' => true,
        ] + $this->buildSuccessPayload($disposable, $mxrecord, $domain);
    }

    /*
    =====================================================

    This method will only check for disposable emails

    =====================================================

    @return true | false
     */
    public function checkDisposableEmail(string $email, bool $deepCheck = false): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $domain = $this->splitEmail($email);
            if (preg_match('/(ThrowAwayMail|DeadAddress|10MinuteMail|20MinuteMail|AirMail|Dispostable|Email Sensei|EmailThe|FilzMail|Guerrillamail|IncognitoEmail|Koszmail|Mailcatch|Mailinator|Mailnesia|MintEmail|MyTrashMail|NoClickEmail|
            SpamSpot|Spamavert|Spamfree24|TempEmail|Thrashmail.ws|Yopmail|EasyTrashMail|Jetable|MailExpire|MeltMail|Spambox|empomail|33Mail|
            E4ward|GishPuppy|InboxAlias|MailNull|Spamex|Spamgourmet|BloodyVikings|SpamControl|MailCatch|Tempomail|EmailSensei|Yopmail|
            Trasmail|Guerrillamail|Yopmail|boximail|ghacks|Maildrop|MintEmail|fixmail|gelitik.in|ag.us.to|mobi.web.id
            |fansworldwide.de|privymail.de|gishpuppy|spamevader|uroid|tempmail|soodo|deadaddress|trbvm)/i', $domain)) { // Possiblities of domain name that can genrate dispossable emails COURTESY FORMGET
                return true;
            }

            if ($deepCheck) {
                return Helper::deepCheck($domain);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
    =====================================================

    This method will check for DNS and MX record of the
    email address domain.

    =====================================================

    @return array with details
     */
    /**
     * @return array{0: string, 1: string}
     */
    public function checkMxAndDnsRecord(string $email): array
    {
        if (empty($this->email_from)) {
            $this->setFromEmail('');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [self::STATUS_INVALID, 'Validation error email address.'];
        }

        $email_arr = explode('@', $email);
        $domainParts = array_slice($email_arr, -1);
        $domain = $domainParts[0] ?? '';

        $domain = ltrim($domain, '[');
        $domain = rtrim($domain, ']');
        if ('IPv6:' === substr($domain, 0, strlen('IPv6:'))) {
            $domain = substr($domain, strlen('IPv6') + 1);
        }

        $mxhosts = [];
        $mxweight = [];
        $mxIp = '';

        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            $mxIp = $domain;
        } else {
            getmxrr($domain, $mxhosts, $mxweight);
        }

        if (!empty($mxhosts)) {
            $lowestWeightIndex = array_search(min($mxweight), $mxweight, true);
            $mxIp = (string) $mxhosts[(int) $lowestWeightIndex];
        } else {
            $recordA = [];
            if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $recordA = dns_get_record($domain, DNS_A);
            } elseif (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $recordA = dns_get_record($domain, DNS_AAAA);
            } else {
                $recordA = dns_get_record($domain, DNS_A + DNS_AAAA);
            }

            if (!empty($recordA)) {
                $mxIp = isset($recordA[0]['ip']) ? (string) $recordA[0]['ip'] : (string) $recordA[0]['ipv6'];
            } else {
                return [self::STATUS_INVALID, 'No suitable MX records found.'];
            }
        }

        if (!$this->smtpProbeEnabled) {
            return [self::STATUS_VALID, 'MX/DNS records found (SMTP probe skipped).'];
        }

        try {
            $connect = @fsockopen($mxIp, $this->smtpPort, $errno, $errstr, $this->smtpTimeoutSeconds);
            if ($connect === false) {
                return [self::STATUS_INVALID, 'MX record found but could not connect to server'];
            }

            $greeting = fgets($connect, 1024);
            if (!is_string($greeting) || !preg_match('/^220/i', $greeting)) {
                fputs($connect, 'QUIT');
                fclose($connect);

                return [self::STATUS_INVALID, 'MX record found but SMTP handshake failed'];
            }

            fputs($connect, "HELO $mxIp\r\n");
            fgets($connect, 1024);

            fputs($connect, 'MAIL FROM: <' . $this->email_from . ">\r\n");
            $from = fgets($connect, 1024);

            fputs($connect, "RCPT TO: <$email>\r\n");
            $to = fgets($connect, 1024);

            fputs($connect, 'QUIT');
            fclose($connect);

            if (!is_string($from) || !is_string($to) || !preg_match('/^250/i', $from) || !preg_match('/^250/i', $to)) {
                return [self::STATUS_INVALID, 'Invalid email address'];
            }

            return [self::STATUS_VALID, 'Valid email address'];
        } catch (\Throwable $e) {
            return [self::STATUS_INVALID, 'MX record found but could not connect to server'];
        }
    }

    /*
    =====================================================

    This method will check if domain exist or not using
    curl.

    =====================================================

    @return true | false
     */

    public function checkDomain(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = $this->splitEmail($email);

        return checkdnsrr($domain, 'MX') ||
            checkdnsrr($domain, 'A') ||
            checkdnsrr($domain, 'AAAA') ||
            checkdnsrr($domain, 'CNAME');
    }

    /*
    =====================================================

    This method will split domain from email address

    =====================================================

    @return domain
     */
    private function splitEmail(string $email): string
    {
        $domain = (string) substr((string) strrchr($email, '@'), 1);
        $this->domain = $domain;
        $this->domian = $domain;

        return $domain;
    }

    /**
     * @param array<string, mixed> $disposable
     * @param array<string, mixed> $mxrecord
     * @param array<string, mixed> $domain
     * @return array<string, mixed>
     */
    private function buildSuccessPayload(array $disposable, array $mxrecord, array $domain): array
    {
        return [
            self::RESPONSE_KEY_DISPOSABLE => $disposable,
            self::RESPONSE_KEY_DISPOSSABLE_LEGACY => $disposable,
            'mxrecord' => $mxrecord,
            'domain' => $domain,
        ];
    }

    /**
     * @return array{success: false, error: string}
     */
    private function failureResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => $message,
        ];
    }

}
