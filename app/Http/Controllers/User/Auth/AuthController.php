<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use App\Mail\SendEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\sendResetLinkEmail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'name'      => 'required|string|max:12|unique:users,name',
            'email'     => 'required|email|unique:users,email',
            'gender'    => 'required|in:0,1', // 0 = ذكر، 1 = أنثى
            'birth_date' => 'required|date_format:d/m/Y|before:today',
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
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'gender'    => $data['gender'],
            'birth_date' => $birth_date,
            'password'  => Hash::make($data['password']),
        ]);
        $token = $user->createToken('api')->plainTextToken;
        return mainResponse(true, 'User created successfully', compact('token'), [], 101);
    }


    public function login(Request $request)
    {
        $rules = [
            'email'    => 'required|email|exists:users,email',
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
        $user = User::where('email', $data['email'])->first();
        if (!Hash::check($data['password'], $user->password)) {
            return mainResponse(false, 'The password is incorrect', [], [], 401);
        }
        $token = $user->createToken('api')->plainTextToken;
        return mainResponse(true, 'User created successfully', compact('token'), [], 101);
    }


    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete(); // حذف التوكن الحالي فقط
            return mainResponse(true, 'You have successfully logged out', [], [], 200);
        }

        return mainResponse(false, 'User is not logged in', [], [], 401);
    }


    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);
            $user = User::where('email', $request->email)->first();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            $rawToken = Str::random(32);
            $hashedToken = Hash::make($rawToken);
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => $hashedToken,
                'created_at' => now(),
            ]);
            $resetLink = url('/reset-password?token=' . urlencode($rawToken) . '&email=' . urlencode($user->email));
            $data = [
                'subject' => 'Password Reset Request',
                'message' => 'We received a request to reset your account password. Please click the button below to reset it.',
                'reset_link' => $resetLink,
            ];
            Mail::to($user->email)->send(new sendResetLinkEmail($data));
            return response()->json(['message' => 'A password reset link has been sent to your email address.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record || !Hash::check($token, $record->token)) {
            return response()->json(['error' => 'Invalid or expired token.'], 400);
        }
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        return response()->json(['message' => 'Password has been reset successfully.']);
    }
}
