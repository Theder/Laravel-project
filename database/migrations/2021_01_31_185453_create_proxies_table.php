<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxies', function (Blueprint $table) {
            $table->id();
            $table->string('ip_port');
            $table->string('login');
            $table->string('password');
            $table->string('type')->nullable();
            $table->integer('rotation_time')->nullable();
            $table->bigInteger('subscription_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('check_status')->nullable();
            $table->string('latency')->nullable();
            $table->boolean('is_action_required')->default(false);
            $table->timestamps();

            $table->string('http_port')->nullable();
            $table->string('socks_port')->nullable();
            $table->string('real_ip')->nullable();

            $table->boolean('is_trial')->default(false);
            $table->dateTime('trial_ends_at')->nullable();

            $table->foreign('subscription_id')->references('id')->on('pay_pal_subscriptions')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proxies');
    }
}
