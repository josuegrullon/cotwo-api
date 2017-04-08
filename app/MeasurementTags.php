<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class MeasurementTags extends Model
{
    protected $fillable = ['id', 'tag_id', 'measurement_id'];
    public $timestamps = false;
    protected $table = 'measurement_tags';

    public static $rules = [
      'tag_id' => 'required|exists:tags,id',
      'measurement_id' => 'required|exists:measurements,id',
    ];

    public function tag()
    {
    	return $this->belongsTo('App\Tags');
    }

}

