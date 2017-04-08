<?php namespace App\library;

use App\Library\Helpers;
use App\Library\Buffer;
use App\Measurements;
use App\Sets as Set;
use App\MSets;

/*
sets devuelve:
- n of sets
- array de sets
- get last set
- get set
- tendencia de un set con peso
- tabla de tendencias con peso

*/
class Sets extends Buffer {

	/**
	 * Gets the trends table.
	 *
	 * @return     <type>  The trends table.
	 */
	public static function getTrendsTable() 
	{
		$sets = Set::select('id')->get();
		$table = [];
		foreach ($sets as $key => $set) {
			$table[$set->id] = self::getSetTrend($set->id);
		}
		return $table;
	}


	/**
	 * Gets the n sets.
	 *
	 * @return     <type>  The n sets.
	 */
	public static function getNSets() 
	{
		return Set::count();
	}

	/**
	 * Gets the sets.
	 *
	 * @return     <type>  The sets.
	 */
	public static function getSets() 
	{
		return Set::all();
	}


	/**
	 * Gets the last set.
	 *
	 * @return     <type>  The last set.
	 */
	public static function getLastSet() 
	{
		return Set::find(Set::max('id'));
	}


	/**
	 * Gets the set.
	 *
	 * @return     <type>  The set.
	 */
	public static function getSet($id) 
	{
		return Set::find($id);
	}

	/**
	 * Gets the set.
	 *
	 * @return     <type>  The set.
	 */
	public static function getMSet($id) 
	{
		return MSets::where('set_id', $id)->with('measurement','set')->get();
	}

	/**
	 * Gets the set trend.
	 *
	 * @return     <type>  The set trend.
	 */
	public static function getSetTrend($id) 
	{
		$trend = [];

		// Get Cols
		$sCols = self::getSensorsColumnsFromSet($id)['co2'];
		$wColsPure = self::getSensorsColumnsFromSet($id)['wind'];
		$wCols = [];

		foreach ($sCols as $key => $value) {
			$wind = self::getMyWind($key);
			$wCols[$wind]  = [];
			foreach ($value as $index => $val) {
				if ($val != 0 && array_key_exists($index, $wColsPure[$wind])) {
					$wCols[$wind][] = $wColsPure[$wind][$index];
				} 
			}
		}

		$wTrend = [];
		foreach ($wCols as $sensor => $info) {
			$wTrend[$sensor] = [
			'velocity' => self::getWindDirectionTrend($info)['velocity'],
			'direction' => self::getWindDirectionTrend($info)['direction']
			];
		}

		foreach ($sCols as $sensor => $ppms) {
			$sum = array_sum($ppms);
			$trend[$sensor] = [
				'length' => self::countOnes($ppms),
				'weight' => $sum,
				'avg' => (round($sum/count($ppms))),
				'wind' => $wTrend[self::getMyWind($sensor)]
			];
		}

		return $trend;
	}

	public static function getWindDirectionTrend($info)
	{
		$directions = [];
		$vel = [];
		foreach ($info as $key => $value) {
			$directions[] = $value['direction'];
			$vel[] = $value['velocity'];
		}
		$countVel = count($vel) == 0 ? 1: count($vel);
		return [
			'direction' => array_count_values($directions),
			'velocity' => array_sum($vel)/$countVel
		];
	}


  /**
   * Counts the number of ones.
   *
   * @param      <type>  $list   The list
   *
   * @return     <type>  Number of ones.
   */
  public static function countOnes($list)
	{	
		$all = array_count_values($list);
		if (array_key_exists(0, $all)) {
			unset($all[0]);
		}
		return array_sum($all);
	}

	/**
	 * Gets the sensors columns from set.
	 *
	 * @param      <type>  $id     The identifier
	 *
	 * @return     array   The sensors columns from set.
	 */
	public static function getSensorsColumnsFromSet($id)
	{
		$mSet = self::getMSet($id)->toArray();
		$sCols = $wCols = [];
		foreach ($mSet as $key => $info) {
			foreach (Helpers::getSensorsInfo($info['measurement']) as $key => $sensorPackage) {
				$sCols[ $sensorPackage->identifier][] = $sensorPackage->ppm;
			};
			foreach (Helpers::getWindInfo($info['measurement']) as $key => $windPackage) {
				$wCols[ $windPackage->identifier][] = [
					'velocity'=> $windPackage->velocity,
					'unit' => $windPackage->unit,
					'direction' => $windPackage->direction
				];
			};
		}
		return ['co2' => $sCols, 'wind' => $wCols];
	}

}
