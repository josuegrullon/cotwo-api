<?php namespace App\library;

use App\MeasurementTags;
use App\Measurements;

class Helpers {

    /**
     * Gets the sensors information.
     *
     * @param      <type>  $package  The package
     *
     * @return     <type>  The sensors information.
     */
    public static function getSensorsInfo($package) 
    {
    		return json_decode($package['sensors_info'])->sensors_info;
    }	

    /**
     * Gets the wind information.
     *
     * @param      <type>  $package  The package
     *
     * @return     <type>  The wind information.
     */
    public static function getWindInfo($package) 
    {
    		return json_decode($package['wind_info'])->wind_info;
    }	
		
		public static function getSensorsInfoAsArray($package) 
    {	
    		$all = [];
    		$sensor = json_decode($package['sensors_info'], 1)['sensors_info'];
    		foreach ($sensor as $key => $value) {
    	  	$all[$key] = $value;
    	  	$all[$key]['updated'] = (string)$package['created_at'];
    	  }
    		return $all;
    }	
		
		public static function getWindInfoAsArray($package) 
    {
    		$all = [];
    	  $sensor = json_decode($package['wind_info'], 1)['wind_info'];
    	  foreach ($sensor as $key => $value) {
    	  	$all[$key] = $value;
    	  	$all[$key]['updated'] = (string)$package['created_at'];
    	  }
    		return $all;
    }	


    /**
     * Last Info.
     *
     * @return     <type>  The wind information.
     */
    public static function getCurrentMeasurement() 
    {
    		return Measurements::orderBy('created_at', 'desc')->first();
    }	

    public static function getCurrentMeasurements($limit) 
    {
    		return Measurements::orderBy('created_at', 'desc')->limit($limit)->get();
    }	

	  /**
	   * Gets the my wind.
	   *
	   * @param      <type>  $cId    The c identifier
	   *
	   * @return     string  The my wind.
	   */
	  public static function getMyWind($cId)
		{
			switch ($cId) {
				case '0001':
					return '123456A';
					break;
				case '0002':
					return '123456B';
					break;
				case '0001':
					return '123456A';
					break;
				case '0003':
					return '123456C';
					break;
				case '0004':
					return '123456D';
					break;
				default:
					# code...
					break;
			}
		}
		/**
		 * Get opposite direction of wind
		 *
		 * @param      <type>  $direction  The direction
		 *
		 * @return     string  ( description_of_the_return_value )
		 */
		public static function oppDir($direction)
		{

		  //    N
		  // O--|--E
		  //    S
			switch ($direction) {
				case 'n':
					return 's';
					break;
				case 's':
					return 'n';
					break;
				case 'e':
					return 'o';
					break;
				case 'o':
					return 'e';
					break;
				case 'no':
					return 'se';
					break;
				case 'se':
					return 'no';
					break;
				case 'ne':
					return 'so';
					break;
				case 'so':
					return 'ne';
					break;
				default:
					return '--';
					break;
			}
		}


		
	/**
 * Calculate a new coordinate based on start, distance and bearing
 *
 * @param $start array - start coordinate as decimal lat/lon pair
 * @param $dist  float - distance in kilometers
 * @param $brng  float - bearing in degrees (compass direction)
 */
	public static function geoDestination($start, $dist, $brng){
	    $lat1 = self::toRad($start[0]);
	    $lon1 = self::toRad($start[1]);
	    $dist = $dist/6371.01; //Earth's radius in km
	    $brng = self::toRad($brng);
	 
	    $lat2 = asin( sin($lat1)*cos($dist) +
	                  cos($lat1)*sin($dist)*cos($brng) );
	    $lon2 = $lon1 + atan2(sin($brng)*sin($dist)*cos($lat1),
	                          cos($dist)-sin($lat1)*sin($lat2));
	    $lon2 = fmod(($lon2+3*pi()),(2*pi())) - pi();  
	 
	    return array(self::toDeg($lat2),self::toDeg($lon2));
	}

	public static function toRad($deg){
	    return $deg * pi() / 180;
	}
	
	public static function toDeg($rad){
	    return $rad * 180 / pi();
	}



	public static function sendMail($subject, $to, $message){
      $url = 'http://api.fide.com/v1/sendmail';
      $response = json_decode((string)  (new  \GuzzleHttp\Client())->post($url, ['form_params' => [
        'subject' => $subject,
        'to' => $to,
        'message' => $message
        ]])->getBody());
    }
}

?>