<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayPalSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_pal_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('plan_id')->unsigned();
            $table->string('name');
            $table->text('paypal_data')->nullable();
            $table->date('ends_at')->nullable();
            $table->string('paypal_status')->nullable();
            
            $table->integer('amount');
            $table->string('paypal_id');

            $table->bigInteger('coupon_id')->unsigned()->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('Set null');

            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plans');

            $table->longText('proxies_history')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_pal_subscriptions');
    }
}
