<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Collectors extends Model
{
    protected $fillable = ['id', 'ppm', 'dir', 'velocity', 'identifier'];
    public $timestamps = false;
    protected $table = 'collectors';

    public static $rules = [
      'type_id' => 'required|exists:group_types,id'
    ];

}
