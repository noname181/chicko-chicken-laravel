<?php

namespace App\Http\Controllers\API;

use App\CityRestaurant;
use App\Coupon;
use App\Dish;
use App\DishCategory;
use App\Http\Controllers\Controller;
use App\Restaurant;
use App\Traits\DistanceHelper;
use App\Traits\ResponseHelper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Setting;

class RestaurantController extends Controller
{
    use ResponseHelper, DistanceHelper;

    public function all_cities(Request $request)
    {
        $cities = CityRestaurant::all();
        $all = new Collection();
        foreach ($cities as $city) {
            $distance = $this->vincentyGreatCircleDistance($request->lat, $request->long, $city->latitude, $city->longitude);
            // return $distance <= $restaurant->delivery_radius;

            $city->distance = $distance;
            $all->push($city);

        }
        return $all;
    }

    public function all_dishes(Request $request)
    {
        $dishes = Dish::where('restaurant_id', $request->id)->get();

        return $dishes;
    }

    public function all(Request $request)
    {
        $restaurants = Restaurant::with('addresses')->where('active', 1)->inRandomOrder()->get();
        $all = new Collection();

        foreach ($restaurants as $restaurant) {
            $distance = $this->vincentyGreatCircleDistance($request->lat, $request->long, $restaurant->lat, $restaurant->long);
            // return $distance <= $restaurant->delivery_radius;

            $restaurant->distance = $distance;
            $all->push($restaurant);

        }

        return $all;
    }

    public function nearby_latlong(Request $request)
    {
        $restaurants = Restaurant::with('addresses')->where('active', 1)->inRandomOrder()->get();
        $nearby_latlong = new Collection();

        foreach ($restaurants as $restaurant) {
            $distance = $this->vincentyGreatCircleDistance($request->lat, $request->long, $restaurant->addresses[0]->latitude, $restaurant->addresses[0]->longitude);
            // return $distance <= $restaurant->delivery_radius;

            if ($distance <= 15) {
                $restaurant->distance = $distance;
                $nearby_latlong->push($restaurant);
            }
        }

        return $nearby_latlong;
    }

    public function nearby()
    {
        $user_address = Auth::user()->addresses()->isPrimary()->first();
        $restaurants = Restaurant::with('addresses')->where('active', 1)->inRandomOrder()->get();

        $nearby_restaurants = new Collection();

        foreach ($restaurants as $restaurant) {
            $distance = $this->vincentyGreatCircleDistance($user_address->latitude, $user_address->longitude, $restaurant->addresses[0]->latitude, $restaurant->addresses[0]->longitude);
            // return $distance <= $restaurant->delivery_radius;
            $nearby_restaurants->push($restaurant);
            // if ($distance <= $restaurant->delivery_radius) {
            //     $nearby_restaurants->push($restaurant);
            // }
        }

        return $nearby_restaurants;
    }

    public function nearby_restaurants()
    {
        $nearby_restaurants = $this->nearby();

        $nearby_restaurants = $nearby_restaurants->map(function ($restaurant) {
            return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'delivery_time', 'for_two', 'featured']);
        });

        $restaurants_ids = $nearby_restaurants->map(function ($restaurant) {
            return $restaurant['id'];
        });

        $categories = DishCategory::whereHas('dishes', function ($query) use ($restaurants_ids) {
            $query->whereIn('restaurant_id', $restaurants_ids)->where('active', 1);
        })->where('active', 1)->inRandomOrder()->get(['id', 'name', 'image']);

        $user = Auth::user();
        $notifications = $user->notifications()->where('read', 0)->count();

        $data = [
            'restaurants' => $nearby_restaurants,
            'categories' => $categories,
            'notifications' => $notifications,
        ];
        return $this->successResponse($data);
    }

    public function top_categories($id)
    {
        $user_address = Auth::user()->addresses()->isPrimary()->first();

        $restaurants = Restaurant::with('addresses')->whereHas('dishes.dish_category', function ($query) use ($id) {
            $query->where([['id', $id], ['active', 1]]);
        })->where('active', 1)->inRandomOrder()->get(['id', 'name', 'description', 'image', 'rating', 'delivery_time', 'for_two', 'featured', 'delivery_radius']);

        $nearby_restaurants = new Collection();

        foreach ($restaurants as $restaurant) {
            $distance = $this->vincentyGreatCircleDistance($user_address->latitude, $user_address->longitude, $restaurant->addresses[0]->latitude, $restaurant->addresses[0]->longitude);
            if ($distance <= $restaurant->delivery_radius) {
                $nearby_restaurants->push($restaurant);
            }
        }

        return $this->successResponse($nearby_restaurants);
    }

    public function search_restaurant(Request $request)
    {
        $request->validate([
            'q' => 'required|string',
        ]);
        $search_query = $request->q;

        $user_address = Auth::user()->addresses()->isPrimary()->first();

        $restaurants = Restaurant::with('addresses')->where('name', 'like', "{$search_query}%")
            ->orWhereHas('dishes', function ($query) use ($search_query) {
                $query->where('name', 'like', "{$search_query}%");
            })->where('active', 1)
            ->get(['id', 'name', 'description', 'image', 'rating', 'delivery_time', 'for_two', 'featured', 'delivery_radius']);

        $nearby_restaurants = new Collection();

        foreach ($restaurants as $restaurant) {
            $distance = $this->vincentyGreatCircleDistance($user_address->latitude, $user_address->longitude, $restaurant->addresses[0]->latitude, $restaurant->addresses[0]->longitude);
            if ($distance <= $restaurant->delivery_radius) {
                $nearby_restaurants->push($restaurant);
            }
        }
        return $this->successResponse($nearby_restaurants);
    }

    public function restaurant($id)
    {
        try {
            $data = Restaurant::with(['addresses', 'dishes', 'dishes.addons_dish.addons_category.addons'])->where('id', $id)->first();
            return $this->successResponse($data);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage() . " Restaurant Not found", 404);
        }
    }

    public function cart($id)
    {
        try {
            $restaurant_details = Restaurant::where('id', $id)->first(['id', 'name', 'restaurant_charges', 'for_two']);
            $coupons = Coupon::whereIn('restaurant_id', [$id, null])->get();

            $address = Auth::user()->addresses()->isPrimary()->first();
            if ($address != null) {
                $address = ['label' => $address->label, 'full_address' => $address->street];
            }
            ;

            $charges = [
                'restaurant_charges' => $restaurant_details->restaurant_charges,
                'delivery_charges' => Setting::get('delivery_charge_applicable') ? Setting::get('delivery_charge') : 0,
                'taxes' => Setting::get('tax_applicable') ? Setting::get('tax_percentage') : 0,
            ];
            $data = [
                'restaurant_details' => $restaurant_details,
                'coupons' => $coupons,
                'charges' => $charges,
                'address' => $address,
            ];

            return $this->successResponse($data);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function coupon_verify(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required',
        ]);

        try {
            $coupons = Coupon::where('coupon_code', $request->coupon_code)->first();
            return $this->successResponse($coupons);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}