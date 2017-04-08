<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Measurements extends Model
{
    protected $fillable = ['id', 'wind_info', 'sensors_info', 'created_at', 'updated_at'];
    public $timestamps = true;
    protected $table = 'measurements';

    public static $rules = [
      'wind_info' => 'required|isValidMeasureWindJson',
      'sensors_info' => 'required|isValidMeasureSensJson',
    ];

    public function tags()
    {
    	return $this->hasMany('App\MeasurementTags', 'measurement_id', 'id');
    }
}
 //    lt --local-host http://192.168.255.128 --port 8050
 // http://0eaea956.ngrok.io