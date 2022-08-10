<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstalmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instalments', function (Blueprint $table) {
            $table->increments('id')->nullable()->unsigned();
            $table->integer('student_id')->unsigned()->unique();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('discointUsdOrPersent')->nullable();
            $table->integer('instalment');
            $table->integer('transport')->nullable();
            $table->integer('persent')->nullable();
            $table->integer('instaAfterPearsent');
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
        Schema::dropIfExists('discount');
    }
}
