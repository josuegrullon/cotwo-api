<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Collectors extends Model
{
    protected $fillable = ['id', 'ppm', 'dir', 'velocity', 'identifier', 'temperature', 'humidity', 'presure'];
    public $timestamps = true;
    protected $table = 'collectors';

    public static $rules = [
      'type_id' => 'required|exists:group_types,id'
    ];
}
