# Utánvét Ellenőr API Connector

This package is the PHP connector library for the Utánvét Ellenőr API 1.x.

## Installing

The easiest way to install the Connector is using Composer:

```
composer require webmenedzser/uvb-connector
```

Then use your framework's autoload, or simply add:

```php
<?php
  require 'vendor/autoload.php';
```

## Manual installation

If you wish to omit using Composer altogether, you can download the sources from the repository and use any [PSR-4](http://www.php-fig.org/psr/psr-4/) compatible autoloader.

## Getting started

You can start making requests to the Utánvét Ellenőr API just by creating a new `UVBConnector` instance and calling its `get()` or `post($outcome)` method. 

```php
<?php
  use webmenedzser\UVBConnector\UVBConnector;

  $email = 'tim@apple.com';
  $publicApiKey = 'aaaa';
  $privateApiKey = 'bbbb';

  $connector = new UVBConnector(
    $email, 
    $publicApiKey, 
    $privateApiKey
  );
```

The `UVBConnector` class takes care of the communication between your app and the Utánvét Ellenőr API.

## General usage

### Get e-mail reputation from Utánvét Ellenőr API

```php
<?php 
  use webmenedzser\UVBConnector\UVBConnector;

  $email = 'tim@apple.com';
  $publicApiKey = 'aaaa';
  $privateApiKey = 'bbbb';
  $threshold = 0.5;

  $connector = new UVBConnector(
    $email, 
    $publicApiKey, 
    $privateApiKey
  );
  
  // Set a threshold for the request
  $connector->threshold = $threshold;

  // Get reputation by hash
  $response = $connector->get();
```

The API will answer with a JSON string with a structure like this: 

```json
{
    "status": 200,
    "message": {
        "good": 3,
        "bad": 5,
        "goodRate": 0.375,
        "badRate": 0.625,
        "totalRate": -0.25,
    }
}
```

If you would like to display these values, use the numeric `totalRate` and/or `good` and `bad` values. Avoid aliasing these values with phrases, as they might mislead users.
 
> #### Examples of using the API responses in a UI:
> 
> ##### Good:
> - 3 successful, 2 failed (deliveries), reputation: 0.2
> - 60% successful delivery rate
>
> ##### Bad:
> - Order should not be fulfilled. (Why? No exact explanation is given.)
> - Too much failed deliveries. (How much is "too much"?)
> - Bad customer reputation. (What reputation is considered "bad"?)

### Submit order outcome to Utánvét Ellenőr API
```php
<?php
  use webmenedzser\UVBConnector\UVBConnector;

  $email = 'tim@apple.com';
  $publicApiKey = 'aaaa';
  $privateApiKey = 'bbbb';
  // 1 if good, -1 if bad;
  $outcome = 1;
  $orderId = '#98143';
  $phoneNumber = '+36209238883';
  $countryCode = 'HU';
  $postalCode = '8640';
  $addressLine = 'Szigligeti utca 10.';

  $connector = new UVBConnector(
    $email, 
    $publicApiKey, 
    $privateApiKey
  );

  // Submit order outcome to API
  $response = $connector->post(
    $outcome, 
    $orderId, 
    $phoneNumber, 
    $countryCode, 
    $postalCode, 
    $addressLine
  );
```

Order status changes are the endorsed and ideal events to trigger these API calls.

## Sandbox environment

By setting the 4<sup>th</sup> parameter of UVBConnector constructor to false, the library will use the sandbox environment instead of the production one. **Please use this when you are experimenting with your shop or integration.**

```php
<?php 
  use webmenedzser\UVBConnector\UVBConnector;

  $email = 'tim@apple.com';
  $publicApiKey = 'aaaa';
  $privateApiKey = 'bbbb';
  $production = false;
  $threshold = 0.5;

  $connector = new UVBConnector(
    $email, 
    $publicApiKey, 
    $privateApiKey,
    $production
  );
  
  // Set a threshold for the request
  $connector->threshold = $threshold;

  // Get reputation by hash
  $response = $connector->get();
```

> The sandbox API will behave the same as the production with one exception: the data it provides will be randomized - **don't use it in production!** 
