<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectorsTable extends Migration
{
    
    public function up()
    {
        Schema::create('collectors', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->string('identifier');
            $table->string('location');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('collectors');
    }
}
