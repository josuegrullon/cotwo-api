<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['id', 'email', 'status'];
    public $timestamps = false;
    protected $table = 'news';

    public static $rules = [
      'email' => 'required'
    ];

}
