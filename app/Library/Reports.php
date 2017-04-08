<?php namespace App\library;

use App\MeasurementTags;
use App\Measurements;

class Reports {
	public static function getMeasurements() {
		$m = [];
		$all = Measurements::get();
		foreach ($all as $key => $value) {
			$m[] = json_decode($value->wind_info)->wind_info[0];
			# code...
		}
		 	
		return $m;
	}
}