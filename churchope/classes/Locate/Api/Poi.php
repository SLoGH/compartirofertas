<?php

class Locate_Api_Poi extends Locate_Api
{
		var $point = array('lat' => 0, 'lng' => 0),
		$title = '',
		$url = null,
		$body = '',
		$address = null,
		$correctedAddress = null,
		$iconid = null,
		$viewport = null,       // array('sw' => array('lat' => 0, 'lng' => 0), 'ne' => array('lat' => 0, 'lng' => 0))
        $user = false,          // If this marker represent's the user geolocation
        $showPoiList = true,    // True = show this marker in the marker list
		$poiListTemplate = null;

	/**
	* Geocode an address using http
	*
	* @param mixed $auto true = automatically update the poi, false = return raw geocoding results
	* @return true if auto=true and success | raw geocoding results if auto=false | WP_Error on failure
	*/
	function geocode($auto=true) {
		// If point was defined using only lat/lng then no geocoding
		if (!empty($this->point['lat']) && !empty($this->point['lng'])) {
			// Default title if empty
			if (empty($this->title))
				$this->title = $this->point['lat'] . ',' . $this->point['lng'];
			return;
		}

		$options = Locate_Api_Options::get();
		$language = $options->language;
		$country = $options->country;

		$address = urlencode($this->address);
		$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&output=json";
		if ($country)
			$url .= "&region=$country";
		if ($language)
			$url .= "&language=$language";

		$response = wp_remote_get($url);

		// If auto=false, then return the RAW result
		if (!$auto)
			return $response;

		// Check for http error
		if (is_wp_error($response))
			return $response;

		if (!$response)
			return new WP_Error('geocode', sprintf(__('No geocoding response from Google: %s', 'churchope'), $response));

		//Decode response and automatically use first address
		$response = json_decode($response['body']);

		// Discard empty results
		foreach((array)$response->results as $key=>$result) {
			if(empty($result->formatted_address))
				unset($response->results[$key]);
		}

		$status = isset($response->status) ? $response->status : null;
		if ($status != 'OK')
			return new WP_Error('geocode', sprintf(__("Google cannot geocode address: %s, status: %s", 'churchope'), $this->address, $status));

		if (!$response  || !isset($response->results) || empty($response->results[0]) || !isset($response->results[0]))
			return new WP_Error('geocode', sprintf(__("No geocoding result for address: %s", 'churchope'), $this->address));

		$placemark = $response->results[0];

		// Point
		$this->point = array('lat' => $placemark->geometry->location->lat, 'lng' => $placemark->geometry->location->lng);

		// Viewport
		if (isset($placemark->geometry->viewport)) {
			$this->viewport = array(
				'sw' => array('lat' => $placemark->geometry->viewport->southwest->lat, 'lng' => $placemark->geometry->viewport->southwest->lng),
				'ne' => array('lat' => $placemark->geometry->viewport->northeast->lat, 'lng' => $placemark->geometry->viewport->northeast->lng)
			);
		} else {
			$this->viewport = null;
		}

		// Corrected address
		$this->correctedAddress = $placemark->formatted_address;

		$parsed = Locate_Api_Poi::parse_address($this->correctedAddress);

		// If the title and body are not populated then default them
		if (!$this->title && !$this->body) {
			$this->title = $parsed[0];
			if ($parsed[1])
				$this->body = $parsed[1];
		}
	}

	/**
	* Static function to parse an address.  It will split the address into 1 or 2 lines, as appropriate
	*
	* @param mixed $address
	* @return array $result - array containing 1 or 2 address lines
	*/
	function parse_address($address) {
		// USA Addresses
		if (strstr($address, ', USA')) {
			// Remove 'USA'
			$address = str_replace(', USA', '', $address);

			// If there's exactly ONE comma left then return a single line, e.g. "New York, NY"
			if (substr_count($address, ',') == 1) {
				return array($address);
			}
		}

		// If NO commas then use a single line, e.g. "France" or "Ohio"
		if (!strpos($address, ','))
			return array($address);

		// Otherwise return first line from before first comma+space, second line after, e.g. "Paris, France" => "Paris<br>France"
		// Or "1 Main St, Brooklyn, NY" => "1 Main St<br>Brooklyn, NY"
		return array(
			substr($address, 0, strpos($address, ",")),
			substr($address, strpos($address, ",") + 2)
		);
	}
}
?>
