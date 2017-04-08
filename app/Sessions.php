<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
    protected $fillable = ['user_id', 'token', 'created_at', 'updated_at'];

    protected $dates = [];

    public $timestamps = true;

    public static $rules = [
    'user_id' => 'required', 'token' => 'required'
    ];

	// Relationships

}
