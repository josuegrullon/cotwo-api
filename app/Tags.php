<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    protected $fillable = ['id', 'name', 'description', 'differer'];
    public $timestamps = false;
    protected $table = 'tags';

    public static $rules = [
      'name' => 'required',
      'description' => 'required',
    ];
}
