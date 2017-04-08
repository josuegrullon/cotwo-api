<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectorsController extends Model
{
    protected $fillable = ['id', 'id_collector'];
    public $timestamps = false;
    protected $table = 'collectors_controller';

    public static $rules = [
      'type_id' => 'required|exists:group_types,id'
    ];

}
