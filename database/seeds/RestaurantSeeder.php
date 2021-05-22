<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Restaurant;

class RestaurantSeeder extends Seeder
{
    protected $restaurantNames = [
        'Atera', 'Barclay Prime', 'Barrique Venice', 'Cafe Provence', 'Double Knot',
        'Fishing Dynamite', 'Giulia Restaurant', 'Harold Black', 'Kinship',
        'Lahaina Grill', 'Marc Forgione', 'The Polo Bar',
        'Oriole', 'Riccardo Enoteca', 'Vetri Cucina'
    ];

    protected $deliveryTimings = [
        "15",
        "20",
        "30",
        "45",
        "60",
        "90",
    ];

    protected $forTwoPeople = [
        "75",
        "100",
        "150",
        "200",
        "250",
        "300",
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $restaurants;
        for ($i=1; $i <= 15; $i++) {
            $faker = Faker\Factory::create();
            $restaurants[] = array(
                'name' => $this->restaurantNames[$i-1],
                'description' => $faker->sentence,
                'image' => "demo/".$i.".jpg",
                'phone' => $faker->phoneNumber,
                'email' => $faker->email,
                "rating" => rand(40, 50)/10,
                "delivery_time" => $this->deliveryTimings[array_rand($this->deliveryTimings)],
                "for_two" => $this->forTwoPeople[array_rand($this->forTwoPeople)],
                "commission_rate" => rand(15, 50),
                'license_code' => $faker->swiftBicNumber,
                "restaurant_charges" => rand(0, 1),
                "delivery_radius" => rand(5, 99),
                "featured" => rand(0, 1),
                "is_veg" => rand(0, 1),
                "active" => 1,
                'user_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            );
        }
        DB::table('restaurants')->insert($restaurants);

        for ($i=1; $i <= 15; $i++) {
            $faker = Faker\Factory::create();
            $restaurant = Restaurant::find($i);
            $restaurant->addresses()->create([
                'label' => 'Restaurant Address',
                'given_name' => $restaurant->name,
                'family_name' => $restaurant->name,
                'street' => $faker->streetAddress,
                'city' => $faker->city,
                'postal_code' => $faker->postcode,
                "latitude" => "40.693505",
                "longitude" => "-73.854745",
                'is_primary' => true,
                'is_billing' => true,
                'is_shipping' => true,
            ]);
            $restaurant->save();
        }
    }
}
