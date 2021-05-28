<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('coupon_code')->unique();
            $table->enum('discount_type', ['PERCENTAGE', 'FIXED']);
            $table->integer('discount');
            $table->decimal('price');
            $table->date('expiry_date');
            $table->boolean('max_usage')->nullable();
            $table->boolean('active');
            $table->timestamps();

            $table->foreignId('restaurant_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}