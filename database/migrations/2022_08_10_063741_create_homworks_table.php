<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homworks', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name_object')->nullable();
            $table->text('contain_homwork',40000)->nullable();
            $table->integer('semester_id')->unsigned();
            $table->foreign('semester_id')->references('id')->on('samesters')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('homworks');
    }
}
