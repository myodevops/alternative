<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('altesqlite')->create('laravellogs', function (Blueprint $table) {
            $table->id();
		    $table->dateTime ('datetime');
            $table->string('message', 250);
            $table->text('stacktrace');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('altesqlite')->dropIfExists('laravellogs');
    }
}
