<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivetyObgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activety_obgs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('object_class_id')->unsigned();
            $table->foreign('object_class_id')->references('id')->on('object_classes')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('activety_obgs');
    }
}
