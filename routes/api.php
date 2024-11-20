<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
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

    // Customer Routes
    // Additional routes for soft delete functionality
    Route::patch('/customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
    Route::delete('/customers/{id}/force', [CustomerController::class, 'forceDelete'])->name('customers.forceDelete');
    // Customer Routes
    Route::apiResource('customers', CustomerController::class);

    // Transaction Routes
    // Additional routes for soft delete functionality
    Route::patch('/transactions/{id}/restore', [TransactionController::class, 'restore'])->name('transactions.restore');
    Route::delete('/transactions/{id}/force', [TransactionController::class, 'forceDelete'])->name('transactions.forceDelete');
    // Transaction Routes
    Route::apiResource('transactions', TransactionController::class);


    // perform operation with customer_id
    // Get all transactions for a specific customer
    Route::get('customer/{customerId}/transactions', [TransactionController::class, 'getTransactionsByCustomer']);

    // Store a new transaction for a specific customer
    Route::post('customer/{customerId}/transaction', [TransactionController::class, 'store']);

    // Update an existing transaction
    Route::put('transaction/{transactionId}', [TransactionController::class, 'update']);

    // Delete a transaction
    Route::delete('transaction/{transactionId}', [TransactionController::class, 'destroy']);

});
