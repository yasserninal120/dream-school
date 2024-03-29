<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToSchoolPaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_pays', function (Blueprint $table) {
            $table->integer("user_id")->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null') ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_pays', function (Blueprint $table) {
            //
        });
    }
}
