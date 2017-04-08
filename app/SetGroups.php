<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SetGroups extends Model
{
    protected $fillable = ['id', 'group_id', 'set_id', 'history_id'];
    public $timestamps = false;
    protected $table = 's_groups';

    public static $rules = [
     'group_id' => 'required|exists:groups,id',
     'history_id' => 'required|exists:groups_history,id',
     'set_id' => 'required|exists:sets,id'
    ];

    public function group()
    {
    	return $this->belongsTo('App\Groups', 'group_id', 'id');
    }

    public function set()
    {
    	return $this->belongsTo('App\Sets', 'set_id', 'id');
    }

    public function history()
    {
    	return $this->belongsTo('App\GroupsHistory', 'history_id', 'id');
    }

}
