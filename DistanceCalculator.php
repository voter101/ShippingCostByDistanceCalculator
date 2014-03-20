<?php

class DistanceCalculator {

	const KM = 0;
	const MI = 1;

	private $_unit;
	private $_gMapsAdapter;

	/**
	 * @throw InvalidArgumentException
	 */
	public function __construct($unit, GoogleMapsAdapter $gMapsAdapter = null) {
		switch ($unit) {
			case self::KM:
				break;
			case self::MI:
				break;
			default:
				throw new InvalidArgumentException("Given unit type isn't supported");
		}
		$this->_unit = $unit;

		if (!$gMapsAdapter) {
			$gMapsAdapter = new GoogleMapsCaller();
		}
		$this->_gMapsAdapter = $gMapsAdapter;
	}

	/**
	 * @throw NotFoundException
	 */
	public function CalculateDistanceBetweenPoints($startPoint, $endPoint) {
		$startGeoPosition = $this->_gMapsAdapter->GetGeoPosition($startPoint);
		$endGeoPosition = $this->_gMapsAdapter->GetGeoPosition($endPoint);
		return $this->calculateDistanceBetweenGeoPositions($startGeoPosition, $endGeoPosition);
	}

	private function calculateDistanceBetweenGeoPositions($startGeoPosition, $endGeoPosition) {
		$pi80 = M_PI / 180;
		$latitude1 = $startGeoPosition['lat'] * $pi80;
		$longitude1 = $startGeoPosition['lng'] * $pi80;
		$latitude2 = $endGeoPosition['lat'] * $pi80;
		$longitude2 = $endGeoPosition['lng'] * $pi80;

		$r = 6372.797; // Earth radius in meters
		$dLatitude = $latitude2 - $latitude1;
		$dLongtitude = $longitude2 - $longitude1;
		$x = ( sin($dLatitude / 2) * sin($dLatitude / 2) ) + ( cos($latitude1) * cos($latitude2) * sin($dLongtitude / 2) * sin($dLongtitude / 2) );
		$y = 2 * atan2(sqrt($x), sqrt(1 - $x));
		$km = $r * $y;
		return $this->_unit === self::KM ? $km : $km * 0.621371192;
	}

	/**
	 * @throw NotFoundException
	 */
	public function CalculateRouteDistance($startPoint, $endPoint) {

	}

} 