<?php

namespace App\Http\Controllers\API;

use App\Coupon;
use App\Dish;
use App\Http\Controllers\Controller;
use App\Notification;
use App\Order;
use App\OrderDeliveryAssign;
use App\OrderDish;
use App\OrderDishAddon;
use App\Restaurant;
use App\Traits\ResponseHelper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Setting;

class OrderController extends Controller
{
    use ResponseHelper;

    public function orders()
    {
        $user = Auth::user();
        $orders = Order::with(['order_dishes', 'order_dishes.order_adddons'])->where('user_id', $user->id)->latest()->get();

        // $orders = Order::whereHas('order_dishes', function ($query) use ($restaurants_ids) {
        //     $query->whereIn('restaurant_id', $restaurants_ids)->where('active', 1);
        // })->where('active', 1)->inRandomOrder()->get(['id','name','image']);

        return $this->successResponse($orders);
    }

    public function place_order(Request $request)
    {
        $order = new Order();

        $random = Str::of(Str::orderedUuid())->upper()->explode('-');
        $order->unique_id = '#' . date('m-d') . '-' . $random[0] . '-' . $random[1];

        $restaurant_id = $request->restaurant_id;
        $order->restaurant_id = $restaurant_id;

        $user = Auth::user();
        $order->user_id = $user->id;

        $address = Auth::user()->addresses()->isPrimary()->first();
        $order->user_address = $address->street;
        $order->user_lat = $address->latitude;
        $order->user_long = $address->longitude;

        $restaurant = Restaurant::where('id', $restaurant_id)->first();
        $order->restaurant_charges = $restaurant->restaurant_charges;

        $orderTotal = 0;
        foreach ($request->dishes as $order_dish) {
            $order_dish = (object) $order_dish;
            $dish = Dish::where('id', $order_dish->id)->first();

            $addons_cost = 0;
            foreach ($order_dish->addons_dish as $addons_dish) {
                $addons_dish = (object) $addons_dish;
                foreach ($addons_dish->addons as $d) {
                    $addons_cost += (int) $d['price'];
                }

            }

            $orderTotal += ($dish->amount() * $order_dish->count) + $addons_cost;
        }

        if (Setting::get('delivery_charge_applicable')) {
            $order->delivery_charge = Setting::get('delivery_charge');
            $orderTotal = $orderTotal + Setting::get('delivery_charge');
        } else {
            $order->delivery_charge = 0;
        }

        $orderTotal = $orderTotal + $restaurant->restaurant_charges;

        if ($request->coupon) {
            $coupon_code = $request->coupon['coupon_code'];
            $coupon = Coupon::where('coupon_code', strtoupper($coupon_code))->first();
            if ($coupon) {
                $order->coupon_id = $coupon->id;
                if ($coupon->discount_type == 'PERCENTAGE') {
                    $coupon_discount_amount = (($coupon->discount / 100) * $orderTotal);
                }
                if ($coupon->discount_type == 'FIXED') {
                    $coupon_discount_amount = $coupon->discount;
                }
                $orderTotal = $orderTotal - $coupon_discount_amount;

                $order->coupon_discount = $coupon_discount_amount;

                $coupon->max_usage = $coupon->max_usage - 1;
                $coupon->save();
            }
        }

        if (Setting::get('tax_applicable')) {
            $tax_amount = (float) (((float) Setting::get('tax_percentage') / 100) * $orderTotal);
            $order->tax = $tax_amount;

            $orderTotal += $tax_amount;
        }

        $order->total_charges = $order->restaurant_charges + $order->delivery_charge + $order->tax;

        $order->total = $orderTotal;
        $order->payment_mode = $request->method;
        $order->status = "ORDER PLACED";
        $order->save();

        foreach ($request->dishes as $order_dish) {

            $order_dish = (object) $order_dish;
            $dish = Dish::where('id', $order_dish->id)->first();
            $orderDish = new OrderDish();
            $orderDish->order_id = $order->id;
            $orderDish->dish_id = $order_dish->id;
            $orderDish->name = $order_dish->name;
            $orderDish->image = $dish->image;
            $orderDish->description = $dish->description;
            $orderDish->quantity = $order_dish->count;
            $orderDish->price = $order_dish->price;
            $orderDish->is_veg = $order_dish->is_veg;
            $orderDish->save();

            $addons_cost = 0;
            foreach ($order_dish->addons_dish as $addons_dish) {
                $addons_dish = (object) $addons_dish;
                foreach ($addons_dish->addons as $d) {

                    $d = (object) $d;

                    $orderDishAddon = new OrderDishAddon();
                    $orderDishAddon->order_id = $order->id;
                    $orderDishAddon->dish_id = $order_dish->id;

                    $orderDishAddon->order_dishes_id = $orderDish->id;

                    $orderDishAddon->name = $d->name;
                    $orderDishAddon->price = $d->price;

                    $orderDishAddon->save();
                }
            }

        }

        Notification::create([
            'message' => 'Your order ' . $order->unique_id . ' is placed successfully',
            'user_id' => $order->user_id,
        ]);

        OrderDeliveryAssign::create([
            'order_id' => $order->id,
        ]);

        return $this->successResponse($order);
    }

    public function process_razorpay(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|string',
            'payment_amount' => 'required',
        ]);

        $payment_id = $request->payment_id;
        $payment_amount = $request->payment_amount;
        $api_key = config('settings.razorpay_api_public_key');
        $api_secret = config('settings.razorpay_api_secret_key');

        $api = new Api($api_key, $api_secret);

        try {
            $order = $api->order->create([
                'amount' => $payment_amount,
                'currency' => config('settings.currency_code'),
            ]);
            $payment = $api->payment->fetch($payment_id);
            $payment->capture(array('amount' => $payment_amount));
            $response = [
                'razorpay_success' => true,
            ];
            return $this->successResponse($response);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}