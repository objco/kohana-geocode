<?php defined('SYSPATH') or die('No direct script access.');
return array(
	'default' => array(
		/**
		 * Geocoding service driver to use for processing geocoding requests.
		 */
		'service' => 'google',
		
		/**
		 * Geocoding over a secure connection is recommended for applications that include
		 * sensitive user data, such as a user's location, in geocoding requests.
		 */
		'enable_https' => FALSE,

		/**
		 * Maximum number of attempts to geocode a location with the geocode service.
		 */
		'attempts' => 10,

		/**
		 * How long we should wait before retrying (in microseconds) after a failed request to the geocode service.
		 * Default: 100000 (0.1 seconds)
		 */
		'retry_delay' => 100000,
		
		/**
		 * The cache instance to use for storing geocoding requests. Set to FALSE to disable caching.
		 */
		'cache' => FALSE,
	),
	'cloudmade' => array(
		/**
		 * Geocoding service driver to use for processing geocoding requests.
		 */
		'service' => 'cloudmade',

		/**
		 * The API key for the app. Sign up at http://cloudmade.com.
		 */
		'api_key' => 'APP_API_KEY',

		/**
		 * Geocoding over a secure connection is recommended for applications that include
		 * sensitive user data, such as a user's location, in geocoding requests.
		 */
		'enable_https' => FALSE,

		/**
		 * The cache instance to use for storing geocoding requests. Set to FALSE to disable caching.
		 */
		'cache' => FALSE,
	),
);