<?php

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $adminPermission = Permission::create(['name' => 'all.control']);
        $adminRole->givePermissionTo($adminPermission);

        $deliveryRole = Role::create(['name' => 'Delivery Scout']);
        $deliveryPermission = Permission::create(['name' => 'delivery.control']);
        $deliveryRole->givePermissionTo($deliveryPermission);

        $restaurantOwnerRole = Role::create(['name' => 'Restaurant Owner']);
        $restaurantOwnerPermission = Permission::create(['name' => 'restaurant.control']);
        $restaurantOwnerRole->givePermissionTo($restaurantOwnerPermission);

        $customerRole = Role::create(['name' => 'Customer']);
        $customerPermission = Permission::create(['name' => 'user_account.control']);
        $customerRole->givePermissionTo($customerPermission);

        User::find(1)->assignRole('Admin');
        User::find(2)->assignRole('Restaurant Owner');
        User::find(3)->assignRole('Delivery Scout');
        User::find(4)->assignRole('Customer');
    }
}
