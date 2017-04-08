<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasurementsTable extends Migration
{
    
    public function up()
    {
        Schema::table('measurements', function(Blueprint $table) {
            $table->string('location');
        });
    }

    public function down()
    {
        Schema::drop('measurements');
    }
}
