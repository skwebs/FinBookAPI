<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post("/login", function (Request $request) {
    $r = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
        'device_name' => ['required']
    ]);

    // return $r;


    // if (!Auth::attempt($request->only('email', 'password'))) {
    //     throw ValidationException::withMessages([
    //         'email' => ['The provided credentials are incorrect.'],
    //     ]);
    // }

    // return response()->json(['token' => $request->user()->createToken($request->deviceName . -'API_Token')->plainTextToken]);


    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages(['email' => 'The provided credential are incorrect.']);
    }
    // return ;
    $token = $user->createToken($request->device_name)->plainTextToken;
    return response()->json([
        'token' => $token
    ]);
});

Route::post('register', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'max:255', 'email', 'unique:' . User::class],
        'password' => ['required', 'confirmed', Password::defaults()],
        'password_confirmation' => ['required'],
        'device_name' => ['required']
    ]);


    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => $request->password,
        // 'password'=>$request->password,
    ]);

    event(new Registered($user));

    return response()->json([
        'token' => $user->createToken($request->device_name)->plainTextToken
    ]);

});


Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->noContent();
    });
});
