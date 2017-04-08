<?php namespace App\library;

use App\MeasurementTags;
use App\Measurements;
use App\Collectors;

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

	public static function getMeasurementsByID($id) {
		$m = [];
		$all = Collectors::where('identifier', $id)->orderBy('id', 'DESC')->get();
		// foreach ($all as $key => $value) {
		// 	$m[] = json_decode($value->wind_info)->wind_info[0];
		// 	# code...
		// }
		 	
		return $all;
	}
}