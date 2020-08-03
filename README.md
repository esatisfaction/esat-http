# e-satisfaction Http Package #

e-satisfaction Http Package for handling all http request from php.

[![codecov](https://codecov.io/bb/esatisfaction/esat-http/branch/v1.0/graph/badge.svg?token=743W8UUK3E)](https://codecov.io/bb/esatisfaction/esat-http)

### Requirements ###

PHP 7.1.0 and later.

### Setup Repository ###

#### Required Extensions ####

Before installing all the necessary packages, you should enable the `ext-pcov` extension in your machine.

You can find the extension here: [https://pecl.php.net/package/pcov](https://pecl.php.net/package/pcov).

#### Composer #### 

To setup this repository, you should install all composer dependencies:

```bash
composer install
```

### Running tests with Coverage ###

Before running any tests with coverage, you should enable the pcov extension to be used.

Make XDebug drive use PCOV instead by executing the following:

```bash
vendor/bin/pcov clobber
```

### Composer ###

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require esatisfaction/esat-http
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

### Manual Installation ###

If you do not wish to use Composer, you can download the [latest release](https://github.com/esatisfaction/esat-http/releases). Then, to use the bindings, include the `init.php` file.

```php
require_once('/path/to/esatisfaction-http/init.php');
```

### Dependencies ###

This library require the following extensions and packages in order to work properly:

- [`guzzlehttp/guzzle`](https://packagist.org/packages/guzzlehttp/guzzle)
- [`panda/helpers`](https://packagist.org/packages/panda/helpers)

If you use Composer, these dependencies should be handled automatically.
If you install manually, you'll want to make sure that these extensions are available and loaded.
