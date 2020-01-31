# Description

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ndberg/laravel-passport-resource-server-middleware.svg?style=flat-square)](https://packagist.org/packages/ndberg/laravel-passport-resource-server-middleware)
[![Build Status](https://img.shields.io/travis/ndberg/laravel-passport-resource-server-middleware/master.svg?style=flat-square)](https://travis-ci.org/ndberg/laravel-passport-resource-server-middleware)
[![Quality Score](https://img.shields.io/scrutinizer/g/ndberg/laravel-passport-resource-server-middleware.svg?style=flat-square)](https://scrutinizer-ci.com/g/ndberg/laravel-passport-resource-server-middleware)
[![Total Downloads](https://img.shields.io/packagist/dt/ndberg/laravel-passport-resource-server-middleware.svg?style=flat-square)](https://packagist.org/packages/ndberg/laravel-passport-resource-server-middleware)

Laravel oAuth Middleware for from Laravel/Passport separated Resource Servers.
It does not make any roundtrip to the laravel/passport server but instead validates the JWT Bearer Token
and takes the user & scopes directly out of the signed token. 

Think about:
- Caching
- Revoked tokens
- CSRF Tokens
- User Migration breaks everything


## Installation

You can install the package via composer:

```bash
composer require ndberg/laravel-passport-resource-server-middleware
```

- publish assets
- migrate db (-> ACHTUNG Users table)
- Change User Model, add id
- Copy public key from laravel/passport

Add the Middleware to the routes:
``` php
Route::middleware('VerifyAccessToken')->get('/auth', function (Request $request) {
    return "No Auth";
});
```

Add 

## Usage

``` php
// Usage description here
```

## Security
As it makes no additional call to the laravel/passport server, it can't check if a token is revoked! As of this you should just use short lifetime access tokens < ~1h.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email a@bergerweb.ch instead of using the issue tracker.

## Credits

- [Andreas Berger](https://github.com/ndberg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
