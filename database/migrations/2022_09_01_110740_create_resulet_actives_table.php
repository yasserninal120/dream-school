<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResuletActivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resulet_actives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('marek');

            $table->integer('student_id')->unsigned();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('object_class_id')->unsigned();
            $table->foreign('object_class_id')->references('id')->on('object_classes')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('activety_obg_id')->unsigned();
            $table->foreign('activety_obg_id')->references('id')->on('activety_obgs')->onDelete('cascade')->onUpdate('cascade');


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
        Schema::dropIfExists('resulet_actives');
    }
}
