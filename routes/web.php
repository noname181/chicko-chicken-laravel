<?php

use Illuminate\Support\Facades\Route;

use App\Mail\ResetPassword;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $role = Auth::user()->getRoleNames()->first();
    $path = '/login';
    switch ($role) {
        case 'Admin':
                $path = '/admin';
            break;
        case 'Restaurant Owner':
                $path = '/restaurant-owner';
            break;
        default:
                $path = '/login';
            break;
    }
    return redirect($path);
})->middleware('auth');

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
  ]);

Route::name('admin')->middleware(['auth','role:Admin'])->prefix('admin')->group(function () {
    Route::get('/', 'AdminController@dashboard');

    Route::get('/orders', 'AdminController@orders');
    Route::get('/orders/{id}', 'AdminController@order_detail')->where('id', '[0-9]+');
    Route::put('/orders/{id}/update', 'AdminController@order_update')->where('id', '[0-9]+');


    Route::get('/restaurants', 'AdminController@restaurants');
    Route::get('/restaurants/create', 'AdminController@create_restaurant');
    Route::get('/restaurants/{id}/edit', 'AdminController@edit_restaurant')->where('id', '[0-9]+');
    Route::post('/restaurants/store', 'AdminController@store_restaurant');
    Route::put('/restaurants/{id}/update', 'AdminController@update_restaurant')->where('id', '[0-9]+');

    Route::get('/push_notification', 'AdminController@push_notification');
    Route::post('/push_notification/update', 'AdminController@update_push_notification');
    
    Route::get('/dishes', 'AdminController@dishes');
    Route::get('/dishes/create', 'AdminController@create_dish');
    Route::get('/dishes/{id}/edit', 'AdminController@edit_dish')->where('id', '[0-9]+');
    Route::post('/dishes/store', 'AdminController@store_dish');
    Route::put('/dishes/{id}/update', 'AdminController@update_dish')->where('id', '[0-9]+');
    
    Route::get('/dish_categories', 'AdminController@dish_categories');
    Route::get('/dish_categories/create', 'AdminController@create_dish_category');
    Route::get('/dish_categories/{id}/edit', 'AdminController@edit_dish_category')->where('id', '[0-9]+');
    Route::post('/dish_categories/store', 'AdminController@store_dish_category');
    Route::put('/dish_categories/{id}/update', 'AdminController@update_dish_category')->where('id', '[0-9]+');
    
    Route::get('/dish_addons', 'AdminController@addons');
    Route::get('/dish_addons/create', 'AdminController@create_addon');
    Route::get('/dish_addons/{id}/edit', 'AdminController@edit_addon')->where('id', '[0-9]+');
    Route::post('/dish_addons/store', 'AdminController@store_addon');
    Route::put('/dish_addons/{id}/update', 'AdminController@update_addon')->where('id', '[0-9]+');
    
    Route::get('/dish_addons_categories', 'AdminController@addons_categories');
    Route::get('/dish_addons_categories/create', 'AdminController@create_addons_category');
    Route::get('/dish_addons_categories/{id}/edit', 'AdminController@edit_addons_category')->where('id', '[0-9]+');
    Route::post('/dish_addons_categories/store', 'AdminController@store_addons_category');
    Route::put('/dish_addons_categories/{id}/update', 'AdminController@update_addons_category')->where('id', '[0-9]+');

    Route::get('/coupons', 'AdminController@coupons');
    Route::get('/coupons/create', 'AdminController@create_coupon');
    Route::get('/coupons/{id}/edit', 'AdminController@edit_coupon')->where('id', '[0-9]+');
    Route::post('/coupons/store', 'AdminController@store_coupon');
    Route::put('/coupons/{id}/update', 'AdminController@update_coupon')->where('id', '[0-9]+');

    Route::get('/users/create', 'AdminController@create_user');
    Route::get('/users/{type}', 'AdminController@users');
    Route::get('/users/{id}/edit', 'AdminController@edit_user')->where('id', '[0-9]+');
    Route::post('/users/store', 'AdminController@store_user');
    Route::put('/users/{id}/update', 'AdminController@update_user')->where('id', '[0-9]+');

    Route::get('/settings', 'AdminController@settings');
    Route::put('/settings/update', 'AdminController@update_settings');
});

Route::name('restaurant-owner')->middleware(['auth','role:Restaurant Owner'])->prefix('restaurant-owner')->group(function () {
    Route::get('/', 'RestaurantOwnerController@dashboard');

    Route::get('/orders', 'RestaurantOwnerController@orders');
    Route::get('/live-orders', 'RestaurantOwnerController@live_orders');
    Route::get('/orders/{id}', 'RestaurantOwnerController@order_detail')->where('id', '[0-9]+');
    Route::put('/orders/{id}/update', 'RestaurantOwnerController@order_update')->where('id', '[0-9]+');
    
    Route::get('/dishes', 'RestaurantOwnerController@dishes');
    Route::get('/dishes/create', 'RestaurantOwnerController@create_dish');
    Route::get('/dishes/{id}/edit', 'RestaurantOwnerController@edit_dish')->where('id', '[0-9]+');
    Route::post('/dishes/store', 'RestaurantOwnerController@store_dish');
    Route::put('/dishes/{id}/update', 'RestaurantOwnerController@update_dish')->where('id', '[0-9]+');
    
    Route::get('/dish_categories', 'RestaurantOwnerController@dish_categories');
    Route::get('/dish_categories/create', 'RestaurantOwnerController@create_dish_category');
    Route::get('/dish_categories/{id}/edit', 'RestaurantOwnerController@edit_dish_category')->where('id', '[0-9]+');
    Route::post('/dish_categories/store', 'RestaurantOwnerController@store_dish_category');
    Route::put('/dish_categories/{id}/update', 'RestaurantOwnerController@update_dish_category')->where('id', '[0-9]+');
    
    Route::get('/dish_addons', 'RestaurantOwnerController@addons');
    Route::get('/dish_addons/create', 'RestaurantOwnerController@create_addon');
    Route::get('/dish_addons/{id}/edit', 'RestaurantOwnerController@edit_addon')->where('id', '[0-9]+');
    Route::post('/dish_addons/store', 'RestaurantOwnerController@store_addon');
    Route::put('/dish_addons/{id}/update', 'RestaurantOwnerController@update_addon')->where('id', '[0-9]+');
    
    Route::get('/dish_addons_categories', 'RestaurantOwnerController@addons_categories');
    Route::get('/dish_addons_categories/create', 'RestaurantOwnerController@create_addons_category');
    Route::get('/dish_addons_categories/{id}/edit', 'RestaurantOwnerController@edit_addons_category')->where('id', '[0-9]+');
    Route::post('/dish_addons_categories/store', 'RestaurantOwnerController@store_addons_category');
    Route::put('/dish_addons_categories/{id}/update', 'RestaurantOwnerController@update_addons_category')->where('id', '[0-9]+');
});

Route::get('/admin/settings/update', function(){
    return redirect()->back();
});