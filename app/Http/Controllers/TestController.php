<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class TestController extends Controller
{
    private $rules = [
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|max:30',
    ];

    private $messages = [
        'required' => 'The :attribute field is required.',
        'unique' => 'This email is already registered.',
        'email' => 'The :attribute field must be valid email.',
        'min' => 'The :attribute field minimum is :min character.',
        'max' => 'The :attribute field maximum is :max character.',
    ];

    public function register(Request $request)
    {
        //Request Validation
        $validator = Validator::make($request->all(), $this->rules, $this->messages);

        //Invalid Request Response
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Valid Request Response
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        //Success Response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user,
        ], 200);
    }

    public function login(Request $request)
    {
        //Define Request
        $newRequest = $request->only('email', 'password');

        //Set New Validation
        $rules = $this->rules;
        $rules['email'] = 'required|email';

        //Request Validation
        $validator = Validator::make($newRequest, $rules, $this->messages);

        //Invalid Request Response
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Creating Token
        try {
            if (!$token = JWTAuth::attempt($newRequest)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $newRequest;
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        //Define Request
        $newRequest = $request->only('token');

        //Set New Rules
        $rules = [
            'token' => 'required',
        ];

        //Request Validation
        $validator = Validator::make($newRequest, $rules, $this->messages);

        //Invalid Request Response
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Valid Request Response
        try {
            JWTAuth::invalidate($newRequest);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out',
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out',
            ], 500);
        }
    }
}
