<?php namespace App\library;

use App\MeasurementTags;
use App\Measurements;
use App\Groups as Group;
use App\GroupsHistory;
use App\Sets as Set;
use App\SetGroups;
use App\Library\Helpers;
use App\Library\Buffer;
use App\Library\Sets;
use App\Library\QuadrantsPolices;
use App\Library\WindPolices;
use App\Library\Quadrants;

/*

	cambio:: isolate contemplara todos los sets y mantiene condicion de avg min


	- no se forman grupos de sets inactivos
	
	 * ttl default == null -> indica que no moriran
	 * 
	 * se obtiene la tabla de groupos live
	 * se identifican los grupos isolados | 1 set | set con avg <= AVG_MIN*TOTAL_AVG => se requiere total avg
	 * 	si is isolated set ttl to N actualizations rounds
	 *  Si no hay cambios --TTL
	 *  if TTL  = 0  set set as inactive
	 * 
	 * se re obtiene la tabla de groups live
	 * se guarda en history
	
		Para cada sensor desde la tabla de trends
		* peso
		* average
		* lenght
		* isolated
		* sets[ids]
*/
class Groups extends Sets {

	const TTL = 4000000;
	const AVG_MIN = 0.80;

  // /**
  //  * Gets the groups information.
  //  *
  //  * @return     array  The groups information.
  //  */
  // public static function getGroupsInfo()
  // {	
  // 		// return Quadrants::getRegion();
  // 		// return Quadrants::setSensorsLocations([40.657172, -4.70512], 30);

  // 		$groups = self::processGroups();

  // 		// Get active quads
  // 		$activeCuadrants = QuadrantsPolices::activeQuadrants($groups);
  // 		$totalAvg = [];
		// 	if (array_key_exists('total_avg', $activeCuadrants)) {
		// 		$totalAvg = $activeCuadrants['total_avg'];
		// 		unset($activeCuadrants['total_avg']);
		// 	}
  // 		$groupsInfo = [];
  // 		foreach ($activeCuadrants as $sensor => $info) {
  // 				$groupsInfo[$sensor] = $info;
  // 				$groupsInfo[$sensor]['active_sub_quadrants'] = WindPolices::apply($info['group']['w_dir']);
  // 				$groupsInfo[$sensor] = Quadrants::appendQuadrants($sensor, $groupsInfo[$sensor]);
  // 		}
  // 		return $groupParsed = [
  // 				'active_sensors' => $groupsInfo
  // 				// 'sensors_ubications' => Quadrants::getSensorsLocations(),
  // 				// 'region' =>  Quadrants::getRegion()
  // 		];
  // }	


  /**
   * Process groups
   *
   * @return     array  ( description_of_the_return_value )
   */
  public static function processGroups()
  {
  	$live = self::getTrendGroupTable();
  		$info = [];
  		foreach ($live as $key => $group) {
  			$groupDb = Group::where('sensor_identifier', $key)->first();
  			$data = ['is_isolated'=> $groupDb['is_isolated'], 'ttl'=> $groupDb['ttl']];
  			$info[$key]['group'] = $group;
  			$info[$key]['info'] = $data;
  		}
  		self::saveHistory($info);
  		return $info;
  }

  /**
   * Saves a history.
   *
   * @param      <type>  $info   The information
   */
  public static function saveHistory($info)
  {
  	if (array_key_exists('total_avg', $info)) {
  		unset($info['total_avg']);
  	}
  	
  	$data = [];

  	$packageIdentifier = uniqid();

  	foreach ($info as $sensor => $group) {
  		$groupData = Group::where('sensor_identifier', $sensor)->first();
  		$history = GroupsHistory::create([
  			'package_identifier' => $packageIdentifier,
  		 	'group_id' => $groupData['id'], 
  		 	'ttl' => $group['info']['ttl'], 
  		 	'weight' => $group['group']['weight'], 
  		 	'length' =>  $group['group']['lenght'], 
  		 	'avg' => $group['group']['avg'], 
  		 	'is_isolated' => $group['info']['is_isolated'],
  		 	'wind_velocity_avg' => $group['group']['w_vel_avg'],
  		 	'wind_directions_trends' => json_encode($group['group']['w_dir']),
  		 ]);

  		if (is_array($group['group']['sets']) && !empty($group['group']['sets'])) {
	  		foreach ($group['group']['sets'] as $key => $setId) {
	  			SetGroups::create(['group_id' => $groupData['id'], 'set_id' => $setId, 'history_id'=> $history->id]);
	  		}
	  	}
  	}
  }


  /**
   * Isolate :: A REFACTORIZAR
   * =======================================================================================================
   *
   * @return     <type>  ( description_of_the_return_value )
   */
  public static function isolate()
  {	
  	$live  = self::getTrendGroupTable();
  	$totalAvg = $live['total_avg'];
  	unset($live['total_avg']);


  	foreach ($live as $key => $sensor) {
  		if (empty($sensor['sets'])) {
  		} else {
  			if ($sensor['avg'] <= $totalAvg*self::AVG_MIN) {
  				$group = Group::where('sensor_identifier', $key)->first();
  				$ttl = $group->ttl == null ? self::TTL : $group->ttl;
  				
  				if ( $group->ttl <= self::TTL && $group->ttl > 0) {
  					--$ttl;
  				}

  				if ($ttl == 0 && !empty($sensor['sets'])) {
  					foreach ($sensor['sets'] as $key => $set) {
  						Set::where('id', $set)->update(['is_active'=> 0]);
  					}
  				}
  			} 
  		}
  	}


  	return  self::getTrendGroupTable();	
  }



	public static function isolateBak1()
  {	
  	$live  = self::getTrendGroupTable();
  	$totalAvg = $live['total_avg'];
  	unset($live['total_avg']);
  	foreach ($live as $key => $sensor) {
  		if (empty($sensor['sets'])) {
  			Group::where('sensor_identifier', $key)->update(['ttl'=> 0, 'is_isolated'=>1]);
  		} else {
  			if (count($sensor['sets']) == 1 && $sensor['avg'] <= $totalAvg*self::AVG_MIN) {
  				$group = Group::where('sensor_identifier', $key)->first();
  				$ttl = $group->ttl == null ? self::TTL : $group->ttl;
  				if ( $group->ttl <= self::TTL && $group->ttl > 0) {
  					--$ttl;
  				}

  				if ($ttl == 0 && !empty($sensor['sets'])) {
  					foreach ($sensor['sets'] as $key => $set) {
  						Set::where('id', $set)->update(['is_active'=> 0]);
  					}
  				}
  				Group::where('sensor_identifier', $key)->update(['ttl'=> $ttl, 'is_isolated'=>1]);
  			} else {
  				Group::where('sensor_identifier', $key)->update(['is_isolated'=>0]);
  			}
  		}
  	}
  	return  self::getTrendGroupTable();	
  }

	/**
	 * Gets the trend group table.
	 *
	 * @return     array  The trend group table.
	 */
	public static function getTrendGroupTable()
	{
		$groupTrend = [];
		$trendsTable = self::getTrendsTable();

		$groupBySensor = [];
		foreach ($trendsTable as $set => $sensors) {
			foreach ($sensors as $sensor => $sTrend) {
				$sTrend['set'] = self::checkActiveSet($set) ? $set : 0;
			
				$groupBySensor[$sensor][] = $sTrend;
			}
		}

		foreach ($groupBySensor as $sensor => $trend) {

				$groupTrend[$sensor] = [
					'weight' => self::gWeight($trend),
					'lenght' => self::gLenght($trend),
					'avg' => self::gAvg($trend),
					'sets' => self::gSets($trend),
					'w_vel_avg' => self::gWVel($trend),
					'w_dir' => self::gWDir($trend)
				];
		}
		$groupTrend['total_avg'] = self::totalAvg($groupTrend);
		return $groupTrend;
	}

	/**
	 * Check active set
	 *
	 * @param      <type>  $id     The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function checkActiveSet($id)
	{
		return Set::find($id)->is_active;
	}


	/**
	 * Get total avg
	 *
	 * @param      <type>  $groups  The groups
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function totalAvg($groups)
	{
		$avg = [];
		foreach ($groups as $key => $group) {
			$avg[] = $group['avg'];
		}
		return array_sum($avg)/4;
	}


	public static function gWVel($trend)
	{
		$avgVel = [];
		foreach ($trend as $key => $value) {
			if ($value['set'] != 0 && $value['weight'] != 0) {
				$avgVel[] = $value['wind']['velocity'];
			}
		}
		return round(array_sum($avgVel)/count($trend));
	}

	public static function gWDir($trend)
	{
		$direction = [];
		foreach ($trend as $key => $value) {
			if ($value['set'] != 0 && $value['weight'] != 0) {
				$direction[] = $value['wind']['direction'];
			}
		}
		$dirSummary = [];
		foreach ($direction as $key => $value) {
			foreach ($value as $key2 => $value2) {
					$dirSummary[$key2][] = $value2 ;
			}
		}
		$directionValues = [];
		foreach ($dirSummary as $key => $value) {
			$directionValues[$key] = array_sum($value); 
		}

		return ($directionValues);
	}


	public static function gWeight($trend)
	{
		$weight = 0;
		foreach ($trend as $key => $value) {
			if ($value['set'] != 0 && $value['weight'] != 0) {
				$weight += $value['weight'];
			}
		}
		return $weight;
	}

	public static function gLenght($trend)
	{
		$list = [];
		foreach ($trend as $key => $value) {
			if ($value['set'] != 0 && $value['weight'] != 0) {
				$list[] = $value['length'];
			}
			
		}
		return self::countOnes($list);
	}

	public static function gAvg($trend)
	{
		$list = [];
		foreach ($trend as $key => $value) {
			if ($value['set'] != 0 && $value['weight'] != 0) {
					$list[] = $value['avg'];
				}
			
		}
		return round(array_sum($list)/count($trend));
	}

	public static function gSets($trend)
	{
		$list = [];
		foreach ($trend as $key => $value) {
			if ($value['avg'] != 0) {
				if ($value['set'] != 0 && $value['weight'] != 0) {
					$list[] = $value['set'];
				}
				
			}
		}
		return $list;
	}

	public function isSetActive($id)
	{
	}

	public function inactiveSet($id)
	{
	}



}
