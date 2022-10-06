<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTakeyemStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('takeyem_students', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('student_id')->nullable()->unsigned();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');

            $table->integer('takeyem_id')->nullable()->unsigned();
            $table->foreign('takeyem_id')->references('id')->on('takeyems')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('takeyem_students');
    }
}
