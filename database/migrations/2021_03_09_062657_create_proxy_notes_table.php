<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxyNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxy_notes', function (Blueprint $table) {
            $table->id();
            $table->text('note');

            $table->bigInteger('proxy_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->foreign('proxy_id')->references('id')->on('proxies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('proxy_notes');
    }
}
