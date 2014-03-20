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
		$GETParams = [
			'address' => $address,
			'sensor' => 'false'
		];
		if ($this->_apiKey != null) {
			$GETParams['key'] = $this->_apiKey;
		}
		$decodedData = self::getDecodedData('http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($GETParams));

		self::checkReceivedDataEmptiness($decodedData);

		return $decodedData['results'][0]['geometry']['location'];
	}

	/**
	 * @throw NotFoundException
	 */
	public function GetRouteDistance($startPoint, $endPoint, $distanceInKM = true) {
		$GETParams = [
			'origin' => $startPoint,
		    'destination' => $endPoint,
		    'units' => $distanceInKM === true ? 'metric' : 'imprerial',
		    'sensor' => 'false'
		];
		if ($this->_apiKey != null) {
			$GETParams['key'] = $this->_apiKey;
		}
		$decodedData = self::getDecodedData('http://maps.googleapis.com/maps/api/directions/json?' . http_build_query($GETParams));

		self::checkReceivedDataEmptiness($decodedData);

		return (double)$decodedData['routes'][0]['legs'][0]['distance']['text'];
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
