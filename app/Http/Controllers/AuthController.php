<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validatedRequest = $request->validate([
            'email' => ["required", "email", "exists:users"],
            'password' => 'required',
        ], [
            "email.exists" => "The provided email is not registered with us"
        ]);

        $user = User::where("email", $validatedRequest["email"])->first();

        if ($user && Hash::check($validatedRequest["password"], $user->password)) {

            $token = $user->createToken($validatedRequest["email"])->plainTextToken;

            return response()->json(["message" => "login successfull", "user" => $user, "token" => $token], 200);
        } else {
            return response()->json(["message" => "Invalid credentials"], 404);
        }

    }

    public function register(Request $request)
    {
        $validatedRequest = $request->validate([
            "name" => ["required", "max:256"],
            "email" => ["required", "email"],
            "password" => ["required", "min:4"]
        ]);

        User::create($validatedRequest);

        return response()->json([
            "message" => "Account Created Successfully"
        ]);
    }


}
