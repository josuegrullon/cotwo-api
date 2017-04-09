<?php namespace App\library;

use App\MeasurementTags;
use App\Measurements;
use App\Collectors;
use Carbon\Carbon;   

class Reports {
	public static function getMeasurements() {
		$collectors =  Collectors::get()
			->groupBy(function($date) {
			    return Carbon::parse($date->created_at)->format('Y-m-d h'); // grouping by years
			});

		$order = [];
		foreach ($collectors as $date => $data) {
			$dir = 0;

			$getByIdentifier = function ($identifier) use ($data) {
				$identifierData = [];
				foreach ($data as $info) {
					if ($info->identifier == $identifier) {
						$identifierData[] = $info->toArray();
					}
				}
				return $identifierData;
			};	

			$getAvgColumn = function ($data, $column) {
				if (!count($data)) return 0;
				return array_sum(array_column($data, $column)) / count($data);
			};



			$appendData = function ($data) use ($getAvgColumn) {
				$dir = array_count_values(array_column($data, 'dir'));
				arsort($dir);
				$dir  = key($dir);

				if ($dir == null) $dir = '';

				return [
					'ppm' =>  $getAvgColumn($data, 'ppm'),
					'dir' => $dir,
					'velocity' => $getAvgColumn($data, 'velocity'),
					'temperature' => $getAvgColumn($data, 'temperature'),
					'humidity' => $getAvgColumn($data, 'humidity'),
					'presure' => $getAvgColumn($data, 'presure')
				];
			};

			$order[] = [
				'f_date' => $date,
				'avg_data' => $appendData($data->toArray()),
				'sensors' => [
					'0001' => $appendData($getByIdentifier('0001')),
					'0002' => $appendData($getByIdentifier('0002')),
					'0003' => $appendData($getByIdentifier('0003')),
					'0004' => $appendData($getByIdentifier('0004')),
				]
			];
		}

		return $order;

	}

	public static function getMeasurementsByID($id) {
		return Collectors::where('identifier', $id)->orderBy('id', 'DESC')->get();
	}
}