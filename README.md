# Email Checker

![CircleCI](https://img.shields.io/circleci/build/github/aman00323/email-checker) [![MadeWithLaravel.com shield](https://madewithlaravel.com/storage/repo-shields/1689-shield.svg)](https://madewithlaravel.com/p/email-checker/shield-link) [![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-green.svg)](https://GitHub.com/aman00323/StrapDown.js/graphs/commit-activity) [![GitHub license](https://img.shields.io/github/license/aman00323/email-checker)](https://github.com/aman00323/email-checker/blob/master/LICENSE) [![GitHub release](https://img.shields.io/github/v/tag/aman00323/email-checker)](https://github.com/aman00323/email-checker/releases)


Email Checker was created and maintained by [Aman Nurani](https://github.com/aman00323). It provides a powerful email validating system for both development and production for Laravel. It uses [fsockopen()](https://www.php.net/manual/en/function.fsockopen.php), [cURL](https://www.php.net/manual/en/book.curl.php) and many more to validate email address exists or not in real world.

Nowadays most of websites are using registration process where they need to verify user's ownership. Mostly developers verify email by sending email verification link to the email, So this will store extra email in database (if they were not exists in real). Additionally some people use [disposable emails](https://en.wikipedia.org/wiki/Disposable_email_address) for temporary usage.

# :tada: WE HAVE MORE THAN 25K DIPOSABLE DOMAIN LIST :tada:
<center> THIS PACKAGE WILL HELP YOU TO VERIFY EMAIL </center>

## Installation

Email Checker requires [PHP](https://php.net) > 7.0. This particular version supports with latest [Laravel](https://laravel.com/).

To get the latest version, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require aman00323/emailchecker
```

Once installed, You need to include `Aman\EmailVerifier\EmailChecker` to access methods for email verify.

## Usage

### Check Disposable Emails

If you want to check email is [disposable emails](https://en.wikipedia.org/wiki/Disposable_email_address) or not then you can use the following function of [emailchecker](https://github.com/aman00323/email-verifier/)

***Added new option to check disposable emails***

This option is part of checkDisposableEmail() method, you need to pass second argument as true.

When you pass true inside helper will check emails with list of dispossable. which are hosted on gist, So whenever list will be changed you would't have to update package.

```php
app(EmailChecker::class)->checkDisposableEmail('something@example.com','boolean'));
```

This email verification will be done on the basis of [disposable emails](https://en.wikipedia.org/wiki/Disposable_email_address) list, This function will check if entered email address is in the list of disposable or not.

### Check DNS And MX Records

*For better output you need to set from email address for this method*

```php
app(EmailChecker::class)->setFromEmail('something@example.com','boolean'));
```

OR
Set ENV variable in your .env
```php
EMAIL_CHECKER_SET_FROM='something@example.com'
```

Another usage is to check [DNS](https://en.wikipedia.org/wiki/Domain_Name_System) and [MX Record](https://en.wikipedia.org/wiki/MX_record) of the email address, In this method package will try to extract records from email address and try to verify using [SMTP](https://en.wikipedia.org/wiki/Simple_Mail_Transfer_Protocol).

If this method will successfully extract records, then it will try to send HELLO message on the email address using [fsockopen()](https://www.php.net/manual/en/function.fsockopen.php), if it get valid status from MAIL server then it will return true. Also function will return true if it is not verify with the detail message.

```php
app(EmailChecker::class)->checkMxAndDnsRecord('something@example.com'));
```
This will return array with success and details, Details will indicate email verified with any exception or not.

For better output your server needs to support [fsockopen()](https://www.php.net/manual/en/function.fsockopen.php).

### Check Domain Status

Sometime it is hard to identify that email exist or not based on DNS and MX Records, So this method will check the domain status using [cURL](https://www.php.net/manual/en/book.curl.php).

This method ensures that email which is given has valid domain.

```php
app(EmailChecker::class)->checkDomain('something@example.com'));
```

This method will return TRUE or FALSE, if it successfully get response then it will return TRUE. Response validates based on [HTTP Status Code](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes).

### Check Email

This method will use all of the methods and it gives detail response, if it gives TRUE.

If any of the method will respond with FALSE then will not give detail report.

```php
app(EmailChecker::class)->checkEmail('something@example.com','boolean'));
```

As we have added new option with checkDisposableEmail() which has second argument that will enable deep check to compare domain with large list.

Don't worry it would't take too much time. :smile:

All are different method you can use individually as per your requirement. To call all of the method at once use **Check Email**

## Future Developement

Please let add your ideas to improve this package.

## Contribution

All contributer are welcome, Code must follow [PSR2](https://www.php-fig.org/psr/psr-2/). create feature branch to compare with email checker. Your code must pass testcases.

**NOTE** : This package will not ensure to verify each and email address, some of them cannot be verify due to MAIL server securities.


