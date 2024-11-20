<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Get all customers (including optional filter for active/inactive)
    public function index(Request $request)
    {
        $isActive = $request->query('is_active'); // Optional filter
        $customers = Customer::when($isActive !== null, function ($query) use ($isActive) {
            return $query->where('is_active', $isActive);
        })->get();

        return response()->json($customers);
    }

    // Get a single customer by ID
    public function show($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json($customer);
    }

    // Create a new customer
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'mobile' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'ledger_number' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $customer = Customer::create($request->all());

        return response()->json(['message' => 'Customer created successfully', 'customer' => $customer], 201);
    }

    // Update a customer
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'mobile' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'ledger_number' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $customer->update($request->all());

        return response()->json(['message' => 'Customer updated successfully', 'customer' => $customer]);
    }

    // Soft delete a customer
    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }

    // Restore a soft-deleted customer
    public function restore($id)
    {
        $customer = Customer::onlyTrashed()->find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found in deleted records'], 404);
        }

        $customer->restore();

        return response()->json(['message' => 'Customer restored successfully', 'customer' => $customer]);
    }

    // Permanently delete a customer
    public function forceDelete($id)
    {
        $customer = Customer::onlyTrashed()->find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found in deleted records'], 404);
        }

        $customer->forceDelete();

        return response()->json(['message' => 'Customer permanently deleted']);
    }
}
