<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestProxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_proxies', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('proxy_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->foreign('proxy_id')->references('id')->on('proxies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->dateTime('ends_at');

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
        Schema::dropIfExists('test_proxies');
    }
}
