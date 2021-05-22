<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('description');
            $table->string('image');
            $table->string('phone');
            $table->string('email');
            $table->decimal('rating',2,1);
            $table->smallInteger('delivery_time');
            $table->smallInteger('for_two');
            // $table->string('address');
            // $table->string('landmark')->nullable();
            // $table->decimal('lat',11,8);
            // $table->decimal('long',11,8);
            $table->decimal('commission_rate');
            $table->string('license_code');
            $table->decimal('restaurant_charges');
            $table->smallInteger('delivery_radius');
            $table->boolean('is_veg');
            $table->boolean('featured');
            $table->boolean('active');
            $table->timestamps();

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
        Schema::dropIfExists('restaurants');
    }
}
