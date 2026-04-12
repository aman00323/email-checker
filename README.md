# Email Checker

![GitHub license](https://img.shields.io/github/license/aman00323/email-checker)
![GitHub release](https://img.shields.io/github/v/tag/aman00323/email-checker)
![Packagist Downloads](https://img.shields.io/packagist/dt/aman00323/emailchecker)
![PHP Version](https://img.shields.io/packagist/php-v/aman00323/emailchecker)

Email Checker helps you reduce fake signups and disposable email usage by validating addresses with a practical multi-step flow.

## Why Use It

- disposable-domain detection
- MX/DNS checks
- SMTP probe (when possible)

Use it in registration and lead-capture flows where cleaner email data matters.

## Quick Start

Install:

```bash
composer require aman00323/emailchecker
```

Basic check:

```php
use Aman\EmailVerifier\EmailChecker;

$result = app(EmailChecker::class)->checkEmail('user@example.com', true);

if ($result['success']) {
	// Use $result['disposable'], $result['mxrecord'], $result['domain']
} else {
	// Use $result['error']
}
```

## Compatibility

| Package Version | PHP | Laravel |
| --- | --- | --- |
| 3.x | >= 8.1 | 10.x, 11.x, 12.x |

## Installation

Email Checker requires [PHP](https://php.net) >= 8.1 and supports modern [Laravel](https://laravel.com/) versions.

To get the latest version, require the project using [Composer](https://getcomposer.org):

```bash
composer require aman00323/emailchecker
```

Once installed, include `Aman\EmailVerifier\EmailChecker` to access validation methods.

Optional: publish package config to tune SMTP behavior globally:

```bash
php artisan vendor:publish --tag=emailchecker-config
```

This creates `config/emailchecker.php` with defaults for probe toggle, port, timeout, and sender email.

## Configuration

Published config (`config/emailchecker.php`) example:

```php
<?php

return [
    'smtp_probe' => env('EMAIL_CHECKER_SMTP_PROBE', true),
    'smtp_port' => (int) env('EMAIL_CHECKER_SMTP_PORT', 25),
    'smtp_timeout_seconds' => (int) env('EMAIL_CHECKER_SMTP_TIMEOUT_SECONDS', 5),
    'from_email' => env('EMAIL_CHECKER_SET_FROM', 'example@example.com'),
];
```

Use global config when:

- you want one default behavior across the whole Laravel app
- settings should come from environment variables per deployment

Use per-instance setters when:

- you need request-specific behavior
- you want to override defaults for one check flow only

```php
$checker = app(EmailChecker::class);

$checker
    ->setSmtpProbeEnabled(false)
    ->setSmtpPort(2525)
    ->setSmtpTimeoutSeconds(10)
    ->setFromEmail('ops@example.com');
```

## Usage

### Check Disposable Emails

If you want to check whether an email is [disposable](https://en.wikipedia.org/wiki/Disposable_email_address), use the method below.

Pass `true` as the second argument to enable deep checking against the full disposable-domain dataset shipped in this package.

```php
app(EmailChecker::class)->checkDisposableEmail('something@example.com', true);
```

This checks the address domain against the disposable-domain dataset included in this package.

### Check DNS and MX Records

For better SMTP responses, set a sender email first:

```php
app(EmailChecker::class)->setFromEmail('something@example.com');
```

Or set an environment variable:
```php
EMAIL_CHECKER_SET_FROM='something@example.com'
```

You can also configure probe behavior globally via environment variables:

```php
EMAIL_CHECKER_SMTP_PROBE=true
EMAIL_CHECKER_SMTP_PORT=25
EMAIL_CHECKER_SMTP_TIMEOUT_SECONDS=5
```

This method checks [DNS](https://en.wikipedia.org/wiki/Domain_Name_System) and [MX records](https://en.wikipedia.org/wiki/MX_record), then attempts an [SMTP](https://en.wikipedia.org/wiki/Simple_Mail_Transfer_Protocol) handshake using [fsockopen()](https://www.php.net/manual/en/function.fsockopen.php).

```php
app(EmailChecker::class)->checkMxAndDnsRecord('something@example.com');
```

This returns an array like `['valid'|'invalid', 'details...']`.

For better output, your server should support [fsockopen()](https://www.php.net/manual/en/function.fsockopen.php).

### Check Domain Status

This method validates domain existence through DNS record checks (`MX`, `A`, `AAAA`, `CNAME`).

```php
app(EmailChecker::class)->checkDomain('something@example.com');
```

This method returns `true` when at least one supported DNS record exists; otherwise `false`.

### Check Email

This method runs disposable, MX/DNS+SMTP, and domain checks and returns a structured response.

```php
app(EmailChecker::class)->checkEmail('something@example.com', true);
```

All methods can be used independently, or together via `checkEmail()`.

Example success response:

```php
[
    'success' => true,
    'disposable' => ['success' => true, 'detail' => 'Email address is not disposable'],
    // Legacy key, kept for backward compatibility:
    'dispossable' => ['success' => true, 'detail' => 'Email address is not disposable'],
    'mxrecord' => ['success' => true, 'detail' => 'Valid email address'],
    'domain' => ['success' => true, 'detail' => 'Domain exists.'],
]
```

## Limits and Expectations

No email verification library can guarantee 100% certainty for every mailbox.

- Some mail servers block SMTP probes
- Some domains are valid but intentionally restrictive
- Disposable-domain providers change frequently

Use this package as a high-signal filter, not a single source of truth.

## Deprecations and Migration Notes

The package keeps old keys/properties for compatibility, but new integrations should use corrected names.

| Legacy | Use Instead | Status |
| --- | --- | --- |
| `dispossable` (response key) | `disposable` | Deprecated, still returned |
| `$checker->domian` | `$checker->domain` | Deprecated, still synchronized |

Migration recommendation:

- read `disposable` in new code
- keep fallback support for `dispossable` in existing clients until your next major release

## Contribution

All contributors are welcome. Please keep code style consistent and ensure tests pass before opening a pull request.

## Keeping Disposable Domains Updated

Refresh the domain dataset locally:

```bash
composer update-disposable-domains
```

This command fetches trusted source lists, normalizes and deduplicates domains, rewrites shard files in `resources/domains`, and updates `resources/domains/metadata.json`.

The repository also includes a daily scheduled GitHub Actions workflow to automate refreshes and open pull requests when updates are available.

## Sponsorship and Commercial Support

If this package saves your team time or prevents signup abuse in production, please consider supporting maintenance.

- Sponsor ongoing open-source maintenance
- Fund roadmap items and faster issue triage
- Request paid integration support for your project

For sponsorship or paid support, contact: `aman.gopher@gmail.com`

## Credits

Thanks to all contributors and users who helped this package reach broad production usage.


