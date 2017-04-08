<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Sets extends Model
{
    protected $fillable = ['id', 'is_active','created_at','updated_at'];
    public $timestamps = true;
    protected $table = 'sets';

    public static $rules = [
      'is_active' => 'required'
    ];
}
