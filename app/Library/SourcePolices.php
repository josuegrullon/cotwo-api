<?php namespace App\library;

use App\MeasurementTags;
use App\Library\Helpers;
use App\Library\Polices;
use App\Library\Groups;
use App\Library\Quadrants;

  /*
   * 2 modos: sigle source and multiple source
   * single source: 
   * 	pares de eventos: 
   * 		-sub quad independiente [n] or [x,n]
   * 		-sub quads central  o lateral
   * 		-2 cols iguales  de n length paralelos
   * 		-2 esquinas iguales de n length paralelas 
   * 		
   *	Corners
   *	Cols
   * Source mode 0 -> not set, 1 -> single, 2-> multi
   */
class SourcePolices extends Polices{

	public  static $quads;
	private static $_instance;
	private static $counter;
	private static $sourceMode;


	public static function getInstance() {
    if (!self::$_instance) {
        self::$_instance = new self();
    }
    return self::$_instance;
	}	
 	/**
 	 * Find source
 	 *
 	 * @param      <type>  $groupsInfo  The groups information
 	 *
 	 * @return     array   ( description_of_the_return_value )
 	 */
 	public static function findSource($groupsInfo)
  {
  	return self::filterQuads($groupsInfo)->applyPolices();
  }

  /**
   * Politica de deteccion de modo de source / single or multi source
   *
   * @param      <type>  $groupsInfo  The groups information
   *
   * @return     <type>  ( description_of_the_return_value )
   */
  public static function applyPolices()
  {

    if (!!self::detectOneEvent()) {
      $events = self::detectOneEvent();
    } elseif( !!self::detectCorners() ) {
      $events = self::detectCorners();
    } elseif (!!self::detectPairs()) {
      $events = self::detectPairs();
    } else {
      // $events = self::dectectMix();
      $events = false;
    }


  	$events = !!$events ? $events : ['type' => 'not supported for this version'];

  	$events['source_location'] = Quadrants::getEventsLocations($events);

  	return ['quads_live' => self::$quads, 'source_events' => $events];
  }

   public static function dectectMix()
   {
      $pair = [];
      $sensors = [];
      $parseOutput = [];

      foreach (self::$quads as $sensor => $subQuads) {
        if (!empty($subQuads) && count($subQuads) == 2) {
          $pair[] = $subQuads;
          $sensors[] = $sensor;
          $parseOutput[$sensor] = $subQuads;
        }
      }
      $parseOutput['type'] = 'mix';
      return  (!empty($sensors) ? $parseOutput : false);
   }


  /**
   * Detect Pairs
   *
   * @return     <type>  ( description_of_the_return_value )
   */
  public static function detectPairs()
  {
  	$pair = [];
  	$sensors = [];
  	$parseOutput = [];
		foreach (self::$quads as $sensor => $subQuads) {
			if (!empty($subQuads) && count($subQuads) == 2) {
				$pair[] = $subQuads;
				$sensors[] = $sensor;
				$parseOutput[$sensor] = $subQuads;
			}
		}
		$parseOutput['type'] = 'pair';
  	return  (!empty($sensors) && self::isInline($sensors, $pair)? $parseOutput : false);
  }


  /**
   * Detect Corners
   *
   * @return     array  ( description_of_the_return_value )
   */
  public static function detectCorners()
  {
  	$quads = [];
  	$sensors = [];
  	$parseOutput = [];
		foreach (self::$quads as $sensor => $subQuads) {
			if (!empty($subQuads) ) {
				$quads[] = $subQuads;
				$sensors[] = $sensor;
				$parseOutput[$sensor] = $subQuads;
			}
		}
		$allQuads = [];
		foreach ($quads as $key => $value) {
			foreach ($value as $key2 => $quad) {
				$allQuads[] = ( $quad);
			}
			
		}

		$A_SET = array_key_exists('0001', $parseOutput);
		$B_SET = array_key_exists('0002', $parseOutput);
		$C_SET = array_key_exists('0003', $parseOutput);
		$D_SET = array_key_exists('0004', $parseOutput);

		$A_D = 
		!(in_array(2, $allQuads) || in_array(3, $allQuads)) && $A_SET && $D_SET && !($parseOutput['0001'] != $parseOutput['0004']) ;
		$B_C = 
		!(in_array(1, $allQuads) || in_array(4, $allQuads)) && $C_SET && $B_SET && !($parseOutput['0003'] != $parseOutput['0002']) ;

		$isDiagonal_1_3 = in_array('0001', $sensors) &&  in_array('0004', $sensors) && $A_D;
		$isDiagonal_2_4 = in_array('0002', $sensors) &&  in_array('0003', $sensors) && $B_C; 

		$isValidCorner = (!empty($sensors) && count($sensors) == 2 ) && 	($isDiagonal_1_3 || $isDiagonal_2_4);

		$parseOutput['type'] = 'corner';
  	return $isValidCorner 	? $parseOutput : false;
  }

  public static function filterQuads($groupsInfo)
  {	
		// Simula subcuadrantes activos
  	// $fakeQuads = [
  	// 	'0001' => [],
  	// 	'0002' => [2],
  	// 	'0003' => [2],
  	// 	'0004' => [],
  	// ];
  	// self::$quads = $fakeQuads;
  	// return self::getInstance();

  	$justQuads = [];
  	foreach ($groupsInfo as $sensor => $props) {
  		$justQuads[$sensor] = (array_key_exists('active_sub_quadrants', $props)) ? $props['active_sub_quadrants'] : [];
  	}
			
		self::$quads = $justQuads;
		return self::getInstance();
  	// return $justQuads;
  }



	/**
	 * Determines if inline / COL
	 *
	 * @param      <type>  $subQuads  The sub quads
	 *
	 * @return     array   True if inline, False otherwise.
	 */
	public static function isInline($sensors, $pair)
	{
			return 
				count($sensors) == 2 && 
				count($pair) == 2 && 
				array_intersect($pair[0], $pair[1]) == $pair[0] && 
				!(in_array('0001', $sensors) && in_array('0004', $sensors)) &&
				!(in_array('0002', $sensors) && in_array('0003', $sensors));
	}



  /**
   * Detecta un solo evento en el system
   *
   * @return     <type>  ( description_of_the_return_value )
   */
  public static function detectOneEvent()
  {
  	$counter = 0;
  	$quads = [];
  	foreach (self::$quads as $sensor => $subQuads) {
  		if (!empty($subQuads)) { // Destecta si hay o no subcuadrantes activos
  			$counter++;
  			$quads[$sensor] = $subQuads;
  		}
  	}
  	$quads['type'] = 'one';
  	return  ($counter !=  1) ? false : $quads;
  }


  
}
