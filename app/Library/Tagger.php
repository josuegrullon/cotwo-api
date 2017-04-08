<?php namespace App\library;

use App\MeasurementTags;
use App\Library\Helpers;

class Tagger {

    const MAX_VELOCITY = 45;

    public static function apply($package) 
    {
    	return self::tag($package->id, $package);
    }	

    protected static function tag($mId, $package)
    {
        self::velocity($mId, $package);
        self::range($mId, $package);
    }

    protected static function range($mId, $package)
    {	
        $sensorsPackage = Helpers::getSensorsInfo($package);
    	$ppms = 0;
    	foreach ($sensorsPackage as $key => $value) {
    		$ppms += $value->ppm;
    	}
        
    	$ppmAvg = $ppms/count($sensorsPackage);
    	
        $taggIt = function ($tagId) use($mId) {
            MeasurementTags::create([
                'tag_id'=> $tagId,
                'measurement_id' =>$mId
            ]);
        };
    	if ($ppmAvg > 0 && $ppmAvg <= 25) {
    		$taggIt(1);
    	} elseif ($ppmAvg > 25 && $ppmAvg <= 50) {
    		$taggIt(2);
    	} elseif ($ppmAvg > 50 && $ppmAvg <= 75) {
    		$taggIt(3);
    	} elseif ($ppmAvg > 75 && $ppmAvg <= 100) {
    		$taggIt(4);
    	}
    	return $ppmAvg;
    }

    protected static function velocity($mId, $package)
    {   
        $sensorsPackage = Helpers::getWindInfo($package);

        $velocity = 0;
        foreach ($sensorsPackage as $key => $value) {
            $velocity += $value->velocity;
        }

        $velAvg = $velocity/count($sensorsPackage);

        $taggIt = function ($tagId) use($mId) {
            MeasurementTags::create([
                'tag_id'=> $tagId,
                'measurement_id' =>$mId
            ]);
        };

        if ($velAvg == 0) {
            $taggIt(5);
        } elseif ($velAvg > 0 && $velAvg  <= (self::MAX_VELOCITY * 0.25)) {
            $taggIt(9);
        } elseif ($velAvg > (self::MAX_VELOCITY * 0.25) && $velAvg  <= (self::MAX_VELOCITY * 0.50)) {
            $taggIt(6);
        } elseif ($velAvg > (self::MAX_VELOCITY * 0.50) && $velAvg  <= (self::MAX_VELOCITY * 0.75)) {
            $taggIt(8);
        } elseif ($velAvg > (self::MAX_VELOCITY * 0.75) && $velAvg  <= (self::MAX_VELOCITY)) {
            $taggIt(7);
        }
        return $velAvg;
    }


    protected static function wind($sensorsPackage)
    {
    	return $sensorsPackage;
    }

    protected static function actives($sensorsPackage)
    {
    	return $sensorsPackage;
    }


}

?>