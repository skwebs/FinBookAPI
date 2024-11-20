<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Get all transactions (optional filter by customer)
    public function index(Request $request)
    {
        $customerId = $request->query('customer_id'); // Optional filter for customer-specific transactions
        $transactions = Transaction::when($customerId, function ($query) use ($customerId) {
            return $query->where('customer_id', $customerId);
        })->orderBy('datetime', 'desc')->get();

        return response()->json($transactions);
    }

    // Get a specific transaction
    public function show($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    // Create a new transaction
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'particulars' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:debit,credit',
            'datetime' => 'required|date',
        ]);

        $transaction = Transaction::create($request->all());

        return response()->json(['message' => 'Transaction created successfully', 'transaction' => $transaction], 201);
    }

    // Update a transaction
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'particulars' => 'nullable|string',
            'amount' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|in:debit,credit',
            'datetime' => 'sometimes|date',
        ]);

        $transaction->update($request->all());

        return response()->json(['message' => 'Transaction updated successfully', 'transaction' => $transaction]);
    }

    // Soft delete a transaction
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }

    // Restore a soft-deleted transaction
    public function restore($id)
    {
        $transaction = Transaction::onlyTrashed()->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found in deleted records'], 404);
        }

        $transaction->restore();

        return response()->json(['message' => 'Transaction restored successfully', 'transaction' => $transaction]);
    }

    // Permanently delete a transaction
    public function forceDelete($id)
    {
        $transaction = Transaction::onlyTrashed()->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found in deleted records'], 404);
        }

        $transaction->forceDelete();

        return response()->json(['message' => 'Transaction permanently deleted']);
    }
}
