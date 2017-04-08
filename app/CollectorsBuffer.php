<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectorsBuffer extends Model
{
    protected $fillable = ['id', 'identifier'];
    public $timestamps = false;
    protected $table = 'collectors_buffer';

    public static $rules = [
      'type_id' => 'required|exists:group_types,id'
    ];

}
