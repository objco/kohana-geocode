<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Geocoding support using the CloudMade Geocoding API (V3 Beta).
 * http://cloudmade.com/documentation/geocoding
 *
 * @package    Geocode
 * @author     Jonathan Davis <jonathan@obj.co>
 * @copyright  (c) 2013 The Objective Company
 * @license    https://raw.github.com/ObjectiveCompany/kohana-geocode/3.2/master/LICENSE.md
 **/
class Kohana_Geocode_CloudMade extends Geocode
{
	public function address($address)
	{
		$response = $this->query_cloudmade(array('q' => $address));
		
		return $response;
	}
	
	public function ll($lat, $lng)
	{
		$response = $this->query_cloudmade(array('q' => $lat.';'.$lng));
		
		return $response;
	}
	
	public function address_to_ll($address)
	{
		if ($geo_data = self::address($address))
		{
			// Get the top result
			$top_result = $geo_data->places[0];

			return array($top_result->position->lat, $top_result->position->lon);
		}

		return $geo_data;
	}

	/**
	 * Returns a url to the CloudMade geocoding service.
	 *
	 * @param   array   API parameters
	 * @return  string
	 */
	public function api_url($params = array())
	{
		if (empty($params['enc']))
		{
			// Set input encoding to UTF-8
			$params['enc'] = 'UTF-8';
		}
		
		if (empty($params['source']))
		{
			// Set the source to Open Street Map
			$params['source'] = 'OSM';
		}

		$params['format'] = 'json';
		
		$protocol = (isset($this->_config['enable_https']) AND $this->_config['enable_https'] === TRUE) ? 'https://' : 'http://';
		
		return $protocol.'beta.geocoding.cloudmade.com/v3/'.$this->_config['api_key'].'/api/geo.location.search.2?'.http_build_query($params);
	}

	/**
	 * Queries the CloudMade geocoding service and returns the results.
	 *
	 * @param   array     API parameters
	 * @return  stdclass  Geocode response
	 */
	public function query_cloudmade($params = array())
	{
		// Check if caching is enabled
		$cache = ( ! empty($this->_config['cache'])) ? HTTP_Cache::factory($this->_config['cache']) : NULL;

		$request = Request::factory($this->api_url($params), $cache);

		$response = $request->execute();
		$geo_data = json_decode($response->body());

		return $geo_data;
	}
}