<?php namespace App\library;

use App\MeasurementTags;
use App\Library\Helpers;
use App\Library\Polices;
use App\Library\Groups;
use App\Library\Quadrants;
use App\Library\SourcePolices;
use App\Library\QuadrantsPolices;
use App\Library\WindPolices;

class SourceFinder extends Polices{

	 /**
   * Gets the groups information.
   *
   * @return     array  The groups information.
   */
  public static function getGroupsInfo()
  {	
      $union = [];
      $vel = [];
      $measurements = Buffer::getCurrentMeasurements(5);
      foreach ($measurements as $key => $value) {
        $wind = Buffer::getWindInfoAsArray($value);
        $ppm = Buffer::getSensorsInfoAsArray($value);
          foreach ($wind as $i => $w) {
             $union[$key][$i] = $w;
             $vel[$i][] = (int)$w['velocity'];
             $union[$key][$i]['ppm'] = $ppm[$i]['ppm'];
             $union[$key][$i]['co2_id'] = $ppm[$i]['identifier'];
          }
      }
  		// return Quadrants::getRegion();
  		// return Quadrants::setSensorsLocations([40.657172, -4.70512], 30);

  		$groups = Groups::processGroups();

  		// Get active quads
  		$activeCuadrants = QuadrantsPolices::activeQuadrants($groups);
  		$totalAvg = [];
			if (array_key_exists('total_avg', $activeCuadrants)) {
				$totalAvg = $activeCuadrants['total_avg'];
				unset($activeCuadrants['total_avg']);
			}
  		$groupsInfo = [];
  		foreach ($activeCuadrants as $sensor => $info) {
          $groupsInfo[$sensor] = $info;
  				$groupsInfo[$sensor]['info']['a'] = ['dsoin'];
  				$groupsInfo[$sensor]['active_sub_quadrants'] = WindPolices::apply($info['group']['w_dir']);
  				$groupsInfo[$sensor] = Quadrants::appendQuadrants($sensor, $groupsInfo[$sensor]);
  		}

      // $union = [];
      // $wind = Buffer::getWindInfoAsArray(Buffer::getCurrentMeasurement());
      // $ppm =  Buffer::getSensorsInfoAsArray(Buffer::getCurrentMeasurement());
      // foreach ($wind as $key => $value) {
      //     $union[$key] = $value;
      //     $union[$key]['ppm'] = $ppm[$key]['ppm'];
      //     $union[$key]['co2_id'] = $ppm[$key]['identifier'];
      // }
  		return $groupParsed = [
  				'active_sensors' => $groupsInfo,
          'source_ubication' => SourcePolices::findSource($groupsInfo),
          'sensors_current_info' => $vel,
  				'sensors_current_union' => $union
  				// 'sensors_ubications' => Quadrants::getSensorsLocations(),
  				// 'region' =>  Quadrants::getRegion()
  		];
  }
}
