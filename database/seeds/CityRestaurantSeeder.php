<?php

use Illuminate\Database\Seeder;

class CityRestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('city_restaurants')->insert([
            'name' => 'Toronto',
            'latitude' => '43.68390313859002',
            'longitude' => '-79.35403396362992',
        ]);
        DB::table('city_restaurants')->insert([
            'name' => 'Québec',
            'latitude' => '46.81776612542938',
            'longitude' => '-71.20643142890924',
        ]);
        DB::table('city_restaurants')->insert([
            'name' => 'Ottawa',
            'latitude' => '45.546814149509416',
            'longitude' => '-75.7426115906398',
        ]);
        DB::table('city_restaurants')->insert([
            'name' => 'Charlottetown',
            'latitude' => '46.241997914047964',
            'longitude' => '-63.13005556728516',
        ]);
    }
}