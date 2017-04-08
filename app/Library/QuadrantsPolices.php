<?php namespace App\library;

use App\MeasurementTags;
use App\Library\Helpers;
use App\Library\Polices;
use App\Library\Groups;
use App\Library\Quadrants;


class QuadrantsPolices extends Polices{

	/**
	 * Apply polices
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function apply()
	{
		return self::windOppDirPolice();
	}	


	/**
	 * Wind opposite direction
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function activeQuadrants($groups)
	{	
		$totalAvg = [];
		if (array_key_exists('total_avg', $groups)) {
			$totalAvg = $groups['total_avg'];
			unset($groups['total_avg']);
		}
		$activeQuadrants = [];
		foreach ($groups as $sensor => $info) {
			if (!($info['info']['is_isolated'] == 1) && 
				( !($info['info']['ttl'] === 0) || ($info['info']['ttl'] === null) )) {

				$activeQuadrants[$sensor] = $info;
			}
		}
		$activeQuadrants['total_avg'] = $totalAvg;
		return $activeQuadrants;
	}
	
}
