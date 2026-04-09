# CHANGELOG

## 3.0.0
✋ Hello,

This release is inspired by the **phoenix**. Just like a phoenix rises stronger, this package has been rebuilt for modern PHP and Laravel with better reliability, cleaner compatibility, and healthier maintenance tooling.

🍔 Some Yummy Updates 🍔

- Modernized package requirements to PHP 8.1+ and current Laravel support ranges.
- Updated test stack and configuration for modern PHPUnit versions.
- Added GitHub Actions CI matrix for PHP 8.1-8.4.
- Fixed Dependabot configuration to track Composer and GitHub Actions updates.
- Hardened validation behavior to avoid false positives when SMTP probe/connect fails.
- Changed `checkDomain()` behavior to DNS-record based validation.
- Added corrected response key `disposable` while preserving legacy `dispossable` for backward compatibility.
- Added corrected public property `domain` while preserving legacy `domian` for backward compatibility.
- Expanded deterministic test coverage for compatibility and validation paths.
- Updated README usage examples and deprecation/migration guidance.

😄 Happy Coding 😄

## 2.2.0
- Disposable domain list are added inside repo instead of gist.
- Refactor helper method and optimize code for performance improvement.
- Add testcases environment for laravel 6 for circle ci.
- New testcases added for Helper.
- Add .gitingore file.
- Add option to set from email address.

## 2.1.0
- Update some character list inside helper.
- New domains added to disposable domain list, Now we have 25K domains list.

## 2.0.0
- Helper added to enhance checking of dispossable emails.
- Added deep check option in checkDisposableEmail() method.
- Large dispossable email list has been added to gist and it is directly used with package.

## 1.2.0
- Added license inside composer.json
- Facade updated composer.json

## 1.1.0
- Added License.md
- Added Security.md

## 1.0.0
- Initial release
- Added test cases.
- Update EmailChecker to handle domain validations.
- Added docs.
