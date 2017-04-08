<?php

use Illuminate\Http\Response;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// HEADERS -------------------------------------------------------*
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
// header('Access-Control-Allow-Origin: *');
$app->get('/', function () use ($app) {
    return redirect('/v1/');;
});


$app->get('/v1/', function () use ($app) {
	   return (new Response(['message' => 'Welcome to COTWO API.'], 200));
});

// RESOURCES -------------------------------------------------------*

resource('/v1/users', 'Users');

resource('/v1/regions', 'Regions');

resource('/v1/sensors/types', 'SensorTypes');

resource('/v1/sensors/locations', 'SensorLocations');

resource('/v1/sensors', 'Sensors');

resource('/v1/tags', 'Tags');

resource('/v1/measurements/tags', 'MeasurementTags');

resource('/v1/measurements', 'Measurements');

// EXPLICIT -------------------------------------------------------*

$app->options('/v1/datatable', 'MeasurementsController@datatable');
$app->get('/v1/datatable', 'MeasurementsController@datatable');
$app->get('/v1/datatable/{id}', 'MeasurementsController@datatableByID');
$app->post('/v1/datatable', 'MeasurementsController@datatable');
$app->get('/v1/testsubscriptions', 'MeasurementsController@testsubs');

// EXPLICIT -------------------------------------------------------*

$app->get('/v1/buffer', 'MeasurementsController@buffer');
$app->get('/v1/movements', 'MeasurementsController@groupsInformation');
$app->get('/v1/regions', 'MeasurementsController@getRegion');
$app->get('/v1/sensors', 'MeasurementsController@getSensors');
$app->get('/v1/source', 'MeasurementsController@source');
$app->get('/v1/test-wifi', 'MeasurementsController@test_wifi');
//Reset all values of database
$app->get('/v1/reset-all-database-tables', 'MeasurementsController@reset_database');


$app->post('/v1/subscribe', 'MeasurementsController@saveSubscriptor');



$app->get('/v1/measurements', 'MeasurementsController@saveData');

// FUNCTIONS -------------------------------------------------------*
function resource($uri, $controller)
{
	global $app;

	$app->get($uri, $controller.'Controller@all');
	$app->get($uri.'/{id}', $controller.'Controller@show');
	
	// $app->get($uri.'/create', $controller.'Controller@create');
	$app->get($uri.'/{id}/edit', $controller.'Controller@edit');

	$app->post($uri, $controller.'Controller@store');
	$app->put($uri.'/{id}', $controller.'Controller@update');
	$app->patch($uri.'/{id}', $controller.'Controller@update');
	$app->delete($uri.'/{id}', $controller.'Controller@destroy');
}
