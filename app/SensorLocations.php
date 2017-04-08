<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SensorLocations extends Model
{
    protected $fillable = ['id', 'coordinates', 'sensor_id', 'region_id'];
    public $timestamps = false;
    protected $table = 'sensor_locations';

    public static $rules = [
      'sensor_id' => 'required|exists:sensors,id',
      'region_id' => 'required|exists:regions,id', 
      'coordinates' => 'required|isValidRegionJson'
    ];

    public function sensor() 
    {
    	return $this->belongsTo('App\Sensors');
    }

    public function region() 
    {
    	return $this->belongsTo('App\Regions');
    }
}
