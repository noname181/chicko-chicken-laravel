<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;

use App\Traits\ResponseHelper;
use App\Traits\Utility;

use Image;
use Setting;

use App\User;
use App\Order;
use App\OrderDeliveryAssign;
use App\Notification;

class DeliveryScoutController extends Controller
{
    use ResponseHelper,Utility;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationFailed($validator->errors());
        }
        
        $user = User::where('email', $request->email)->orWhere('phone', $request->email)->get()->first();

        if ($user->hasRole('Delivery Scout') && $user && \Hash::check($request->password, $user->password)) {
            // $user->tokens()->delete();

            $auth_token = $user->createToken('authToken')->plainTextToken;

            $data = [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                ],
                'auth_token' => $auth_token ?? null,
                'currency_symbol' => Setting::get('currency_symbol')
            ];

            return $this->successResponse($data);
        } else {
            return $this->errorResponse(["Invalid Login Credentials!"]);
        }
    }

    public function live_orders()
    {
        $user = Auth::user();
        $orders = Order::with('order_dishes','order_dishes.order_adddons')->where('status', '!=', 'Delivered')->whereHas('order_delivery_assign', function ($query) {
            return $query->where('user_id', Auth::user()->id);
        })->latest()->get();

        $user = Auth::user();
        $notifications = $user->notifications()->where('read', 0)->count();
        
        $data = [
            'orders' => $orders,
            'notifications' => $notifications,
        ];

        return $this->successResponse($data);
    }

    public function past_orders()
    {
        $user = Auth::user();
        $orders = Order::with('order_dishes','order_dishes.order_adddons')->where('status', 'Delivered')->whereHas('order_delivery_assign', function ($query) {
            return $query->where('user_id', Auth::user()->id);
        })->latest()->get();
        return $this->successResponse($orders);
    }

    public function order_detail($id)
    {
        $order = Order::with('restaurant.addresses', 'user', 'order_dishes','order_dishes.order_adddons')->where('id', $id)
                    ->whereHas('order_delivery_assign', function ($query) {
                        return $query->where('user_id', Auth::user()->id);
                    })->first();
        if ($order) {
            return $this->successResponse($order);
        }
    }

    public function order_update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_status' => 'required|string',
        ]);
        
        $order = Order::with('restaurant.addresses', 'user', 'order_dishes')->where('id', $id)
                    ->whereHas('order_delivery_assign', function ($query) {
                        return $query->where('user_id', Auth::user()->id);
                    })->first();
        
        $acceptable_status = ['On the Way','Delivered'];
        if ($order && in_array($request->order_status, $acceptable_status)) {
            $message;
            switch ($request->order_status) {
                case 'On the Way':
                    $message = 'Your order '.$order->unique_id.' is on the way. Be ready';
                    break;
                case 'Delivered':
                    $message = 'Your order '.$order->unique_id.' is delivered successfully';
            }

            $order->status = $request->order_status;

            Notification::create([
                'message' => $message,
                'user_id' => $order->user_id
            ]);
            $this->sendFCM($message, $order->user_id);

            try {
                $order->save();
                return $this->successResponse('Updated Successfully!');
            } catch (Exception $e) {
                return $this->errorResponse(["Something went wrong!"]);
            }
        } else {
            return $this->errorResponse(["Invalid Details!"]);
        }
    }
    
    public function update_position(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        OrderDeliveryAssign::where('user_id', Auth::user()->id)
                ->update([
                    'scout_lat'=>$request->latitude,
                    'scout_long'=>$request->longitude
                ]);

        return $this->successResponse("success");
    }
    
    public function save_token(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = Auth::user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return $this->successResponse("success");
    }

    public function notifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()->latest()->get();
        $user->notifications()->update(['read' => 1]);

        return $this->successResponse($notifications);
    }

    public function update_profile_img(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
        ]);

        $user = Auth::user();

        $image = $request->file('image');
        $rand_name = time() . Str::random(12);
        $filename = $rand_name . '.jpg';
        
        $photo = Image::make($image)->fit(300, 300)->encode('jpg', 80);
    
        Storage::disk('public')->put(config('path.avatar').$filename, $photo);
                
        $user->avatar = $filename;
                
        $user->save();

        return $this->successResponse($user->avatar);
    }
}
