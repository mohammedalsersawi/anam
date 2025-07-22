<?php

namespace App\Http\Controllers\Api\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'name'      => 'required|string|max:12|unique:admins,name',
            'email'     => 'required|email|unique:admins,email',
            'gender'    => 'required|in:0,1', // 0 = ذكر، 1 = أنثى
            'password'  => 'required|string|min:6|confirmed',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return mainResponse(
                false,
                $validator->errors()->first(),
                [],
                $validator->errors()->messages(),
                422
            );
        }
        $birth_date = Carbon::createFromFormat('d/m/Y', $data['birth_date'])->format('Y-m-d');
        $admin = Admin::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'gender'    => $data['gender'],
            'password'  => Hash::make($data['password']),
        ]);
        $token = $admin->createToken('api')->plainTextToken;
        return mainResponse(true, 'Admin created successfully', compact('token'), [], 101);
    }


    public function login(Request $request)
    {
        $rules = [
            'email'    => 'required|email|exists:admins,email',
            'password' => 'required|string|min:6',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return mainResponse(
                false,
                $validator->errors()->first(),
                [],
                $validator->errors()->messages(),
                422
            );
        }
        $admin = Admin::where('email', $data['email'])->first();
        if (!Hash::check($data['password'], $admin->password)) {
            return mainResponse(false, 'The password is incorrect', [], [], 401);
        }
        $token = $admin->createToken('api')->plainTextToken;
        return mainResponse(true, 'Admin created successfully', compact('token'), [], 101);
    }



public function logout(Request $request)
{
    /** @var \App\Models\Admin|null $admin */
    $admin = auth()->guard('admin')->user();

    /** @var PersonalAccessToken|null $token */
    $token = $admin?->currentAccessToken();

    if ($token) {
        $token->delete();
        return mainResponse(true, 'You have successfully logged out', [], [], 200);
    }

    return mainResponse(false, 'Admin is not logged in', [], [], 401);
}

}
