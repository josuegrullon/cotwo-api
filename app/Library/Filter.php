<?php namespace App\library;

use App\Measurements;
use App\CollectorsController;
use App\Collectors;
use App\CollectorsBuffer;
use App\Library\MathModel;
use App\Library\Helpers;

class Filter {

    public static function bufferCollectors($request)
    {

        if (!array_key_exists('ppm', $request)) {
            print_r("ppm does not exists problem\n");
            return false;
        }
        if ( $request['ppm'] > 10000) {
           print_r("ppm over 10000\n");
            return false;
        }

        if ( $request['w_dir'] === '0')  {
           print_r("w_dir === 0 problem\n");
            return false;
        }

        if ( $request['ppm'] < 100) {
            print_r("ppm under 100\n");
            return false;
        }

       
      $guzzle = new \GuzzleHttp\Client();
      $url = 'http://api.openweathermap.org/data/2.5/weather?q=DominicanRepublic,SantoDomingo&appid=0f0fd0473ef1e111d3fdda0d195dcca2';

      $response = json_decode((string) $guzzle->get($url)->getBody());

      $col = Collectors::create([
          'ppm' => $request['ppm'],
          'dir' => strtolower($request['w_dir']),
          'identifier' => $request['id'],
          'velocity' => $request['w_vel'],
          'humidity' => $response->main->humidity, 
          'temperature' => $response->main->temp, 
          'presure' => $response->main->pressure
      ]);
      

        // $ppm = round(($col->ppm / 10000 ) *  100) ;
        $ppm = (int)$col->ppm ;
       
        if ($ppm > 1600) {
           $dist = MathModel::getAproxDistance($ppm, (int)$col->velocity);
            foreach (\App\News::all() as $key => $value) {
              Helpers::sendMail("COTWO NOTIFICATION", $value->email, " Level of: {$ppm}ppm in {$col->identifier} Direction: {$col->dir}, Aprox. Distance: {$dist}");
            }
        } 
        $addMissingWinds = function ($info) {
          $all['wind_info'] = [
            [ 'identifier' => '123456A', 'velocity' => 0, 'unit' => 'm/s', 'direction'=> 0],
            [ 'identifier' => '123456B', 'velocity' => 0, 'unit' => 'm/s', 'direction'=> 0],
            [ 'identifier' => '123456C', 'velocity' => 0, 'unit' => 'm/s', 'direction'=> 0],
            [ 'identifier' => '123456D', 'velocity' => 0, 'unit' => 'm/s', 'direction'=> 0],
          ];
          foreach ($all['wind_info'] as $key => $value) {
              if ($value['identifier'] == Helpers::getMyWind($info->identifier) ) {
                $all['wind_info'][$key] = [
                 'identifier' => Helpers::getMyWind($info->identifier), 
                 'velocity' => (int)$info->velocity, 
                 'unit' => 'm/s',
                 'direction'=> strtolower(trim($info->dir))
                ];

              }
          }
          return $all;
        };
      
        $addMissingCo2 = function ($info) {
          $all['sensors_info'] = [
              [ 'identifier' => '0001', 'ppm' => 0],
              [ 'identifier' => '0002', 'ppm' => 0],
              [ 'identifier' => '0003', 'ppm' => 0],
              [ 'identifier' => '0004', 'ppm' => 0],
          ];
          foreach ($all['sensors_info'] as $key => $value) {
              if ($value['identifier'] == $info->identifier ) {
                // $ppm = round(($info->ppm / 10000 ) *  100) ;
                $ppm = (int)$info->ppm ;
                $all['sensors_info'][$key] = [ 'identifier' => $info->identifier, 'ppm' => $ppm];
              }
          }
          return $all;
        };
  
    
        $parse = [
          'wind_info' => json_encode($addMissingWinds($col)),
          'sensors_info' => json_encode($addMissingCo2($col))
        ];

        return ($parse);    
    }

    /**
     * Filter package
     *
     * @param      array  $package  The package
     */
    public static function filterPackage($package)
    {
    	return self::filterUnderZero($package);
    }

    protected static function filterUnderZero($package) 
    {
    	$sensorsInfo = json_decode($package['sensors_info']);
    	$isEvent = 0;
    	foreach ($sensorsInfo as $key => $value) {
    		
    		foreach ($value as $key => $sensor) {
    			if ($sensor->ppm > 0) {
    				$isEvent++;
    			}

    		}
    	}

    	return $isEvent > 0 ? $package: false;
    }
}

?>