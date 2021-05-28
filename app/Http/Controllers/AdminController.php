<?php

namespace App\Http\Controllers;

use App\Addon;
use App\AddonDish;
use App\AddonsCategory;
use App\Coupon;
use App\Dish;
use App\DishCategory;
use App\Http\Requests\AddonRequest;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\DishRequest;
use App\Http\Requests\RestaurantRequest;
use App\Http\Requests\UserRequest;
use App\Notification;
use App\Order;
use App\Restaurant;
use App\Traits\Utility;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Setting;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    use Utility;

    public function dashboard()
    {

        $today_orders = Order::whereDate('created_at', Carbon::today())->get();

        $all_orders = Order::all();

        $last_seven_days_orders = Order::whereDate('created_at', '>=', Carbon::today()->subDays(7))->get();

        $users = $last_seven_days_orders->sortBy('created_at')->groupBy(function ($item) {
            return $item->created_at->format('d');
        });

        $seven_days_record = [];
        $last_seven_days_orders_count = 0;

        for ($i = 0; $i < 7; $i += 1) {
            $key = Carbon::today()->subDays($i)->format('d');
            if (isset($users[$key])) {
                $seven_days_record[] = $users[$key]->count();
                $last_seven_days_orders_count += $users[$key]->count();
            } else {
                $seven_days_record[] = 0;
            }
        }

        $stats = new \stdClass;
        $stats->today_orders = $today_orders;
        $stats->all_orders = $all_orders;
        $stats->last_seven_days_orders_count = $last_seven_days_orders_count;
        $stats->seven_days_record = array_reverse($seven_days_record);

        return view('admin.dashboard')->withStats($stats);
    }

    public function orders()
    {
        $orders = Order::with('restaurant')->latest()->get();
        return view('admin.orders.index')->withOrders($orders);
    }

    public function order_detail($id)
    {
        $order = Order::with('restaurant', 'user', 'order_dishes', 'order_dishes.order_adddons', 'order_delivery_assign')->where('id', $id)->first();

        $orderStatus = ['ORDER PLACED', 'ORDER ACCEPTED', 'ORDER PREPARED', 'On the Way', 'Delivered'];
        $paymentStatus = ['NOT_PAID', 'PAID', 'YET_TO_BE_PAID'];
        $delivery_scout = User::role('Delivery Scout')->get();
        return view('admin.orders.detail')->withOrder($order)->withDeliveryScout($delivery_scout)->withOrderStatus($orderStatus)->withPaymentStatus($paymentStatus);
    }

    public function order_update($id, Request $request)
    {
        // dd($request->all());
        $order = Order::with('order_delivery_assign')->where('id', $id)->first();

        if ($order) {
            if ($order->status != $request->order_status) {
                $order->status = $request->order_status;

                $message;
                switch ($request->order_status) {
                    case 'ORDER ACCEPTED':
                        $message = 'Your order ' . $order->unique_id . ' is Accepted';
                        break;
                    case 'ORDER PREPARED':
                        $message = 'Your order ' . $order->unique_id . ' is prepared and ready for pickup.';
                        break;
                    case 'On the Way':
                        $message = 'Your order ' . $order->unique_id . ' is on the way. Be ready';
                        break;
                    case 'Delivered':
                        $message = 'Your order ' . $order->unique_id . ' is delivered successfully';

                }
                Notification::create([
                    'message' => $message,
                    'user_id' => $order->user_id,
                ]);
                $this->sendFCM($message, $order->user_id);
            }

            if ($request->delivery_user_id != null) {
                if ($order->order_delivery_assign->user_id != $request->delivery_user_id) {
                    $message = 'Your order ' . $order->unique_id . ' will be delivered by ' . $order->user->name;
                    Notification::create([
                        'message' => $message,
                        'user_id' => $order->user_id,
                    ]);

                    $this->sendFCM($message, $order->user_id);

                    $message = 'You\'re assigned to a new order ' . $order->unique_id;
                    Notification::create([
                        'message' => $message,
                        'user_id' => $request->delivery_user_id,
                    ]);
                    $this->sendFCM($message, $request->delivery_user_id);
                }

                $order->order_delivery_assign()
                    ->update([
                        'user_id' => $request->delivery_user_id,
                    ]);
            }

            try {
                $order->save();
                return redirect()->back()->with(['success' => 'Updated Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        }
    }

    public function restaurants()
    {
        $restaurants = Restaurant::with('user')->latest()->get();
        return view('admin.restaurants.index')->withRestaurants($restaurants);
    }

    public function create_restaurant()
    {
        $owners = User::role('Restaurant Owner')->get();
        return view('admin.restaurants.create')->withOwners($owners);
    }

    public function edit_restaurant($id)
    {
        $restaurant = Restaurant::with('addresses')->where('id', $id)->first();
        $addresses = $restaurant->addresses->first();
        $owners = User::role('Restaurant Owner')->get();
        $dishes = Dish::where('restaurant_id', $id)->with(['dish_category'])->latest()->get();

        return view('admin.restaurants.edit')->withRestaurant($restaurant)->withOwners($owners)->withAddresses($addresses)->withDishes($dishes);
    }

    public function store_restaurant(RestaurantRequest $request)
    {
        $restaurant = new Restaurant();

        $restaurant->name = $request->name;
        $restaurant->description = $request->description;

        $image = $request->file('image');
        $rand_name = time() . Str::random(12);
        $filename = $rand_name . '.jpg';

        $photo = Image::make($image)->fit(600, 360, function ($constraint) {
            // $constraint->upsize();
        })->encode('jpg', 80);

        Storage::disk('public')->put(config('path.restaurant') . $filename, $photo);

        $restaurant->image = $filename;

        $restaurant->phone = $request->phone;
        $restaurant->email = $request->email;

        $restaurant->rating = $request->rating;
        $restaurant->delivery_time = $request->delivery_time;
        $restaurant->for_two = $request->for_two;

        $restaurant->license_code = $request->license_code;

        $restaurant->restaurant_charges = $request->restaurant_charges;
        $restaurant->commission_rate = $request->commission_rate;

        if ($request->delivery_radius != null) {
            $restaurant->delivery_radius = $request->delivery_radius;
        }

        if ($request->is_veg == 'on') {
            $restaurant->is_veg = true;
        } else {
            $restaurant->is_veg = false;
        }

        if ($request->featured == 'on') {
            $restaurant->featured = true;
        } else {
            $restaurant->featured = false;
        }

        $restaurant->user_id = $request->user_id;

        $restaurant->active = false;

        try {
            $restaurant->save();

            $restaurant->addresses()->create([
                'label' => 'Restaurant Address',
                'given_name' => $request->name,
                'family_name' => $request->name,
                'street' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->pincode,
                'latitude' => $request->lat,
                'longitude' => $request->long,
                'is_primary' => true,
                'is_billing' => true,
                'is_shipping' => true,
            ]);

            $restaurant->save();

            return redirect('admin/restaurants');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function update_restaurant($id, RestaurantRequest $request)
    {
        $restaurant = Restaurant::where('id', $id)->first();

        if ($restaurant) {
            $restaurant->name = $request->name;
            $restaurant->description = $request->description;

            if ($request->image != null) {
                $image = $request->file('image');
                $rand_name = time() . Str::random(12);
                $filename = $rand_name . '.jpg';

                $photo = Image::make($image)->fit(600, 360, function ($constraint) {
                    // $constraint->upsize();
                })->encode('jpg', 80);

                Storage::disk('public')->put(config('path.restaurant') . $filename, $photo);

                $restaurant->image = $filename;
            }

            $restaurant->phone = $request->phone;
            $restaurant->email = $request->email;

            $restaurant->rating = $request->rating;
            $restaurant->delivery_time = $request->delivery_time;
            $restaurant->for_two = $request->for_two;

            $restaurant->license_code = $request->license_code;

            $restaurant->restaurant_charges = $request->restaurant_charges;
            $restaurant->commission_rate = $request->commission_rate;

            if ($request->delivery_radius != null) {
                $restaurant->delivery_radius = $request->delivery_radius;
            }

            if ($request->is_veg == 'on') {
                $restaurant->is_veg = true;
            } else {
                $restaurant->is_veg = false;
            }

            if ($request->featured == 'on') {
                $restaurant->featured = true;
            } else {
                $restaurant->featured = false;
            }

            if ($request->active == 'on') {
                $restaurant->active = true;
            } else {
                $restaurant->active = false;
            }

            $restaurant->user_id = $request->user_id;

            try {
                $restaurant->save();

                $restaurant->addresses()->create([
                    'label' => 'Restaurant Address',
                    'given_name' => $request->name,
                    'family_name' => $request->name,
                    'street' => $request->address,
                    'city' => $request->city,
                    'postal_code' => $request->pincode,
                    'latitude' => $request->lat,
                    'longitude' => $request->long,
                    'is_primary' => true,
                    'is_billing' => true,
                    'is_shipping' => true,
                ]);

                $restaurant->save();
                return redirect()->back()->with(['success' => 'Updated Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->withErrors(['message' => $e->getMessage()]);
            }
        }
    }

    public function dishes()
    {
        $dishes = Dish::with(['restaurant', 'dish_category'])->get()->sortByDesc('id');
        return view('admin.dishes.index')->withDishes($dishes);
    }

    public function create_dish()
    {
        $restaurants = Restaurant::all()->sortByDesc('id');
        $categories = DishCategory::all()->sortByDesc('id');
        $addons = AddonsCategory::where('user_id', Auth::user()->id)->get()->sortByDesc('id');

        return view('admin.dishes.create')->withRestaurants($restaurants)->withCategories($categories)->withAddons($addons);
    }

    public function edit_dish($id)
    {
        $dish = Dish::with(['addons_dish'])->where('id', $id)->first();

        $restaurants = Restaurant::all()->sortByDesc('id');
        $categories = DishCategory::all()->sortByDesc('id');
        $addons = AddonsCategory::where('user_id', Auth::user()->id)->get()->sortByDesc('id');

        return view('admin.dishes.edit')->withDish($dish)->withRestaurants($restaurants)->withCategories($categories)->withAddons($addons);
    }

    public function store_dish(DishRequest $request)
    {
        $dish = new Dish();

        $dish->name = $request->name;
        $dish->description = $request->description;
        $dish->price = $request->price;
        $dish->discount_price = $request->discount_price == null ? 0 : $request->discount_price;

        $dish->restaurant_id = $request->restaurant_id;
        $dish->dish_category_id = $request->dish_category_id;

        if ($request->image != null) {
            $image = $request->file('image');
            $rand_name = time() . Str::random(12);
            $filename = $rand_name . '.jpg';

            $photo = Image::make($image)->fit(400, 350, function ($constraint) {
                // $constraint->upsize();
            })->encode('jpg', 80);

            Storage::disk('public')->put(config('path.dishes') . $filename, $photo);

            $dish->image = $filename;
        }

        if ($request->is_veg == 'on') {
            $dish->is_veg = true;
        } else {
            $dish->is_veg = false;
        }

        if ($request->featured == 'on') {
            $dish->featured = true;
        } else {
            $dish->featured = false;
        }

        if ($request->active == 'on') {
            $dish->active = true;
        } else {
            $dish->active = false;
        }

        try {
            $dish->save();

            foreach ($request->addon_id as $addon_id) {
                $dish_addon = new AddonDish();
                $dish_addon->addons_category_id = $addon_id;
                $dish_addon->dish_id = $dish->id;
                $dish_addon->save();
            }

            return redirect('admin/dishes');
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function update_dish($id, DishRequest $request)
    {
        $dish = Dish::where('id', $id)->first();

        if ($dish) {
            $dish->name = $request->name;
            $dish->description = $request->description;
            $dish->price = $request->price;
            $dish->discount_price = $request->discount_price == null ? 0 : $request->discount_price;

            $dish->restaurant_id = $request->restaurant_id;
            $dish->dish_category_id = $request->dish_category_id;

            if ($request->image != null) {
                $image = $request->file('image');
                $rand_name = time() . Str::random(12);
                $filename = $rand_name . '.jpg';

                $photo = Image::make($image)->fit(400, 350, function ($constraint) {
                    // $constraint->upsize();
                })->encode('jpg', 80);

                Storage::disk('public')->put(config('path.dishes') . $filename, $photo);

                $dish->image = $filename;
            }

            if ($request->is_veg == 'on') {
                $dish->is_veg = true;
            } else {
                $dish->is_veg = false;
            }

            if ($request->featured == 'on') {
                $dish->featured = true;
            } else {
                $dish->featured = false;
            }

            if ($request->active == 'on') {
                $dish->active = true;
            } else {
                $dish->active = false;
            }

            try {
                $dish->save();

                AddonDish::where('dish_id', $dish->id)->whereNotIn('addons_category_id', $request->addon_id ?? [])->delete();

                if (isset($request->addon_id)) {
                    // Create new which is added now
                    foreach ($request->addon_id as $addon_id) {
                        AddonDish::updateOrCreate(
                            ['addons_category_id' => $addon_id, 'dish_id' => $dish->id]
                        );
                    }
                }

                return redirect()->back()->with(['success' => 'Updated Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        }
    }

    public function dish_categories()
    {
        $categories = DishCategory::all()->sortByDesc('id');
        return view('admin.dishes_categories.index')->withCategories($categories);
    }

    public function create_dish_category()
    {
        return view('admin.dishes_categories.create');
    }

    public function edit_dish_category($id)
    {
        $category = DishCategory::where('id', $id)->first();
        return view('admin.dishes_categories.edit')->withCategory($category);
    }

    public function store_dish_category(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $category = new DishCategory();
        $category->name = $request->name;

        if ($request->image != null) {
            $image = $request->file('image');
            $rand_name = time() . Str::random(12);
            $filename = $rand_name . '.jpg';

            $photo = Image::make($image)->fit(200, 200)->encode('jpg', 80);

            Storage::disk('public')->put(config('path.categories') . $filename, $photo);

            $category->image = $filename;
        }

        if ($request->active == 'on') {
            $category->active = true;
        } else {
            $category->active = false;
        }

        try {
            $category->save();
            return redirect('admin/dish_categories')->with(['success' => 'Saved Successfully!']);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function update_dish_category($id, Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $category = DishCategory::where('id', $id)->first();

        if ($category) {
            $category->name = $request->name;

            if ($request->image != null) {
                $image = $request->file('image');
                $rand_name = time() . Str::random(12);
                $filename = $rand_name . '.jpg';

                $photo = Image::make($image)->fit(200, 200)->encode('jpg', 80);

                Storage::disk('public')->put(config('path.categories') . $filename, $photo);

                $category->image = $filename;
            }

            if ($request->active == 'on') {
                $category->active = true;
            } else {
                $category->active = false;
            }

            try {
                $category->save();
                return redirect('admin/dish_categories')->with(['success' => 'Saved Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        }
    }

    public function addons()
    {
        $addons = Addon::with(['addons_category'])->get()->sortByDesc('id');
        return view('admin.addons.index')->withAddons($addons);
    }

    public function create_addon()
    {
        $restaurants = Restaurant::all()->sortByDesc('id');
        $categories = AddonsCategory::where('user_id', Auth::user()->id)->get()->sortByDesc('id');
        return view('admin.addons.create')->withCategories($categories);
    }

    public function edit_addon($id)
    {
        $addon = Addon::where('id', $id)->first();
        $categories = AddonsCategory::where('user_id', Auth::user()->id)->get()->sortByDesc('id');

        return view('admin.addons.edit')->withAddon($addon)->withCategories($categories);
    }

    public function store_addon(AddonRequest $request)
    {
        $addon = new Addon();

        $addon->name = $request->name;
        $addon->price = $request->price;
        $addon->addons_category_id = $request->addons_category_id;
        $addon->user_id = Auth::user()->id;
        $addon->active = true;

        try {
            $addon->save();
            return redirect('admin/dish_addons');
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function update_addon($id, AddonRequest $request)
    {
        $addon = Addon::where('id', $id)->first();

        if ($addon) {
            $addon->name = $request->name;
            $addon->price = $request->price;
            $addon->addons_category_id = $request->addons_category_id;
            $addon->user_id = Auth::user()->id;

            if ($request->active == 'on') {
                $addon->active = true;
            } else {
                $addon->active = false;
            }

            try {
                $addon->save();
                return redirect()->back()->with(['success' => 'Updated Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        }
    }

    public function addons_categories()
    {
        $addons = AddonsCategory::get()->sortByDesc('id');
        return view('admin.addons_category.index')->withAddons($addons);
    }

    public function create_addons_category()
    {
        $types = ['SINGLE', 'MULTIPLE'];
        return view('admin.addons_category.create')->withTypes($types);
    }

    public function edit_addons_category($id)
    {
        $addon = AddonsCategory::where([['id', $id], ['user_id', Auth::user()->id]])->first();
        $types = ['SINGLE', 'MULTIPLE'];

        return view('admin.addons_category.edit')->withAddon($addon)->withTypes($types);
    }

    public function store_addons_category(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
        ]);

        $addon = new AddonsCategory();
        $addon->name = $request->name;
        $addon->type = $request->type;
        $addon->user_id = Auth::user()->id;

        try {
            $addon->save();
            return redirect('admin/dish_addons_categories');
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function update_addons_category($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
        ]);

        $addon = AddonsCategory::where('id', $id)->first();

        if ($addon) {
            $addon->name = $request->name;
            $addon->type = $request->type;

            try {
                $addon->save();
                return redirect()->back()->with(['success' => 'Updated Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        }
    }

    public function coupons()
    {
        $coupons = Coupon::with(['restaurant'])->get()->sortByDesc('id');
        return view('admin.coupons.index')->withCoupons($coupons);
    }

    public function create_coupon()
    {
        $restaurants = Restaurant::all()->sortByDesc('id');
        $restaurants->prepend((object) array(
            'name' => 'All Restaurants',
            'id' => '0',
        ));

        $discount_type = array(
            (object) array(
                'name' => 'Fixed Amount Discount',
                'id' => 'FIXED',
            ),
            (object) array(
                'name' => 'Percentage Discount',
                'id' => 'PERCENTAGE',
            ),
        );

        return view('admin.coupons.create')->withRestaurants($restaurants)->withDiscountType($discount_type);
    }

    public function edit_coupon($id)
    {
        $coupon = Coupon::where('id', $id)->first();

        $restaurants = Restaurant::all()->sortByDesc('id');
        $restaurants->prepend((object) array(
            'name' => 'All Restaurants',
            'id' => '0',
        ));

        $discount_type = array(
            (object) array(
                'name' => 'Fixed Amount Discount',
                'id' => 'FIXED',
            ),
            (object) array(
                'name' => 'Percentage Discount',
                'id' => 'PERCENTAGE',
            ),
        );

        return view('admin.coupons.edit')->withCoupon($coupon)->withRestaurants($restaurants)->withDiscountType($discount_type);
    }

    public function store_coupon(CouponRequest $request)
    {
        $coupon = new Coupon();

        $coupon->name = $request->name;
        $coupon->description = $request->description;
        $coupon->coupon_code = $request->coupon_code;
        $coupon->discount_type = $request->discount_type;
        $coupon->discount = $request->discount;
        $coupon->price = $request->price;
        $coupon->expiry_date = Carbon::parse($request->expiry_date)->format('Y-m-d');
        $coupon->max_usage = $request->max_usage;

        $coupon->restaurant_id = $request->restaurant_id == '0' ? null : $request->restaurant_id;

        if ($request->active == 'on') {
            $coupon->active = true;
        } else {
            $coupon->active = false;
        }

        try {
            $coupon->save();
            return redirect('admin/coupons')->with(['success' => 'Saved Successfully!']);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function update_coupon($id, CouponRequest $request)
    {
        $coupon = Coupon::where('id', $id)->first();

        if ($coupon) {
            $coupon->name = $request->name;
            $coupon->description = $request->description;
            $coupon->coupon_code = $request->coupon_code;
            $coupon->discount_type = $request->discount_type;
            $coupon->discount = $request->discount;
            $coupon->price = $request->price;
            $coupon->expiry_date = Carbon::parse($request->expiry_date)->format('Y-m-d');
            $coupon->max_usage = $request->max_usage;

            $coupon->restaurant_id = $request->restaurant_id == '0' ? null : $request->restaurant_id;

            if ($request->active == 'on') {
                $coupon->active = true;
            } else {
                $coupon->active = false;
            }

            try {
                $coupon->save();
                return redirect()->back()->with(['success' => 'Updated Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        }
    }

    public function users($type)
    {
        if ($type == 'customers') {
            $role = 'Customer';
        } elseif ($type == 'owners') {
            $role = 'Restaurant Owner';
        } elseif ($type == 'delivery') {
            $role = 'Delivery Scout';
        } else {
            return redirect()->back();
        }

        $users = User::role($role)->get();
        return view('admin.users.index')->withUsers($users)->withRole($role);
    }

    public function create_user()
    {
        $roles = Role::where('name', '!=', 'Admin')->get()->sortByDesc('id');

        return view('admin.users.create')->withRoles($roles);
    }

    public function edit_user($id)
    {
        $user = User::where('id', $id)->first();
        $roles = Role::where('name', '!=', 'Admin')->get()->sortByDesc('id');
        return view('admin.users.edit')->withUser($user)->withRoles($roles);
    }

    /**
     * @param Request $request
     */
    public function store_user(UserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => \Hash::make($request->password),
            ]);

            $role = Role::findById($request->role)->name;
            $user->assignRole($role);

            if ($request->avatar != null) {
                $image = $request->file('avatar');
                $rand_name = time() . Str::random(12);
                $filename = $rand_name . '.jpg';

                $photo = Image::make($image)->fit(300, 300, function ($constraint) {
                    // $constraint->upsize();
                })->encode('jpg', 80);

                Storage::disk('public')->put(config('path.avatar') . $filename, $photo);
                $user->avatar = $filename;
                $user->save();
            }
            if ($role == 'Customer') {
                $redirect = 'customers';
            } elseif ($role == 'Restaurant Owner') {
                $redirect = 'owners';
            } elseif ($role == 'Delivery Scout') {
                $redirect = 'delivery';
            }

            return redirect('admin/users/' . $redirect);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function update_user($id, UserRequest $request)
    {
        $user = User::where('id', $id)->first();

        if ($user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;

            if ($request->avatar != null) {
                $image = $request->file('avatar');
                $rand_name = time() . Str::random(12);
                $filename = $rand_name . '.jpg';

                $photo = Image::make($image)->fit(200, 200)->encode('jpg', 80);

                Storage::disk('public')->put(config('path.avatar') . $filename, $photo);

                $user->avatar = $filename;
            }
            $role = Role::findById($request->role)->name;
            $user->assignRole($role);

            try {
                $user->save();
                return redirect('admin/users/' . $id . '/edit')->with(['success' => 'Updated Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        }
    }

    public function settings()
    {
        $settings = (object) Setting::all();
        // dd($settings);
        return view('admin.settings.index')->withSettings($settings);
    }

    public function update_settings(Request $request)
    {
        $req = $request->all();
        unset($req["_token"]);
        unset($req["_method"]);

        if (in_array($req["setting_tab"], ["general", "fcm", "sms_gateway", "payment_gateway"])) {
            if ($req["setting_tab"] == "general") {
                $radio_options = ["tax_applicable", "delivery_charge_applicable"];
            } elseif ($req["setting_tab"] == "fcm") {
                $radio_options = ["fcm_active"];
            } elseif ($req["setting_tab"] == "sms_gateway") {
                $radio_options = ["verification_required"];
            } elseif ($req["setting_tab"] == "payment_gateway") {
                $radio_options = ["pg_paypal_active", "pg_razorpay_active", "pg_cod_active"];
            }
            foreach ($radio_options as $value) {
                if (array_key_exists($value, $req)) {
                    Setting::set($value, true);
                    unset($req[$value]);
                } else {
                    Setting::set($value, false);
                }
            }
        }

        unset($req["setting_tab"]);

        foreach ($req as $key => $value) {
            Setting::set($key, $value == null ? '' : $value);
        }

        try {
            Setting::save();
            return redirect()->back()->with(['success' => 'Updated Successfully!']);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function push_notification()
    {
        $users = User::all()->sortByDesc("id");
        $users->prepend((object) array(
            'name' => 'All Users',
            'id' => '0',
        ));

        return view('admin.utilities.push_notification')->withUsers($users);
    }

    public function update_push_notification(Request $request)
    {
        $request->validate([
            'user_id.*' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($request->user_id[0] == '0') {
            $users = User::where('fcm_token', '!=', null)->pluck('fcm_token')->toArray();
        } else {
            $users = User::whereIn('id', $request->user_id)->get()->pluck('fcm_token')->toArray();
        }

        try {
            $this->sendFCM($request->message, $users, $request->title, true);
            return redirect()->back()->with(['success' => 'Sent Successfully!']);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }
}
