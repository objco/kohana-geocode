<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Test case to evaluate the integrity of the Geocode module and supporting functions.
 *
 * @package    Geocode
 * @author     Jonathan Davis <jonathan@obj.co>
 * @copyright  (c) 2011 The Objective Company
 * @license    https://raw.github.com/ObjectiveCompany/kohana-geocode/3.2/master/LICENSE.md
 * @group      geocode
 **/
class GeocodeTest extends Unittest_TestCase
{
	public function providerAddresses()
	{
		return array(
			array('1600 Pennsylvania Avenue NW, Washington, DC 20500'),
			array('Balcarce 50, 1086 Buenos Aires, Capital Federal, Argentina'),
			array('The Kremlin, Moscow, Russia'),
		);
	}
	
	public function providerLL()
	{
		return array(
			array(38.8976777, -77.036517),
			array(-34.6082801, -58.3708382),
			array(55.75, 37.616667),
		);
	}
	
	/**
	 * @dataProvider providerAddresses
	 */
	public function testAddress($address)
	{
		$geocode = Geocode::instance();
		$geo_data = $geocode->address($address);
		
		$this->assertNotEmpty($geo_data, 'No results for the following address: '.$address);
	}
	
	/**
	 * @dataProvider providerLL
	 */
	public function testLL($lat, $lng)
	{
		$geocode = Geocode::instance();
		$geo_data = $geocode->ll($lat, $lng);
		
		$this->assertNotEmpty($geo_data, 'No results for the following latitude and longitude: '.$lat.', '.$lng);
	}
	
	/**
	 * @dataProvider providerAddresses
	 */
	public function testAddressToLL($address)
	{
		$geocode = Geocode::instance();
		$ll = $geocode->address_to_ll($address);
		
		$this->assertNotEmpty($ll, 'No latitude and longitude data for the following address: '.$address);
		$this->assertEquals(2, count($ll), 'Malformed latitude and longitude response.');
		foreach ($ll as $value)
		{
			$this->assertTrue(is_numeric($value), 'Invalid latitude or longitude coordinate: '.$value);
		}
	}
}