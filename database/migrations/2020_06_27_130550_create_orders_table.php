<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('unique_id');
            $table->enum('status', ['ORDER PLACED', 'ORDER ACCEPTED', 'ORDER PREPARED', 'On the Way', 'Delivered']);
            $table->string('user_address');

            $table->decimal('user_lat', 11, 8);
            $table->decimal('user_long', 11, 8);

            $table->string('payment_mode');

            $table->decimal('rating', 2, 1)->nullable();

            $table->decimal('restaurant_charges')->default('0');
            $table->decimal('tax')->default('0');
            $table->decimal('delivery_charge')->default('0');

            $table->decimal('coupon_discount')->default('0');
            $table->decimal('tip')->default('0');
            $table->decimal('total_charges')->default('0');

            $table->decimal('total');

            $table->timestamps();

            $table->foreignId('restaurant_id')->constrained();
            $table->foreignId('coupon_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}