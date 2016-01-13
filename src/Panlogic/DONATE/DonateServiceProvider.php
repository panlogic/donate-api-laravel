<?php namespace Panlogic\DONATE;
/**
* DONATE helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package DONATE
* @version 1.0.0
* @author Panlogic Ltd
* @license MIT
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use Illuminate\Support\ServiceProvider;
use Panlogic\DONATE\Exceptions\DonateException;

class DonateServiceProvider extends ServiceProvider {

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../config/donate.php' => config_path('panlogic.donate.php'),
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('donate', function ($app)
		{
			if (is_null(config('panlogic.donate.live_apikey')))
			{
				throw new DonateException;
			}

			return new Donate($app['config']['panlogic.donate']);
		});

		$this->app->alias('donate', 'Panlogic\DONATE\Donate');
	}

	 /**
     * Get the services provided by the provider.
     *
	 * @return array
	 */
	public function provides()
	{
		return ['donate','Panlogic\DONATE\Donate'];
	}
}