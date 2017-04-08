<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasurementsTable extends Migration
{
    
    public function up()
    {
        Schema::create('measurements', function(Blueprint $table) {
            $table->increments('id');
            // Schema declaration
            $table->integer('collector_id')->unsigned();
            $table->integer('ppm')->unsigned();
            $table->string('temperature');
            // Constraints declaration
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('measurements');
    }
}
