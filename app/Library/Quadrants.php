<?php namespace App\library;

use App\Sensors;
use App\SensorLocations;
use App\Library\Helpers;
use LucDeBrouwer\Distance\Distance;
		

/**
 *				NO   N	   NE
 *				     |
 *				O----|---- E
 *					 |
 *				SO   S    SE
 *					 
 *  Calculate segment
 *  
 *  Get cuadrant by sensor
 *  
 */
class Quadrants extends Helpers{


	/**
	 * Gets the region.
	 *
	 * @return     array  The region.
	 */
	public static function getEventsLocations($subQuads)
	{
		if (!is_array($subQuads)) {
			return false;
		}

		$type = $subQuads['type'];
		unset($subQuads['type']);

		switch ($type) {
			case 'one':
				return [self::getOneSourceLocation($subQuads)];
				break;
			case 'pair':
				return [self::getPairSourceLocation($subQuads)];
				break;
			case 'corner':
				return [self::getCornerSourceLocation($subQuads)];
				break;
			case 'mix':
				return self::getMix($subQuads);
				break;
		}
	}	



	public static function  getMix($quads)
	{
		$sensors = [];
		$quadrants = [];
		foreach ($quads as $sensor => $quadrant) {
			$sensors[] = $sensor;
			$quadrants[$sensor] = $quadrant;
		}
		
		$sLocations = [];
		foreach ($sensors as $key => $sensor) {
			$sLocations[] = self::getSubQuadCenter(self::getSensorLocation($sensor), $quadrants[$sensor] );
		}

		return $sLocations;
	}

	/**
	 * Gets the corner source location.
	 *
	 * @param      <type>  $quads  The quads
	 *
	 * @return     <type>  The corner source location.
	 */
	public static function getCornerSourceLocation($quads)	 
	{
		$sensors = [];
		$quadrants = [];
		foreach ($quads as $sensor => $quadrant) {
			$sensors[] = $sensor;
			$quadrants[] = $quadrant;
		}
	
		$sensors = self::getSensorSpoted($quads);
		
		$sensorL = self::getSensorLocation($sensors);
		return (self::getSubQuadCenter($sensorL, $quadrants[0]));
	}

	/**
	 * Gets one source location.
	 *
	 * @param      <type>  $quads  The quads
	 *
	 * @return     <type>  The one source location.
	 */
	public static function getOneSourceLocation($quads)	 
	{
		$sensorL = [];
		$quadrant = [];
		foreach ($quads as $key => $value) {
			$sensorL = self::getSensorLocation($key);
			$quadrants = $value;
		}
		return (self::getSubQuadCenter($sensorL, $quadrants));
	}

	/**
	 * Gets the sub quad center.
	 *
	 * @param      <type>  $sensorL  The sensor l
	 * @param      <type>  $sub      The sub
	 *
	 * @return     <type>  The sub quad center.
	 */
	public static function getSubQuadCenter($sensorL, $sub)
	{
		$segment = ((self::segment())/1000)/2;

		$x1 = (float)$sensorL['lat'];
		$x2 = (float)$sensorL['long'];

		return self::getSubQuadCenterOP($x1, $x2, $sub, $segment);
	}

	/**
	 * Gets the sub quad center op.
	 *
	 * @param      <type>   $x1       The x 1
	 * @param      <type>   $x2       The x 2
	 * @param      <type>   $sub      The sub
	 * @param      integer  $segment  The segment
	 *
	 * @return     <type>   The sub quad center op.
	 */
	public static function getSubQuadCenterOP($x1, $x2, $sub, $segment)
	{
		switch ($sub) {
			case [1]:
				$m1 = self::geoDestination([$x1,$x2], $segment, -90);
				return self::geoDestination($m1, $segment, 0);
				break;
			case [2]:
				$m1 = self::geoDestination([$x1,$x2], $segment, 90);
			 	return self::geoDestination($m1, $segment, 0);
				break;
			case [3]:
				$m1 = self::geoDestination([$x1,$x2], $segment, 180);
			 	return self::geoDestination($m1, $segment, -90);
				break;
			case [4]:
				$m1 = self::geoDestination([$x1,$x2], $segment, 180);
			 	return self::geoDestination($m1, $segment, 90);
				break;
			case [1,2]:
				return self::geoDestination([$x1,$x2], $segment*2, 0);
				break;
			case [1,3]:
				return self::geoDestination([$x1,$x2], $segment*2, -90);
				break;
			case [2,4]:
				return self::geoDestination([$x1,$x2], $segment*2, 90);
				break;
			case [3,4]:
				return self::geoDestination([$x1,$x2], $segment*2, 180);
				break;			
			default:
				break;
		}
		
	}

	/**
	 * Gets the middle sensor location.
	 *
	 * @param      <type>  $sensors  The sensors
	 *
	 * @return     <type>  The middle sensor location.
	 */
	public static function getMiddleSensorLocation($sensors)
	{
		$a = self::getSensorLocation($sensors[0]);
		$segment = ((self::segment())/1000)/2;

		$x1 = (float)$a['lat'];
		$x2 = (float)$a['long'];

		switch ($sensors) {
			case ['0001', '0002']:
			case ['0002', '0001']:
				$m1 = self::geoDestination([$x1,$x2], $segment*2, 90);
				return self::geoDestination($m1, $segment*2, 0);
				break;
			case ['0002', '0004']:
			case ['0004', '0002']:
				$m1 = self::geoDestination([$x1,$x2], $segment*2, 90);
				return self::geoDestination($m1, $segment*2, 180);
				break;
			case ['0001', '0003']:
			case ['0003', '0001']:
				$m1 = self::geoDestination([$x1,$x2], $segment*2, -90);
				return self::geoDestination($m1, $segment*2, 180);
				break;
			case ['0003', '0004']:
			case ['0004', '0003']:
				$m1 = self::geoDestination([$x1,$x2], $segment*2.3	, 90);
				return self::geoDestination($m1, $segment*2, 189);
				break;
		}
		
	}

	/**
	 * Gets the pair source location.
	 *
	 * @param      <type>  $quads  The quads
	 *
	 * @return     <type>  The pair source location.
	 */
	public static function getPairSourceLocation($quads)	 
	{
		$sensors = [];
		$quadrants = [];
		foreach ($quads as $sensor => $quadrant) {
			$sensors[] = $sensor;
			$quadrants[] = $quadrant;
		}
	
		$sensors = self::getSensorSpoted($quads);

		if (count($sensors) == 1) {
			$sensorL = self::getSensorLocation($sensors);
			return (self::getSubQuadCenter($sensorL, $quadrants[0]));
		} else {
			return self::getMiddleSensorLocation($sensors);
		}
	}

	/**
	 * Gets the sensor spoted.
	 *
	 * @param      <type>  $quadrants  The quadrants
	 */
	public static function getSensorSpoted($quadrants) 
	{	
		$sensors = [];
		$allQuads = [];
		foreach ($quadrants as $sensor => $quadrant) {
			$sensors[] = $sensor;
			foreach ($quadrant as $key => $value) {
				$allQuads[] = $value;
			}
		}

		$quad = array_keys(array_count_values($allQuads));
		return count($quad) != 1 ? self::getPairDir($quad, $sensors[0], $sensors[1]) : self::getCornerDir($quad, $sensors[0], $sensors[1]);
	}

	public static function getCornerDir($quad, $s1, $s2)
	{
		if (($s1 == '0001'  || $s1 == '0004') && ($s2 == '0004' || $s2 == '0001') && $quad == [1]) {
			return '0001';
		} elseif (($s1 == '0001'  || $s1 == '0004') && ($s2 == '0004' || $s2 == '0001') && $quad == [4]) {
			return '0004';
		} elseif (($s1 == '0002'  || $s1 == '0003') && ($s2 == '0003' || $s2 == '0002') && $quad == [2]) {
			return '0002';
		} elseif (($s1 == '0002'  || $s1 == '0003') && ($s2 == '0003' || $s2 == '0002') && $quad == [3]) {
			return '0003';
		} 
	}


	/**
	 * Gets the pair dir.
	 *
	 * @param      array         $quad   The quad
	 * @param      string        $s1     The s 1
	 * @param      string        $s2     The s 2
	 *
	 * @return     array|string  The pair dir.
	 */
	public static function getPairDir($quad, $s1, $s2)
	{
		if ($s1 == '0002' && $s2 == '0004' && $quad == [2, 4]) {
			return ['0002', '0004'];
		} elseif ($s1 == '0003' && $s2 == '0004' && $quad == [3, 4]) {
			return ['0003', '0004'];
		} elseif ($s1 == '0001' && $s2 == '0003' && $quad == [1, 3]) {
			return ['0001', '0003'];
		} elseif ($s1 == '0001' && $s2 == '0002' && $quad == [1, 2]) {
			return ['0001', '0002'];
		} else {
			switch ($quad) {
				case [1,3]:
				case [3,1]:
					return  $s1;
					break;
				case [2,4]:
				case [4,2]:
					return  $s2;
					break;
				case [1,2]:
				case [2,1]:
					return  $s1;
					break;
				case [3,4]:
				case [4,3]:
					return   $s2;
					break;	
			}
		}
	}


	/**
	 * Gets the sensors locations from a. // A X metros uno del otro
	 *
	 * @param      array  $center    The center {lat, lng}
	 * @param      int     $distance  in meters
	 *
	 * @return     array   The sensors locations from a.
	 */
	public static function getSensorsLocationsFromCenter($center, $distance)
	{
		$x1 = (float)$center[0];
		$y1 = (float)$center[1];

		$distance = $distance/1000;
		
		$segment = $distance/2;

		// Walk from center to A
		$topFromCenter = self::geoDestination([$x1,$y1], $segment, 0);

		$a = self::geoDestination($topFromCenter, $segment, -90);

		$b = self::geoDestination($a, $distance, 90);
		$c = self::geoDestination($a, $distance, 180);
		$d = self::geoDestination($c, $distance, 90);
		
		return [
			'center' => $center,
			'A' => $a,
			'B' => $b,
			'C' => $c,
			'D' => $d
		];
	}
	/**
	 * Gets the sensors locations.
	 *
	 * @return     array  The sensors locations.
	 */
	public static function getSensorsLocations()
	{
		return [
			'A' => self::A(),
			'B' => self::B(),
			'C' => self::C(),
			'D' => self::D()
		];
	}
	/**
	 * Sets the sensors locations.
	 *
	 * @param      <type>  $distance  The distance in meters
	 */
	public static function setSensorsLocations($center, $distance)
	{

		$locations = self::getSensorsLocationsFromCenter($center, $distance);
		
		$a = Sensors::where('identifier', '0001')->first();
		$aLocation = json_encode([
			"lat" => $locations['A'][0],
			"long" => $locations['A'][1]
		]);

		$setA = SensorLocations::where('sensor_id', $a->id)->update([
			'coordinates' => $aLocation
		]);


		$b = Sensors::where('identifier', '0002')->first();
		$bLocation = json_encode([
			"lat" => $locations['B'][0],
			"long" => $locations['B'][1]
		]);

		$setB = SensorLocations::where('sensor_id', $b->id)->update([
			'coordinates' => $bLocation
		]);


		$c = Sensors::where('identifier', '0003')->first();
		$cLocation = json_encode([
			"lat" => $locations['C'][0],
			"long" => $locations['C'][1]
		]);

		$setC = SensorLocations::where('sensor_id', $c->id)->update([
			'coordinates' => $cLocation
		]);


		$d = Sensors::where('identifier', '0004')->first();
		$dLocation = json_encode([
			"lat" => $locations['D'][0],
			"long" => $locations['D'][1]
		]);

		$setD = SensorLocations::where('sensor_id', $d->id)->update([
			'coordinates' => $dLocation
		]);
		return true;
	}

	/**
	 * Gets the sensor location.
	 */
	public static function getSensorLocation($identifier)
	{
		$sensor = Sensors::where('identifier', $identifier)->first();
		$coordinates = json_decode(SensorLocations::find($sensor->id)->coordinates, true);
		return $coordinates;
	}

	
	/**
	 * Appends a quadrants.
	 *
	 * @param      <type>  $sensor  The sensor
	 * @param      <type>  $group   The group
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function appendQuadrants($sensor, $group) 
	{
		$subQuadsInfo = $group['active_sub_quadrants'];
		$subQuads = [];
		if (empty($subQuadsInfo)) {
			return [];
		}
		foreach ($subQuadsInfo as $key => $sub) {
			$subQuads[] =  self::getCoordinatesBySubQuad($sensor, $sub);
		}
		$group['quadrants'] = [
			'sensor_center_point' => self::getSensorLocation($sensor),
			'sub_quads' => $subQuads
		];
		return $group;
	}

	/**
	 * Gets the coordinates by sub quad.
	 *
	 * @param      <type>  $identifier  The identifier
	 * @param      <type>  $sub         The sub
	 *
	 * @return     array   The coordinates by sub quad.
	 */
	public static function getCoordinatesBySubQuad($identifier, $sub)
	{
		$distance = self::segment()/1000;
		$sensorCoordinates = self::getSensorLocation($identifier);
		$x1 = (float)$sensorCoordinates['lat'];
		$y1 = (float)$sensorCoordinates['long'];

		$north = self::geoDestination([$x1,$y1], $distance, 0);
		$west = self::geoDestination([$x1,$y1], $distance, -90);
		$south = self::geoDestination([$x1,$y1], $distance, 180);
		$east = self::geoDestination([$x1,$y1], $distance, 90);

		$northWest =  self::geoDestination($west, $distance, 0);
		$northEast =  self::geoDestination($east, $distance, 0);
		$southWest =  self::geoDestination($west, $distance, 180);
		$southEast =  self::geoDestination($east, $distance, 180);

		switch ($sub) {
			case 1:
				return [ 'sub_quad' => [$northWest, $north, [$x1,$y1],$west], 
				'summary' => ['no', 'n', 'c', 'o']];
				break;
			case 2:
				return ['sub_quad' => [$north, $northEast, $east ,[$x1,$y1]], 
				'summary' => ['n', 'ne', 'e', 'c'] ];
				break;
			case 3:
				return ['sub_quad' => [$west, $southWest, $south, [$x1,$y1]], 
				'summary' => ['o', 'so', 's', 'c'] ];
				break;
			case 4:
				return ['sub_quad' => [$southEast, $south, [$x1,$y1], $east], 
				'summary' => ['se', 's', 'c','e'] ];
				break;
			
			default:
				return ['sub_quad' => [], 'summary' => [] ];
				break;
		}
	}



	public static function A()
	{
		return self::getSensorLocation('0001');
	}

	public static function B()
	{
		return self::getSensorLocation('0002');
	}

	public static function C()
	{
		return self::getSensorLocation('0003');
	}

	public static function D()
	{
		return self::getSensorLocation('0004');
	}

	/**
	 * Calculate distance in metters 
	 *
	 * @param      	array    $from   	[lat, long]
	 * @param      	array    $to     	[lat, long]
	 *
	 * @return     Distance  ( description_of_the_return_value )
	 */
	public static function diffDistance($from, $to)
	{
      $distance = new Distance();
      $distance->setUnit('m');
      $distance->setFormula('haversine');
      return $dist = $distance->between($from['lat'],$from['long'], $to['lat'],$to['long']);
	}

	/**
	 * Get segment
	 *
	 * @param      <type>  $x      { parameter_description }
	 * @param      <type>  $y      { parameter_description }
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function segment()
	{		
		return self::diffDistance(self::A(), self::B())/2;
	}

	/**
	 * Gets the region.
	 *
	 * @return     array  The region.
	 */
	public static function getRegion()
	{
		$segment = (self::segment())/1000;

		$distance = (self::segment() * 2)/1000;

		$sensorCoordinates = self::A();

		$x1 = (float)$sensorCoordinates['lat'];
		$y1 = (float)$sensorCoordinates['long'];

		$n = self::geoDestination([$x1,$y1], $segment, 0);
		$no = self::geoDestination($n, $segment, -90);

		$s = self::geoDestination([$x1,$y1], $distance + $segment, 180);
		$so = self::geoDestination($s, $segment, -90);

		$n2 = self::geoDestination([$x1,$y1], $distance + $segment, 90);
		$ne = self::geoDestination($n2, $segment, 0);

		$se = self::geoDestination($ne, $distance + $segment + $segment, 180);

		$a1 = self::geoDestination($no, $segment , 90);
		$a2 = self::geoDestination($so, $segment , 90);

		$b1 = self::geoDestination($no, $segment*2 , 90);
		$b2 = self::geoDestination($so, $segment*2 , 90);

		$c1 = self::geoDestination($no, $segment*3 , 90);
		$c2 = self::geoDestination($so, $segment*3 , 90);


		$d1 = self::geoDestination($no, $segment*4 , 90);
		$d2 = self::geoDestination($so, $segment*4 , 90);

		$x1 = self::geoDestination($no, $segment, 180);
		$x2 = self::geoDestination($ne, $segment , 180);

		$y1 = self::geoDestination($no, $segment*2, 180);
		$y2 = self::geoDestination($ne, $segment*2 , 180);

		$w1 = self::geoDestination($no, $segment*3, 180);
		$w2 = self::geoDestination($ne, $segment*3 , 180);

		$z1 = self::geoDestination($no, $segment*4, 180);
		$z2 = self::geoDestination($ne, $segment*4 , 180);

		return [
			'region' => [
				'A' => $no,
				'B' => $ne,
				'C' => $so,
				'D' => $se
			],
			'dividers' => [
				'A1' => $a1,
				'A2' => $a2,
				'B1' => $b1,
				'B2' => $b2,
				'C1' => $c1,
				'C2' => $c2,
				'D1' => $d1,
				'D2' => $d2,

				'X1' => $x1,
				'X2' => $x2,
				'Y1' => $y1,
				'Y2' => $y2,
				'W1' => $w1,
				'W2' => $w2,
				'Z1' => $z1,
				'Z2' => $z2,
			],
		];
	}


	
	
}
