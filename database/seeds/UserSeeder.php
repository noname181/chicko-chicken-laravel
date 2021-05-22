<?php

use Illuminate\Database\Seeder;

use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '+123456890',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Restaurant Owner',
            'email' => 'owner@example.com',
            'phone' => '+123456891',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
        
        DB::table('users')->insert([
            'name' => 'Delivery Scout',
            'email' => 'delivery@example.com',
            'phone' => '+123456892',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
        
        DB::table('users')->insert([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'phone' => '+123456893',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

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
