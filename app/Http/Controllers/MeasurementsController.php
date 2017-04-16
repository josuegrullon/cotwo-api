<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Library\Filter;
use App\Library\Tagger;
use App\Library\Buffer;
use App\Library\Helpers;
use App\Library\WindPolices;
use App\Library\Polices;
use App\Library\Sets;
use App\Library\Groups;
use App\Library\Quadrants;
use App\Library\SourcePolices;
use App\Library\SourceFinder;
use App\Library\Reports;
use Illuminate\Support\Facades\Mail;

class MeasurementsController extends Controller
{
    const MODEL = "\App\Measurements";
    const Sensors = "\App\Sensors";
    use RESTActions;

   	public function getRegion()
   	{
      ob_start();
exec('top');
$output = ob_get_clean();
ob_clean();
// $cpu = preg_split('/[\s]+/', shell_exec('mpstat 1 1'));
// $cpu = 100-$cpu[42];
// print_r(shell_exec('mpstat 1 1'));die();
   		return $this->respond(Response::HTTP_OK, Quadrants::getRegion());
   	}

   	public function getSourcePoints()
   	{
   		return $this->respond(Response::HTTP_OK, SourcePolices::getSourceLocation());
   	}
   	

   	public function getSensors()
    {
      return $this->respond(Response::HTTP_OK, Quadrants::getSensorsLocations());
    }

    public function datatable()
   	{
   		return $this->respond(Response::HTTP_OK, Reports::getMeasurements());
   	}

    public function datatableByID($id)
    {
      return $this->respond(Response::HTTP_OK, Reports::getMeasurementsByID($id));
    }
     /**
     * Groups information
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function groupsInformation()
    {	

    //   $in = json_decode(json_encode([
    //     'identifier' => '0004',
    //     'ppm' => '1500',
    //     'velocity' => '10',
    //     'dir' => 'o',
    //   ]));



    // $addMissingWinds = function ($info) {
    //   $all['wind_info'] = [
    //     [ 'identifier' => '123456A', 'velocity' => 0, 'unit' => 'm/s', 'direction'=> 0],
    //     [ 'identifier' => '123456B', 'velocity' => 0, 'unit' => 'm/s', 'direction'=> 0],
    //     [ 'identifier' => '123456C', 'velocity' => 0, 'unit' => 'm/s', 'direction'=> 0],
    //     [ 'identifier' => '123456D', 'velocity' => 0, 'unit' => 'm/s', 'direction'=> 0],
    //   ];
    //   unset($all['wind_info'][Helpers::getMyWind($info->identifier)]);
    //   $all['wind_info'][] = [ 
    //     'identifier' => Helpers::getMyWind($info->identifier), 
    //     'velocity' => $info->velocity, 
    //     'unit' => 'm/s', 
    //     'direction'=> $info->dir
    //     ];
    //   return $all;
    // };


    // $addMissingCo2 = function ($info) {
    //   $all['sensors_info'] = [
    //       [ 'identifier' => '0001', 'ppm' => 0],
    //       [ 'identifier' => '0002', 'ppm' => 0],
    //       [ 'identifier' => '0003', 'ppm' => 0],
    //       [ 'identifier' => '0004', 'ppm' => 0],
    //   ];
    //   unset($all['sensors_info'][$info->identifier]);
    //   $all['sensors_info'][] = [ 'identifier' => $info->identifier, 'ppm' => $info->ppm];
    //   return $all;
    // };

    // $parse = [
    //   'wind_info' => json_encode($addMissingWinds($in)),
    //   'sensors_info' => json_encode($addMissingCo2($in))
    // ];

    // return $parse;
    //   // print_r(Helpers::getMyWind('0002'));

    //   die();
      // $guzzle = new \GuzzleHttp\Client();
      // $url = 'http://api.openweathermap.org/data/2.5/weather?q=DominicanRepublic,SantoDomingo&appid=0f0fd0473ef1e111d3fdda0d195dcca2';

      // $response = json_decode((string) $guzzle->get($url)->getBody());

      //   print_r([$response->main->humidity, $response->main->temp, $response->main->pressure]);
      //   die();
		return $this->respond(Response::HTTP_OK, SourceFinder::getGroupsInfo());
    }

    // public static function sendMail($subject, $to, $message){
    //   $url = 'http://api.fide.com/v1/mailer';
    //   $response = json_decode((string)  (new  \GuzzleHttp\Client())->post($url, ['form_params' => [
    //     'subject' => $subject,
    //     'to' => $to,
    //     'message' => $message
    //     ]])->getBody());
    // }

    public function testsubs() {
      foreach (\App\News::all() as $key => $value) {
                 Helpers::sendMail("COTWO NOTIFICATION",   $value->email, " Level of: 10000 ppm in 0001 from NO");
            }
    }
    public function saveSubscriptor(Request $request)
    {
      if(filter_var($request->all()['email'], FILTER_VALIDATE_EMAIL)) {

        if (\App\News::where('email', $request->all()['email'])->exists()) {
           return $this->respond(Response::HTTP_CREATED, ['message' => 'Email already subscribed']);
        }
        Helpers::sendMail("COTWO SUBSCRIPTION",  $request->all()['email'], "You are successfully subscribed");
        return $this->respond(Response::HTTP_CREATED, \App\News::create([
          'email' => $request->all()['email'],
          'status' => 0
        ]));
      } else {
        return $this->respond(Response::HTTP_CREATED, ['message' => 'Invalid Email']);
      }
    }

    public function saveData(Request $request)
    {
        $data = Filter::bufferCollectors($request->all());
        $m = self::MODEL;
   
        if ($data == false) {
          return $this->respond(Response::HTTP_CREATED, false);
        }

      
        $filter = Filter::filterPackage($data);

        if ($filter != false) {
        	$package = $m::create($filter);
        	// Apply tag
        	Tagger::apply($package);
        	Buffer::populateSets($package);
        	Groups::isolate();
        	return $this->respond(Response::HTTP_CREATED, $package);

        } else {
        	return $this->respond(Response::HTTP_CREATED, false);
        }
    }

    /**
     * Buffer
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function buffer()
    {	
		  return $this->respond(Response::HTTP_OK, Polices::getGroupsInfo());
    }
   

    public function test_wifi()
    {	
    	// $ok = null;
    	// if (array_key_exists('m', $_GET)) {
    	// 	$ok = $_GET['m'];
    	// 	\App\Users::create([ 'name'=> 'value:__'.$ok]);
    	// }
    	// \App\Users::create([ 'name'=> 'guillermo_'.uniqid()])
		return $this->respond(Response::HTTP_OK, $ok);
    }


    public function reset_database()
    {	
  		return $this->respond(Response::HTTP_OK, [
  			'groups history count' => \App\GroupsHistory::truncate()->count(),
  			'measurements count' => \App\Measurements::truncate()->count(),
  			'measurement Tags count' => \App\MeasurementTags::truncate()->count(),
  			'M sets count' => \App\MSets::truncate()->count(),
  			'Sets count' => \App\Sets::truncate()->count(),
        'Sgroups count' => \App\SetGroups::truncate()->count(),
  			'collectors' => \App\Collectors::truncate()->count(),
  			'groups reset, ttl: 4000000, is isolated: 0' => \App\Groups::whereRaw('1')->update([
  				'ttl' => 4000000,
  				'is_isolated' => 0
  			])
  		]);
    }

}
