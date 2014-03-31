<?php

/**
 * As PHP is quite bad for making accurate money calculations, so calculator will round price to precision specified in constructor.
 *
 * Best address format: "<streetname> <home/flat address>, <city>, <country>"
 */
class ShippingCostByDistanceCalculator {

	private $_startingPoint;
	private $_ratePerUnit;
	private $_roundPrecision;
	private $_maxDistance;

	private $_distanceCalculator;

	/**
	 * @param $maxDistance        0 means: don't check if distance is correct
	 * @param $distanceCalculator default distanceCalculator has KM units
	 */
	public function __construct($startingPoint, $ratePerUnit, $roundPrecision = 2, $maxDistance = 50, DistanceCalculator $distanceCalculator = null) {
		$this->_startingPoint = $startingPoint;
		$this->_ratePerUnit = (double)$ratePerUnit;
		$this->_roundPrecision = (int)$roundPrecision;
		$this->_maxDistance = (double)$maxDistance;

		if (!$distanceCalculator) {
			$distanceCalculator = new DistanceCalculator(DistanceCalculator::KM);
		}
		$this->_distanceCalculator = $distanceCalculator;
	}

	/**
	 * @throw TooLongDistanceException
	 * @throw NotFoundException
	 */
	public function CalculateCostByRoute($endPoint, $calculateDistanceByRoute = true) {
		$distance = $this->_distanceCalculator->CalculateRouteDistance($this->_startingPoint, $endPoint);
		return $this->calculateCost($distance);
	}

	/**
	 * @throw TooLongDistanceException
	 * @throw NotFoundException
	 */
	public function CalculateCostBetweenPoints($endPoint, $calculateDistanceByRoute = true) {
		$distance = $this->_distanceCalculator->CalculateDistanceBetweenPoints($this->_startingPoint, $endPoint);
		return $this->calculateCost($distance);
	}

	private function calculateCost($distance) {
		if (!$this->isResultInCorrectRange($distance)) {
			throw new TooLongDistanceException("Result distance is out of specified range");
		}

		return $this->calculatePrice($distance);
	}

	private function isResultInCorrectRange($result) {
		if ($this->_maxDistance == 0) {
			return true;
		}

		return $result <= $this->_maxDistance;
	}

	private function calculatePrice($distance) {
		$price = $distance * $this->_ratePerUnit;

		return round($price, $this->_roundPrecision);
	}

}