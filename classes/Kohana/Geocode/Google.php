<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Geocoding support using the Google Geocoding API (V3).
 * http://code.google.com/apis/maps/documentation/geocoding/
 *
 * @package    Geocode
 * @author     Jonathan Davis <jonathan@obj.co>
 * @copyright  (c) 2011 The Objective Company
 * @license    https://raw.github.com/ObjectiveCompany/kohana-geocode/3.2/master/LICENSE.md
 **/
class Kohana_Geocode_Google extends Geocode
{
	// Geocoding Responses
	const OK				= 'OK';
	const ZERO_RESULTS		= 'ZERO_RESULTS';       // Geocode was successful but returned no results.
	const OVER_QUERY_LIMIT	= 'OVER_QUERY_LIMIT';   // Over the quota.
	const REQUEST_DENIED	= 'REQUEST_DENIED';     // Request was denied, generally because of lack of a sensor parameter.
	const INVALID_REQUEST	= 'INVALID_REQUEST';    // Generally indicates that the query (address or latlng) is missing.
	
	public function address($address)
	{
		$response = $this->query_google(array('address' => $address));
		
		if (isset($response->results))
		{
			return $response->results;
		}
		
		return $response;
	}
	
	public function ll($lat, $lng)
	{
		$response = $this->query_google(array('latlng' => $lat.','.$lng));
		
		if (isset($response->results))
		{
			return $response->results;
		}
		
		return $response;
	}
	
	public function address_to_ll($address)
	{
		if ($geo_data = self::address($address))
		{
			// Get the top result
			$top_result = $geo_data[0];
			
			return array($top_result->geometry->location->lat, $top_result->geometry->location->lng);
		}

		return $geo_data;
	}
	
	/**
	 * Returns a url to the Google geocoding service.
	 *
	 * @param   array   API parameters
	 * @return  string
	 */
	 public function api_url($params = array())
	 {
		if (empty($params['ie']))
		{
			// Set input encoding to UTF-8
			$params['ie'] = 'utf-8';
		}

		if (empty($params['oe']))
		{
			// Set ouput encoding to input encoding
			$params['oe'] = $params['ie'];
		}
		
		if (empty($params['sensor']))
		{
			// Disable the location sensor
			$params['sensor'] = 'false';
		}
		
		$protocol = (isset($this->_config['enable_https']) AND $this->_config['enable_https'] === TRUE) ? 'https://' : 'http://';
		
		return $protocol.'maps.googleapis.com/maps/api/geocode/json?'.http_build_query($params);
	 }
	
	/**
	 * Queries the Google geocoding service and returns the results.
	 *
	 * @param   array     API parameters
	 * @return  stdclass  Geocode response
	 */
	protected function query_google($params = array())
	{
		// Check if caching is enabled
		$cache = ( ! empty($this->_config['cache'])) ? HTTP_Cache::factory($this->_config['cache']) : NULL;
		
		// Create the request
		$request = Request::factory($this->api_url($params), $cache);
		
		// Setup the retry counter and retry delay
		$remaining_attempts = isset($this->_config['attempts']) ? $this->_config['attempts'] : 10;
		$retry_delay = isset($this->_config['retry_delay']) ? $this->_config['retry_delay'] : 100000;
		
		// Enter the request/retry loop.
		while ($remaining_attempts > 0)
		{
			$response = $request->execute();
			$geo_data = json_decode($response->body());
			
			switch ($geo_data->status)
			{
				case self::OK:
				case self::ZERO_RESULTS:
					// Geocode was successful, don't retry.
					$remaining_attempts = 0;
					break;
				
				case self::OVER_QUERY_LIMIT:
					/**
					 * Google is rate limiting us - either we're making too many requests too fast, or
					 * we've exceeded the limit of 2.5k requests per day.
					 * See: http://code.google.com/apis/maps/documentation/geocoding/#Limits
					 */
					
					// Reduce the number of remaining attempts
					$remaining_attempts--;
					if ($remaining_attempts == 0)
					{
						throw new Geocode_Exception_Query_Limit('Exceeded the query limit');
					}
					else
					{
						// Sleep for $retry_delay microseconds before trying again.
					 	usleep($retry_delay);
					}
					break;
				
				case self::REQUEST_DENIED:
					throw new Geocode_Exception_Request_Denied('Geocode request was denied by the geocode service');
				
				case self::INVALID_REQUEST:
					throw new Geocode_Exception_Invalid_Request('Invalid geocode request');
				
				default:
					throw new Geocode_Exception('Unknown :status status returned in geocode response',
						array('status' => $geo_data->status));
			}
		}
		
		return $geo_data;
	}
}