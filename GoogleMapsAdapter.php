<?php

class GoogleMapsAdapter {

	private $_apiKey;

	public function __construct($apiKey = null) {
		$this->_apiKey = $apiKey;
	}

	/*
	 * @throw NotFoundException
	 * @return array format: ['lng': <longitude>, 'lat': <latitude>]
	 */
	public function GetGeoPosition($address) {
		$addressURL = urlencode($address);
		$apiKey = self::getAPIKeyForURL();
		$decodedData = self::getDecodedData('http://maps.googleapis.com/maps/api/geocode/json?address=' . $addressURL . '&sensor=false' . $apiKey);

		self::checkReceivedDataEmptiness($decodedData);

		return $decodedData['results'][0]['geometry']['location'];
	}

	/**
	 * @throw NotFoundException
	 */
	public function GetRouteDistance($startPoint, $endPoint, $distanceInKM = true) {
		$startAddressURL = urlencode($startPoint);
		$endAddressURL = urlencode($endPoint);
		$units = $distanceInKM === true ? 'metric' : 'imprerial';
		$apiKey = self::getAPIKeyForURL();
		$decodedData = self::getDecodedData('http://maps.googleapis.com/maps/api/directions/json?origin=' . $startAddressURL . '&destination=' . $endAddressURL . '&units=' . $units . '&sensor=false' . $apiKey);

		self::checkReceivedDataEmptiness($decodedData);

		return (double)$decodedData['routes'][0]['legs'][0]['distance']['text'];
	}

	protected function getAPIKeyForURL() {
		return $this->_apiKey == null ? '' : '&key=' . $this->_apiKey;
	}

	protected function getDecodedData($url) {
		return json_decode(@file_get_contents($url), true);
	}

	protected function checkReceivedDataEmptiness($data) {
		if ($data['status'] === "ZERO_RESULTS") {
			throw new NotFoundException("Point not found in Google Maps");
		}
	}
}
