DONATE
==========

DONATE is a PHP library class to make using the DONATE API easy

## Composer

To install DONATE as a Composer package, simply add this to your composer.json:

```json
"panlogic/donate": "dev-master"
```

..and run `composer update`.  Once it's installed, if you're using Laravel 5, you can register the service provider in `app/config/app.php` in the `providers` array add :

```php
'Panlogic\DONATE\DonateServiceProvider',
```

You can also benefit from using a Facade in Laravel 5 by adding to the alias array also in app.php below the providers array

```php
'Donate'    => 'Panlogic\DONATE\DonateFacade',
```

## Documentation

If you're using Laravel, publish the config file by running

```php
php artisan vendor:publish
```

This will create a panlogic.donate.php file in your Config directory, be sure to fill in the appropriate details provided by Donate.

If you aren't using Laravel then you can create a Donate object by:

```php
use Panlogic\DONATE\Donate;

$config = [
	'live_apikey' 		=> 'your-live-api-key-here',
	'test_apikey' 		=> 'your-test-api-key-here',
	'platform' 			=> 'test',
];

$donate = new Donate($config);
```

For more information about how to use the Donate class, read the [Wiki](https://github.com/panlogic/donate-api-laravel/wiki)

## Copyright and Licence

Donate has been written by Panlogic Ltd and is released under the MIT License.
