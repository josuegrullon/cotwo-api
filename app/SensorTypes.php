<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SensorTypes extends Model
{
    protected $fillable = ['id', 'name', 'description'];
    public $timestamps = false;
    protected $table = 'sensor_types';

    public static $rules = [
      'name' => 'required', 'description' => 'required'
    ];
}
