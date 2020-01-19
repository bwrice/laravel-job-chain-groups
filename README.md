
#### This package is still in development and not recommended for production use!

# Dispatch a chain of jobs after a group of asynchronous jobs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bwrice/laravel-job-chain-groups.svg?style=flat-square)](https://packagist.org/packages/bwrice/laravel-job-chain-groups)
[![Build Status](https://img.shields.io/travis/bwrice/laravel-job-chain-groups/master.svg?style=flat-square)](https://travis-ci.org/bwrice/laravel-job-chain-groups)
[![Quality Score](https://img.shields.io/scrutinizer/g/bwrice/laravel-job-chain-groups.svg?style=flat-square)](https://scrutinizer-ci.com/g/bwrice/laravel-job-chain-groups)
[![Total Downloads](https://img.shields.io/packagist/dt/bwrice/laravel-job-chain-groups.svg?style=flat-square)](https://packagist.org/packages/bwrice/laravel-job-chain-groups)

Imagine you have monthly subscriptions and every month you need to process those subscriptions and immediately after processing them,
you want to generate a subscriptions report. You create a ProcessSubscription job and a GenerateSubscriptionReport job, but the only way to guarantee
the GenerateSubscriptionReport job runs after all the ProcessSubscription jobs are finished is to chain all of the ProcessSubscription jobs
concurrently and then chain the GenerateSubscriptionReport job on the end.

## Installation

You can install the package via composer:

```bash
composer require bwrice/laravel-job-chain-groups
```

## Usage

``` php
// Usage description here
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email bwrice83@gmail.com instead of using the issue tracker.

## Credits

- [Brian Rice](https://github.com/bwrice)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
