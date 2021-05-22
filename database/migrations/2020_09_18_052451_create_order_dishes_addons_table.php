<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDishesAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_dishes_addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price');
            
            $table->foreignId('order_dishes_id')->constrained();
            $table->foreignId('dish_id')->constrained();
            $table->foreignId('order_id')->constrained();

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
        Schema::dropIfExists('order_dishes_addons');
    }
}
