<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('naem');
            $table->integer("samester_id")->unsigned()->default(1)->nullable();
            $table->foreign('samester_id')->references('id')->on('samesters')->onDelete('set null') ;
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
        Schema::dropIfExists('object_classes');
    }
}
