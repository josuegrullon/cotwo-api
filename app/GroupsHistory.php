<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupsHistory extends Model
{
    protected $fillable = ['id', 'group_id', 'ttl', 'weight', 'length', 'avg', 'is_isolated','created_at', 'updated_at','package_identifier','wind_velocity_avg', 'wind_directions_trends'];
    public $timestamps = true;
    protected $table = 'groups_history';

    public function group()
    {
    	return $this->belongsTo('App\Groups', 'group_id', 'id');
    }

    public function sets()
    {
    	return $this->hasMany('App\SetGroups', 'group_id', 'id');
    }

}
