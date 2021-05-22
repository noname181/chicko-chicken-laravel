<?php

use Illuminate\Database\Seeder;

use App\User;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::find(4);
        $user->addresses()->delete();

        for ($i=1; $i <= 4; $i++) {
            $faker = Faker\Factory::create();
            $user = User::find(4);
            $user->addresses()->create([
                'label' => $faker->lastName." - ".$faker->cityPrefix,
                'given_name' => $user->name,
                'family_name' => $user->name,
                'street' => $faker->streetAddress,
                'city' => $faker->city,
                'postal_code' => $faker->postcode,
                "latitude" => "40.693505",
                "longitude" => "-73.854745",
                'is_primary' => ($i == 2) ? true : false,
                'is_billing' => ($i == 2) ? true : false,
                'is_shipping' => ($i == 2) ? true : false,
            ]);
            $user->save();
        }
    }
}
