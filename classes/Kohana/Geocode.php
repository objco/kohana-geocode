<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Support for geocoding and reverse-geocoding.
 *
 * @package    Geocode
 * @author     Jonathan Davis <jonathan@obj.co>
 * @copyright  (c) 2011 The Objective Company
 * @license    https://raw.github.com/ObjectiveCompany/kohana-geocode/3.2/master/LICENSE.md
 **/
abstract class Kohana_Geocode
{
	/**
	 * @var  string  current version
	 */
	const VERSION = '1.0.0';
		
	/**
	 * @var  string  default instance name
	 */
	public static $default = 'default';

	/**
	 * @var  array  Geocode instances, by name
	 */
	public static $instances = array();

	/**
	 * Get a singleton Geocode instance. If configuration is not specified,
	 * it will be loaded from the geocode configuration file using the same
	 * group as the name.
	 *
	 *     // Load the default geocode
	 *     $geocode = Geocode::instance();
	 *
	 *     // Create a custom configured instance
	 *     $geocode = Geocode::instance('custom', $config);
	 *
	 * @param   string   instance name
	 * @param   array    configuration parameters
	 * @return  Geocode
	 */
	public static function instance($name = NULL, array $config = NULL)
	{
		if ($name === NULL)
		{
			// Use the default instance name
			$name = Geocode::$default;
		}

		if ( ! isset(Geocode::$instances[$name]))
		{
			if ($config === NULL)
			{
				// Load the configuration for this geocode
				$config = Kohana::$config->load('geocode')->$name;
			}

			if ( ! isset($config['service']))
			{
				throw new Kohana_Exception('Geocode service not defined in :name configuration',
					array(':name' => $name));
			}

			// Set the driver class name
			$driver = 'Geocode_'.ucfirst($config['service']);

			// Create the geocode instance
			new $driver($name, $config);
		}

		return Geocode::$instances[$name];
	}
	
	// Instance name
	protected $_instance;
	
	// Configuration array
	protected $_config;
	
	/**
	 * Stores the geocode configuration locally and names the instance.
	 *
	 * [!!] This method cannot be accessed directly, you must use [Geocode::instance].
	 *
	 * @return  void
	 */
	protected function __construct($name, array $config)
	{
		// Set the instance name
		$this->_instance = $name;

		// Store the config locally
		$this->_config = $config;

		// Store the gecode instance
		Geocode::$instances[$name] = $this;
	}
	
	/**
	 * Geocodes an address.
	 *
	 * @return array
	 **/
	abstract public function address($address);
	
	/**
	 * Geocodes the location at the specified latitude and longitude.
	 *
	 * @return array
	 **/
	abstract public function ll($lat, $lng);
	
	/**
	 * Retrieves the latitude and longitude of an address.
	 *
	 * @param string $address address
	 * @return array latitude, longitude
	 **/
	abstract public function address_to_ll($address);
	
}