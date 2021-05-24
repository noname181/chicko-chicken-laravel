<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('/login', 'API\AuthController@login');
Route::post('/register', 'API\AuthController@register');
Route::post('/verify_otp', 'API\AuthController@verifyOTP');
Route::post('/resend_otp', 'API\AuthController@resendOTP');
Route::post('/password/reset', 'API\AuthController@reset_password');

Route::get('/settings/fcm', 'API\AuthController@fcm_settings');
Route::get('/restaurants/nearby_latlong', 'API\RestaurantController@nearby_latlong');
Route::get('/restaurants/all', 'API\RestaurantController@all');
Route::get('/restaurants/all_dishes', 'API\RestaurantController@all_dishes');
Route::get('/restaurants/all_cities', 'API\RestaurantController@all_cities');
Route::get('/settings/map', 'API\AuthController@map_settings');
Route::middleware(['auth:sanctum', 'role:Customer'])->group(function () {
    Route::get('/category/{id}', 'API\RestaurantController@category');
    Route::get('/categories', 'API\RestaurantController@all_categories');

    Route::get('/restaurants/nearby', 'API\RestaurantController@nearby_restaurants');

    Route::get('/top_categories/{id}', 'API\RestaurantController@top_categories')->where('id', '[0-9]+');
    Route::post('/restaurants/search', 'API\RestaurantController@search_restaurant');

    Route::get('/restaurant/{id}', 'API\RestaurantController@restaurant')->where('id', '[0-9]+');
    Route::get('/cart/{id}', 'API\RestaurantController@cart')->where('id', '[0-9]+');

    Route::get('/orders', 'API\OrderController@orders');
    Route::post('/order/place_order', 'API\OrderController@place_order');
    Route::post('/payment/razorpay', 'API\OrderController@process_razorpay');

    Route::post('/coupon/verify', 'API\RestaurantController@coupon_verify');

    Route::get('/users', 'API\AuthController@users');
    Route::post('/user/fcm_token', 'API\UserController@save_token');

    Route::get('/user/notifications', 'API\UserController@notifications');
    Route::get('/user/addresses', 'API\UserController@addresses');
    Route::post('/user/address', 'API\UserController@save_address');
    Route::put('/user/address', 'API\UserController@update_address');
    Route::delete('/user/address', 'API\UserController@delete_address');
    Route::post('/user/profile/image_update', 'API\UserController@update_profile_img');

    Route::get('/settings/payment_options', 'API\AuthController@payment_settings');
    Route::get('/settings/razorpay', 'API\AuthController@razorpay_settings');
});

Route::post('/delivery/login', 'API\DeliveryScoutController@login');

Route::middleware(['auth:sanctum', 'role:Delivery Scout'])->group(function () {

    Route::get('/orders/live', 'API\DeliveryScoutController@live_orders');
    Route::get('/orders/past', 'API\DeliveryScoutController@past_orders');

    Route::get('/order/{id}', 'API\DeliveryScoutController@order_detail')->where('id', '[0-9]+');
    Route::put('/order/{id}/update', 'API\DeliveryScoutController@order_update')->where('id', '[0-9]+');

    Route::post('/delivery/position', 'API\DeliveryScoutController@update_position');

    Route::post('/delivery/fcm_token', 'API\UserController@save_token');
    Route::get('/delivery/notifications', 'API\UserController@notifications');

    Route::post('/delivery/profile/image_update', 'API\DeliveryScoutController@update_profile_img');
});