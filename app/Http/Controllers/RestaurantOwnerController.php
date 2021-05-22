<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\UserRequest;
use App\Http\Requests\RestaurantRequest;
use App\Http\Requests\DishRequest;
use App\Http\Requests\AddonRequest;
use App\Http\Requests\CouponRequest;

use Carbon\Carbon;
use Spatie\Permission\Models\Role;

use App\Traits\Utility;

use Auth;
use Image;
use Setting;

use App\User;
use App\Restaurant;
use App\Dish;
use App\DishCategory;
use App\Order;
use App\AddonDish;
use App\Addon;
use App\AddonsCategory;
use App\OrderDeliveryAssign;
use App\Coupon;
use App\Notification;

class RestaurantOwnerController extends Controller
{
    use Utility;

    public function dashboard()
    {
        $restaurant_ids = Auth::user()->restaurants()->get()->pluck('id')->toArray();

        $today_orders = Order::with('restaurant')->whereHas('restaurant', function ($query) use ($restaurant_ids) {
            return $query->whereIn('id', $restaurant_ids);
        })->whereDate('created_at', Carbon::today())->get();

        $all_orders = Order::with('restaurant')->whereHas('restaurant', function ($query) use ($restaurant_ids) {
            return $query->whereIn('id', $restaurant_ids);
        })->get();

        $last_seven_days_orders = Order::with('restaurant')->whereHas('restaurant', function ($query) use ($restaurant_ids) {
            return $query->whereIn('id', $restaurant_ids);
        })->whereDate('created_at', '>=', Carbon::today()->subDays(7))->get();

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

        return view('restaurant_owners.dashboard')->withStats($stats);
    }

    public function orders()
    {
        $restaurant_ids = Auth::user()->restaurants()->get()->pluck('id')->toArray();
        $orders = Order::with('restaurant')->whereHas('restaurant', function ($query) use ($restaurant_ids) {
            return $query->whereIn('id', $restaurant_ids);
        })->latest()->get();
        return view('restaurant_owners.orders.index')->withOrders($orders);
    }

    public function live_orders()
    {
        $restaurant_ids = Auth::user()->restaurants()->get()->pluck('id')->toArray();
        $orders = Order::with('restaurant')
                    ->whereHas('restaurant', function ($query) use ($restaurant_ids) {
                        return $query->whereIn('id', $restaurant_ids);
                    })->where('status', '!=', 'Delivered')->latest()->get();
        // dd($orders);
        return view('restaurant_owners.orders.index')->withOrders($orders);
    }

    public function order_detail($id)
    {
        $order = Order::with('restaurant', 'user', 'order_dishes','order_dishes.order_adddons','order_delivery_assign')->where('id', $id)
                    ->whereHas('restaurant', function ($query) {
                        return $query->where('user_id', Auth::user()->id);
                    })->first();
        if ($order) {
            $orderStatus = ['ORDER PLACED', 'ORDER ACCEPTED', 'ORDER PREPARED', 'On the Way', 'Delivered'];
            $paymentStatus = ['NOT_PAID', 'PAID', 'YET_TO_BE_PAID'];
            $delivery_scout = User::role('Delivery Scout')->get();
            return view('restaurant_owners.orders.detail')->withOrder($order)->withDeliveryScout($delivery_scout)->withOrderStatus($orderStatus)->withPaymentStatus($paymentStatus);
        }
    }

    public function order_update($id, Request $request)
    {

        $order = Order::with('order_delivery_assign')->where('id', $id)->whereHas('restaurant', function ($query) {
            return $query->where('user_id', Auth::user()->id);
        })->first();
        
        if ($order) {
            if ($order->status != $request->order_status) {
                $order->status = $request->order_status;
                
                $message;
                switch ($request->order_status) {
                    case 'ORDER ACCEPTED':
                        $message = 'Your order '.$order->unique_id.' is Accepted';
                        break;
                    case 'ORDER PREPARED':
                        $message = 'Your order '.$order->unique_id.' is prepared and will be picked up by a Delivery Scout.';
                        break;
                    case 'On the Way':
                        $message = 'Your order '.$order->unique_id.' is on the way.';
                        break;
                    case 'Delivered':
                        $message = 'Your order '.$order->unique_id.' is delivered successfully';

                }
                Notification::create([
                    'message' => $message,
                    'user_id' => $order->user_id
                ]);
                $this->sendFCM($message, $order->user_id);
            }

            if ($request->delivery_user_id != null) {
                if ($order->order_delivery_assign->user_id != $request->delivery_user_id) {
                    $message = 'Your order '.$order->unique_id.' will be delivered by '.$order->user->name;
                    Notification::create([
                        'message' => $message,
                        'user_id' => $order->user_id
                    ]);
                    $this->sendFCM($message, $order->user_id);
                    
                    $message = 'You\'re assigned to a new order '.$order->unique_id;
                    Notification::create([
                        'message' => $message,
                        'user_id' => $request->delivery_user_id
                    ]);
                    $this->sendFCM($message, $request->delivery_user_id);
                }

                $order->order_delivery_assign()
                        ->update([
                            'user_id'=> $request->delivery_user_id
                        ]);
            }

            try {
                $order->save();
                return redirect()->back()->with(['success' => 'Updated Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        }
        return redirect()->back()->with(['message' => 'Erorr: Something Wrong']);
    }

    public function restaurants()
    {
        $restaurants = Restaurant::with('user')->latest()->get();
        return view('restaurant_owners.restaurants.index')->withRestaurants($restaurants);
    }

    public function dishes()
    {
        $restaurant_ids = Auth::user()->restaurants()->get()->pluck('id')->toArray();
        // dd($restaurant_ids);
        $dishes = Dish::with(['restaurant','dish_category'])->whereIn('restaurant_id', $restaurant_ids)->get()->sortByDesc('id');
        // dd($dishes);
        return view('restaurant_owners.dishes.index')->withDishes($dishes);
    }

    public function create_dish()
    {
        $restaurants = Auth::user()->restaurants()->get();
        $categories = DishCategory::all()->sortByDesc('id');
        $addons = AddonsCategory::where('user_id', Auth::user()->id)->get()->sortByDesc('id');
        return view('restaurant_owners.dishes.create')->withRestaurants($restaurants)->withCategories($categories)->withAddons($addons);
    }

    public function edit_dish($id)
    {
        $restaurants = Auth::user()->restaurants()->get();
        $dish = Dish::with(['addons_dish'])->where('id', $id)->whereHas('restaurant', function ($query) {
            return $query->where('user_id', Auth::user()->id);
        })->first();
        $categories = DishCategory::all()->sortByDesc('id');
        $addons = AddonsCategory::where('user_id', Auth::user()->id)->get()->sortByDesc('id');

        return view('restaurant_owners.dishes.edit')->withDish($dish)->withRestaurants($restaurants)->withCategories($categories)->withAddons($addons);
    }

    public function store_dish(DishRequest $request)
    {
        $restaurant_ids = Auth::user()->restaurants()->get()->pluck('id')->toArray();
        if (!in_array($request->restaurant_id, $restaurant_ids)) {
            return redirect()->back();
        }

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

            Storage::disk('public')->put(config('path.dishes').$filename, $photo);
            
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

            return redirect('restaurant-owner/dishes');
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function update_dish($id, DishRequest $request)
    {
        $restaurant_ids = Auth::user()->restaurants()->get()->pluck('id')->toArray();
        if (!in_array($request->restaurant_id, $restaurant_ids)) {
            return redirect()->back();
        }

        $dish = Dish::where([['id', $id],['restaurant_id', $request->restaurant_id]])->first();
        
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

                Storage::disk('public')->put(config('path.dishes').$filename, $photo);
            
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

                AddonDish::where('dish_id',$dish->id)->whereNotIn('addons_category_id', $request->addon_id ?? [])->delete();
                
                if(isset($request->addon_id)){
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
        return view('restaurant_owners.dishes_categories.index')->withCategories($categories);
    }

    public function create_dish_category()
    {
        return view('restaurant_owners.dishes_categories.create');
    }

    public function edit_dish_category($id)
    {
        $category = DishCategory::where('id', $id)->first();
        return view('restaurant_owners.dishes_categories.edit')->withCategory($category);
    }

    public function store_dish_category(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        $category = new DishCategory();
        $category->name = $request->name;

        if ($request->image != null) {
            $image = $request->file('image');
            $rand_name = time() . Str::random(12);
            $filename = $rand_name . '.jpg';
    
            $photo = Image::make($image)->fit(200, 200)->encode('jpg', 80);

            Storage::disk('public')->put(config('path.categories').$filename, $photo);
            
            $category->image = $filename;
        }

        if ($request->active == 'on') {
            $category->active = true;
        } else {
            $category->active = false;
        }

        try {
            $category->save();
            return redirect('restaurant-owner/dish_categories')->with(['success' => 'Saved Successfully!']);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function update_dish_category($id, Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        $category = DishCategory::where('id', $id)->first();
        
        if ($category) {
            $category->name = $request->name;

            if ($request->image != null) {
                $image = $request->file('image');
                $rand_name = time() . Str::random(12);
                $filename = $rand_name . '.jpg';
    
                $photo = Image::make($image)->fit(200, 200)->encode('jpg', 80);

                Storage::disk('public')->put(config('path.categories').$filename, $photo);
            
                $category->image = $filename;
            }

            if ($request->active == 'on') {
                $category->active = true;
            } else {
                $category->active = false;
            }
        
            try {
                $category->save();
                return redirect('restaurant-owner/dish_categories')->with(['success' => 'Saved Successfully!']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        }
    }

    public function addons()
    {
        $addons = Addon::with(['addons_category'])->where('user_id',Auth::user()->id)->get()->sortByDesc('id');
        return view('restaurant_owners.addons.index')->withAddons($addons);
    }

    public function create_addon()
    {
        $restaurants = Restaurant::all()->sortByDesc('id');
        $categories = AddonsCategory::where('user_id', Auth::user()->id)->get()->sortByDesc('id');
        return view('restaurant_owners.addons.create')->withCategories($categories);
    }

    public function edit_addon($id)
    {
        $addon = Addon::where([['id', $id],['user_id',Auth::user()->id]])->first();
        $categories = AddonsCategory::where('user_id', Auth::user()->id)->get()->sortByDesc('id');

        return view('restaurant_owners.addons.edit')->withAddon($addon)->withCategories($categories);
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
            return redirect('restaurant-owner/dish_addons');
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function update_addon($id, AddonRequest $request)
    {
        $addon = Addon::where([['id', $id],['user_id',Auth::user()->id]])->first();
        
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
        $addons = AddonsCategory::where('user_id',Auth::user()->id)->get()->sortByDesc('id');
        return view('restaurant_owners.addons_category.index')->withAddons($addons);
    }

    public function create_addons_category()
    {
        $types = ['SINGLE', 'MULTIPLE'];
        return view('restaurant_owners.addons_category.create')->withTypes($types);
    }

    public function edit_addons_category($id)
    {
        $addon = AddonsCategory::where([['id', $id],['user_id', Auth::user()->id]])->first();
        $types = ['SINGLE', 'MULTIPLE'];

        return view('restaurant_owners.addons_category.edit')->withAddon($addon)->withTypes($types);
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
            return redirect('restaurant-owner/dish_addons_categories');
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
}
