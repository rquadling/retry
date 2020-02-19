# RQuadling/retry

[![Build Status](https://img.shields.io/travis/rquadling/retry.svg?style=for-the-badge&logo=travis)](https://travis-ci.org/rquadling/retry)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/rquadling/retry.svg?style=for-the-badge&logo=scrutinizer)](https://scrutinizer-ci.com/g/rquadling/retry/)
[![GitHub issues](https://img.shields.io/github/issues/rquadling/retry.svg?style=for-the-badge&logo=github)](https://github.com/rquadling/retry/issues)

[![PHP Version](https://img.shields.io/packagist/php-v/rquadling/retry.svg?style=for-the-badge)](https://github.com/rquadling/retry)
[![Stable Version](https://img.shields.io/packagist/v/rquadling/retry.svg?style=for-the-badge&label=Latest)](https://packagist.org/packages/rquadling/retry)

[![Total Downloads](https://img.shields.io/packagist/dt/rquadling/retry.svg?style=for-the-badge&label=Total+downloads)](https://packagist.org/packages/rquadling/retry)
[![Monthly Downloads](https://img.shields.io/packagist/dm/rquadling/retry.svg?style=for-the-badge&label=Monthly+downloads)](https://packagist.org/packages/rquadling/retry)
[![Daily Downloads](https://img.shields.io/packagist/dd/rquadling/retry.svg?style=for-the-badge&label=Daily+downloads)](https://packagist.org/packages/rquadling/retry)

A retry library that is completely based upon the [keboola/retry](https://github.com/keboola/retry) library.

The main purpose for this library is that this is PHP 7.0 compatible as that is a use case I have.

The main changes made are:
1. Removal of `void` return typehints.
2. Removal of nullable aspect to typehints.
3. Removal of `public` visibility keyword for class constants.

## Installation

Using Composer:

```sh
composer require rquadling/retry
```
