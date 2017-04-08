<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

	public function boot()
	{
		/*
				TO DO: valid region data
		*/
		// 1 - arr length = 4
		// 2 - for each item in arr
		// 				must have 2 elements named 0 -> lat &&  1 - long

		\Validator::extendImplicit('isValidRegionJson', function($attribute, $value, $parameters, $validator) {
			$data = (object)$validator->getData();
			if (!array_key_exists('coordinates', $validator->getData())) {
				return false;
			}
    	$validJson = function ($string) {
				if (empty(json_decode($string, true))) {
					return false;
				} 
				return (is_string($string) && is_array(json_decode($string, true)));
			};
    	return $validJson($data->coordinates);
		});


		\Validator::extendImplicit('isValidMeasureWindJson', function($attribute, $value, $parameters, $validator) {
			$data = (object)$validator->getData();
			if (!array_key_exists('wind_info', $validator->getData())) {
				return false;
			}
    	$validJson = function ($string) {
				if (empty(json_decode($string, true))) {
					return false;
				} 
				return (is_string($string) && is_array(json_decode($string, true)));
			};
    	return $validJson($data->wind_info);
		});

		\Validator::extendImplicit('isValidMeasureSensJson', function($attribute, $value, $parameters, $validator) {
			$data = (object)$validator->getData();
				if (!array_key_exists('sensors_info', $validator->getData())) {
				return false;
			}
    	$validJson = function ($string) {
				if (empty(json_decode($string, true))) {
					return false;
				} 
				return (is_string($string) && is_array(json_decode($string, true)));
			};
    	return $validJson($data->sensors_info);
		});
	}

    /**
     * Register any application services.
     *
     * @return void
     */
	
	public function register()
	{
		if ($this->app->environment() == 'local') {
		    $this->app->register('Wn\Generators\CommandsServiceProvider');
		}


		$this->app->singleton('mailer', function ($app) {
        $app->configure('services');
        return $app->loadComponent('mail', 'Illuminate\Mail\MailServiceProvider', 'mailer');
    });
}


}
