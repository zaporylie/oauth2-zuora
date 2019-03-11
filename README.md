Zuora OAuth2 Client Provider
====
[![Build Status](https://travis-ci.org/zaporylie/oauth2-zuora.svg?branch=master)](https://travis-ci.org/zaporylie/oauth2-zuora)
[![Coverage Status](https://coveralls.io/repos/github/zaporylie/oauth2-zuora/badge.svg?branch=master)](https://coveralls.io/github/zaporylie/oauth2-zuora?branch=master)

# Installation
```bash
composer require zaporylie/oauth2-zuora:^1.0
```

# Usage
```php
$oauth = new \zaporylie\OAuth2\Client\Provider\Zuora([
    'clientId' => 'id_obtained_from_zuora',
    'clientSecret' => 'secret_obtained_from_zuora',
    // Skip `mode` for production.
    'mode' => 'sandbox',
]);
try {
    $token = $oauth->getAccessToken()->getToken();
}
catch (IdentityProviderException $exception) {
    // Something went wrong on Zuora. Check message.
}
```
