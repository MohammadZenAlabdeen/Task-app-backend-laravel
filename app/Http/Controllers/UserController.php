<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'email',
            'password' => 'min:8',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user || Hash::check(Hash::make($validated['password']), $user->password)) {
            return response()->json(['status' => 'failed', 'message' => 'no user with these credentials exists or the password is incorrect'], 404);
        }
        $token = $user->createToken($user->name)->plainTextToken;
        return response()->json([
            'status' => 'success',
            'message' => 'user loged in succesfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        if (User::where('email', $request->input('email'))->first()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'this email is used',
            ], 400);
        }
        $validated = $request->validate([
            'name' => 'string|required',
            'email' => 'email|unique:users,email',
            'password' => 'min:8|confirmed',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();
        $token = $user->createToken($validated['name'])->plainTextToken;
        $response = [
            'message' => 'user created succesfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ];
        auth()->attempt($validated);
        return response()->json($response, 201);
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'user is logged out successfully'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
