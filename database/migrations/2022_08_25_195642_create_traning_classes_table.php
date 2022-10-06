<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraningClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traning_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer("city_id")->unsigned()->default(1)->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traning_classes');
    }
}
