<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class MSets extends Model
{
    protected $fillable = ['id', 'm_id', 'set_id'];
    public $timestamps = false;
    protected $table = 'm_sets';

    public static $rules = [
    	'm_id' => 'required|exists:measurements,id',
    	'set_id' => 'required|exists:sets,id'
    ];

    public function measurement()
    {
    	return $this->belongsTo('App\Measurements', 'm_id', 'id');
    }

    public function set()
    {
    	return $this->belongsTo('App\Sets', 'set_id', 'id');
    }
}
