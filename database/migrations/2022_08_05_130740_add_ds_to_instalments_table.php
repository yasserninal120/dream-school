<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDsToInstalmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instalments', function (Blueprint $table) {
            $table->integer('discointUsdOrPersent')->default(0)->change();
            $table->integer('persent')->default(2)->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instalments', function (Blueprint $table) {
            //
        });
    }
}
