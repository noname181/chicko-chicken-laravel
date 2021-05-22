<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->smallInteger('count_r');
            $table->decimal('latitude', 20, 17)->nullable();
            $table->decimal('longitude', 20, 17)->nullable();
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
        Schema::dropIfExists('city_restaurants');
    }
}