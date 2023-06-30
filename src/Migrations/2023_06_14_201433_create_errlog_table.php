<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('altesqlite')->create('errlog', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->bigInteger('userid');
            $table->enum('type', ['warning', 'error', 'fatal', 'debug']);
            $table->string('info', 250);
            $table->timestamp('datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('altesqlite')->dropIfExists('errlog');
    }
}