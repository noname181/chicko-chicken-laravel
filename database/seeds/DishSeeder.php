<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Dish;
use App\Addon;
use App\AddonDish;
use App\AddonsCategory;

class DishSeeder extends Seeder
{
    protected $categories = [
        'French', 'Italian', 'American', 'Mexican', 'Chinese', 
        'Indian', 'Mediterranean', 'Russian'
    ];

    protected $dishNames = [
        'Cheese Pizza', 'Hamburger', 'Cheeseburger', 'Bacon Burger', 'Bacon Cheeseburger',
        'Little Hamburger', 'Little Cheeseburger', 'Little Bacon Burger', 'Little Bacon Cheeseburger',
        'Veggie Sandwich', 'Cheese Veggie Sandwich', 'Grilled Cheese',
        'Cheese Dog', 'Bacon Dog', 'Bacon Cheese Dog', 'Pasta'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dish_categories=[];
        $faker = Faker\Factory::create();
        for ($i=1; $i <= 8; $i++) {
            $dish_categories[] = array(
                'name' => $this->categories[$i-1],
                'image' => "demo/".$i.".jpg",
                "active" => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            );
        }
        DB::table('dish_categories')->insert($dish_categories);

        $addons_categories = array( 
            array(
                'name' => "Crust",
                'type' => "SINGLE",
                "user_id" => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Size",
                'type' => "SINGLE",
                "user_id" => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Fillings",
                'type' => "MULTIPLE",
                "user_id" => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
         );

        DB::table('addons_categories')->insert($addons_categories);

        $addons = array( 
            array(
                'name' => "Cheese Burst",
                'price' => 12.00,
                'addons_category_id' => 1,
                "user_id" => 1,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Fresh Pan",
                'price' => 10.00,
                'addons_category_id' => 1,
                "user_id" => 1,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Classic Hand Tossed",
                'price' => 8.00,
                'addons_category_id' => 1,
                "user_id" => 1,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Small",
                'price' => 5.00,
                'addons_category_id' => 2,
                "user_id" => 1,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Medium",
                'price' => 8.00,
                'addons_category_id' => 2,
                "user_id" => 1,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Large",
                'price' => 12.00,
                'addons_category_id' => 2,
                "user_id" => 1,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Nutella Filling",
                'price' => 4.00,
                'addons_category_id' => 3,
                "user_id" => 1,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Peanut Butter Filling",
                'price' => 5.00,
                'addons_category_id' => 3,
                "user_id" => 1,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => "Dark Chocolate",
                'price' => 3.00,
                'addons_category_id' => 3,
                "user_id" => 1,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
         );

        DB::table('addons')->insert($addons);
        

        for ($i = 1; $i < 15; $i++) {
            for ($j = 1; $j <= 15; $j++) {
                $dish = Dish::create([
                    'name' => $this->dishNames[$j-1],
                    'description' => $faker->sentence,
                    'image' => "demo/" . $j . ".jpg",
                    'price' => mt_rand(8, 25),
                    'discount_price' => mt_rand(8, 25),
                    'is_veg' => rand(0, 1),
                    'featured' => rand(0, 1),
                    'active' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'restaurant_id' => $i,
                    'dish_category_id' => mt_rand(1, 8),
                ]);
                
                if($j < 4){
                    AddonDish::create([
                        'addons_category_id' => mt_rand(1, 2),
                        'dish_id' => $dish->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    AddonDish::create([
                        'addons_category_id' => 3,
                        'dish_id' => $dish->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }
    }
}
