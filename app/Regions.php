<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class Regions extends Model
{
    protected $fillable = ['id', 'name', 'coordinates'];
    public $timestamps = false;
    protected $table = 'regions';

    public static $rules = [
      'name' => 'required', 'coordinates' => 'required|isValidRegionJson'
    ];
}
