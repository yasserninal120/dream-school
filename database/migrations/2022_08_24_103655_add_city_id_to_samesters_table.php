<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityIdToSamestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('samesters', function (Blueprint $table) {
            $table->integer("city_id")->unsigned()->default(1)->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null') ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('samesters', function (Blueprint $table) {
            //
        });
    }
}
