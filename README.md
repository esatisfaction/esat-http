# e-satisfaction Http Package

[![Build Status](https://travis-ci.org/esatisfaction/esat-http.svg?branch=v1.0)](https://travis-ci.org/esatisfaction/esat-http)
[![Latest Stable Version](https://poser.pugx.org/esatisfaction/http/v/stable)](https://packagist.org/packages/esatisfaction/http)
[![Total Downloads](https://poser.pugx.org/esatisfaction/http/downloads)](https://packagist.org/packages/esatisfaction/http)
[![License](https://poser.pugx.org/esatisfaction/http/license)](https://packagist.org/packages/esatisfaction/http)

e-satisfaction Http Package for handling all http request from php.

## Requirements

PHP 7.1.0 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require esatisfaction/esat-http
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/esatisfaction/esat-http/releases). Then, to use the bindings, include the `init.php` file.

```php
require_once('/path/to/esatisfaction-http/init.php');
```

## Dependencies

This library require the following extensions and packages in order to work properly:

- [`guzzlehttp/guzzle`](https://packagist.org/packages/guzzlehttp/guzzle)
- [`panda/helpers`](https://packagist.org/packages/panda/helpers)

If you use Composer, these dependencies should be handled automatically.
If you install manually, you'll want to make sure that these extensions are available and loaded.
