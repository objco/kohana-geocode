# Kohana Geocode

Geocoding module for the [Kohana](http://kohanaframework.org/) framework.

**Supported Drivers**

- [Google Geocoding API (V3)](http://code.google.com/apis/maps/documentation/geocoding/)

## Geocode

	$geocode = Geocode::instance();
	$geo_data = $geocode->address('1600 Pennsylvania Avenue NW, Washington, DC 20500');

## Reverse Geocode

	$geocode = Geocode::instance();
	$geo_data = $geocode->ll(-34.6082801, -58.3708382);

## Address to Latitude and Longitude

	$geocode = Geocode::instance();
	list($lat, $lng) = $geocode->address_to_ll('The Kremlin, Moscow, Russia');