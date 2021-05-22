<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ResponseHelper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class UserController extends Controller
{
    use ResponseHelper;

    public function save_token(Request $request)
    {
        // $request->validate([
        //     'fcm_token' => 'required|string'
        // ]);

        // $user = Auth::user();
        // $user->fcm_token = $request->fcm_token;
        // $user->save();

        return $this->successResponse("success");
    }

    public function notifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()->latest()->get();
        $user->notifications()->update(['read' => 1]);

        return $this->successResponse($notifications);
    }

    public function addresses()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->latest()->get();
        return $this->successResponse($addresses);
    }

    public function save_address(Request $request)
    {
        $request->validate([
            'label' => 'required|string',
            'full_address' => 'required|string',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $user = Auth::user();
        $user->addresses()->update(['is_primary' => 0]);
        $user->addresses()->create([
            'label' => $request->label,
            'given_name' => $user->name,
            'family_name' => $user->name,
            'street' => $request->full_address,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'is_primary' => true,
            'is_billing' => true,
            'is_shipping' => true,
        ]);
        $user->save();

        return $this->successResponse($user->addresses()->isPrimary()->first());
    }

    public function update_address(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $user = Auth::user();
        $user->addresses()->update(['is_primary' => 0]);
        $user->addresses()->where('id', $request->id)->update(['is_primary' => 1]);
        $user->save();

        return $this->successResponse($user->addresses()->isPrimary()->first());
    }

    public function delete_address(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $user = Auth::user();
        $user->addresses()->where('id', $request->id)->delete();

        return $this->successResponse("success");
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

        Storage::disk('public')->put(config('path.avatar') . $filename, $photo);

        $user->avatar = $filename;

        $user->save();

        return $this->successResponse($user->avatar);
    }
}