<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// To use Personal Access Token
Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();
    if ($user) {
        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            return response()->json(['token' => $token]);
        } else {
            return response()->json(["message" => "Password mismatch"], 422);
        }
    } else {
        return response()->json(["message" => 'User does not exist'], 404);
    }
});

Route::middleware('auth:api')->get('/verify', function (Request $request) {
    if (!auth()->check())
        return response()->json(['valid' => false], 401);

    return response()->json(['valid' => true]);
});
