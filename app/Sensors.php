<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Sensors extends Model
{
    protected $fillable = ['id', 'name', 'type_id', 'identifier'];
    public $timestamps = false;
    protected $table = 'sensors';

    public static $rules = [
      'type_id' => 'required|exists:sensor_types,id', 'name' => 'required', 'identifier' => 'required'
    ];

    public function location()
    {
    	return $this->hasMany('App\SensorLocations', 'sensor_id', 'id');
    }
}
