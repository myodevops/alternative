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
        Schema::connection('altesqlite')->create('errorlogs', function (Blueprint $table) {
            $table->id();
            $table->text('message');
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
        Schema::connection('altesqlite')->dropIfExists('errorlogs');
    }
}
