<?php
// app/Http/Controllers/TransactionController.php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
  // Fetch transactions by customer
  public function getTransactionsByCustomer($customerId)
  {
    // Load transactions related to the customer
    $customer = Customer::with('transactions')->find($customerId);

    if (!$customer) {
      return response()->json(['message' => 'Customer not found'], 404);
    }

    return response()->json($customer->transactions); // Return transactions for this customer
  }

  // Store a new transaction
  public function store(Request $request, $customerId)
  {
    $validator = Validator::make($request->all(), [
      'particulars' => 'nullable|string|max:255',
      'amount' => 'required|numeric',
      'type' => 'required|in:debit,credit',
      'datetime' => 'required|date',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Find customer and associate transaction
    $customer = Customer::find($customerId);
    if (!$customer) {
      return response()->json(['message' => 'Customer not found'], 404);
    }

    // Create transaction and associate with customer
    $transaction = new Transaction([
      'particulars' => $request->particulars,
      'amount' => $request->amount,
      'type' => $request->type,
      'datetime' => $request->datetime,
    ]);

    // Save the transaction for the customer
    $customer->transactions()->save($transaction);

    return response()->json($transaction, 201);
  }

  // Update a transaction
  public function update(Request $request, $transactionId)
  {
    $validator = Validator::make($request->all(), [
      'particulars' => 'nullable|string|max:255',
      'amount' => 'required|numeric',
      'type' => 'required|in:debit,credit',
      'datetime' => 'required|date',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    // Find transaction by ID
    $transaction = Transaction::find($transactionId);
    if (!$transaction) {
      return response()->json(['message' => 'Transaction not found'], 404);
    }

    // Update transaction fields
    $transaction->update($request->all());

    return response()->json($transaction);
  }

  // Delete a transaction
  public function destroy($transactionId)
  {
    $transaction = Transaction::find($transactionId);
    if (!$transaction) {
      return response()->json(['message' => 'Transaction not found'], 404);
    }

    // Delete the transaction
    $transaction->delete();

    return response()->json(['message' => 'Transaction deleted']);
  }
}
