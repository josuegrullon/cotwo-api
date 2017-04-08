<?php namespace App;
use Illuminate\Database\Eloquent\Model;
class Users extends Model {
    protected $table = 'users';
    protected $fillable = [
        'name',
        'username',
        'password'
    ];
    protected $hidden = ['password'];
    public static $rules = [
		 	'name' => 'required', 'username' => 'unique|required', 'password' => 'required'
    ];

    public $timestamps = true;
}