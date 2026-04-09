# Email Checker

![GitHub license](https://img.shields.io/github/license/aman00323/email-checker) ![GitHub release](https://img.shields.io/github/v/tag/aman00323/email-checker)

Email Checker validates addresses using a practical multi-step flow:

- disposable-domain detection
- MX/DNS checks
- SMTP probe (when possible)

This helps reduce fake signups and temporary/disposable addresses in registration flows.

## Installation

Email Checker requires [PHP](https://php.net) >= 8.1 and supports modern [Laravel](https://laravel.com/) versions.

To get the latest version, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require aman00323/emailchecker
```

Once installed, include `Aman\EmailVerifier\EmailChecker` to access validation methods.

## Usage

### Check Disposable Emails

If you want to check whether an email is [disposable](https://en.wikipedia.org/wiki/Disposable_email_address), use the method below.

Pass `true` as the second argument to enable deep checking against the full disposable-domain dataset shipped in this package.

```php
app(EmailChecker::class)->checkDisposableEmail('something@example.com', true);
```

This email verification will be done on the basis of [disposable emails](https://en.wikipedia.org/wiki/Disposable_email_address) list, This function will check if entered email address is in the list of disposable or not.

### Check DNS and MX Records

For better SMTP responses, set a sender email first:

```php
app(EmailChecker::class)->setFromEmail('something@example.com');
```

Or set an environment variable:
```php
EMAIL_CHECKER_SET_FROM='something@example.com'
```

This method checks [DNS](https://en.wikipedia.org/wiki/Domain_Name_System) and [MX records](https://en.wikipedia.org/wiki/MX_record), then attempts an [SMTP](https://en.wikipedia.org/wiki/Simple_Mail_Transfer_Protocol) handshake using [fsockopen()](https://www.php.net/manual/en/function.fsockopen.php).

```php
app(EmailChecker::class)->checkMxAndDnsRecord('something@example.com');
```
This returns an array like `['valid'|'invalid', 'details...']`.

For better output your server needs to support [fsockopen()](https://www.php.net/manual/en/function.fsockopen.php).

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

## Deprecations and Migration Notes

The package keeps old keys/properties for compatibility, but new integrations should use corrected names.

| Legacy | Use Instead | Status |
| --- | --- | --- |
| `dispossable` (response key) | `disposable` | Deprecated, still returned |
| `$checker->domian` | `$checker->domain` | Deprecated, still synchronized |

Migration recommendation:

- read `disposable` in new code
- keep fallback support for `dispossable` in existing clients until your next major release

## Future Development

Ideas and contributions are welcome.

## Contribution

All contributors are welcome. Please keep code style consistent and ensure tests pass before opening a pull request.

Note: No email verifier can guarantee 100% certainty for every mailbox because many mail servers intentionally limit probe-based verification.


