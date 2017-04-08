<?php namespace App\library;

use App\MeasurementTags;
use App\Library\Helpers;
use App\Library\Polices;
use App\Library\Groups;
use App\Library\Quadrants;


class WindPolices extends Polices{

	/**
	 * Apply polices
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function apply($windInfo)
	{
		return self::windOppDirPolice($windInfo);
	}	


	/**
	 *
	 * TO REFACTOR*************************************************8
	 * Wind opposite direction
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function windOppDirPolice($windInfo)
	{
		// $windInfo[''] = 30;
		// $windInfo['s'] = 30;
		// $windInfo['s'] = 30;

		$aprox = [];
		foreach ($windInfo as $dir => $avg) {
			$aprox[] = self::getOppSubQuads($dir);
		}
		// print_r($windInfo);
		// print_r($aprox);
		//INTERSECTION OF SAME ARRAY
		$res_arr = array_shift($aprox);
		foreach($aprox as $filter){
		     $res_arr = array_intersect(array_shift($aprox), $filter);
		}

		// print_r($res_arr);die();
		return $res_arr;
	}


	/**
	 * Gets the opp sub quads.
	 *
	 * @param      <type>  $dir    The dir
	 *
	 * @return     array   The opp sub quads.
	 */
	public static function getOppSubQuads($dir)
	{
			//    N      | 1 | 2 |
		  // O--|--E   | 3 | 4 |
		  //    S
			switch ($dir) {
				case 'n':
					return [3,4];
					break;
				case 's':
					return [1,2];
					break;
				case 'e':
					return [1,3];
					break;
				case 'o':
					return [2,4];
					break;
				case 'no':
					return  [4];
					break;
				case 'se':
					return [1];
					break;
				case 'ne':
					return [3];
					break;
				case 'so':
					return [2];
					break;
				default:
					return [1,2,3,4];
					break;
		}
	}
	
}
