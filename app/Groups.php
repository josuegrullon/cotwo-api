<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    protected $fillable = ['id', 'is_isolated', 'ttl', 'sensor_identifier'];
    public $timestamps = false;
    protected $table = 'groups';

    public static $rules = [
      'type_id' => 'required|exists:group_types,id'
    ];

}
