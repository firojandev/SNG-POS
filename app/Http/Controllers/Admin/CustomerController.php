<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $data['title'] = 'Customers';
        $data['menu'] = 'customers';
        return view('admin.Customer.index', $data);
    }

    public function getData(): JsonResponse
    {
        $customers = Customer::latest()->get();
        return response()->json(['success' => true, 'data' => $customers]);
    }

    public function store(CustomerStoreRequest $request): JsonResponse
    {
        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('customers', 'public');
            }

            $customer = Customer::create([
                'name' => $request->validated()['name'],
                'phone' => $request->validated()['phone'],
                'email' => $request->validated()['email'] ?? null,
                'address' => $request->validated()['address'] ?? null,
                'is_active' => $request->validated()['is_active'] ?? true,
                'photo' => $photoPath,
            ]);

            return response()->json(['success' => true, 'message' => 'Customer created successfully', 'data' => $customer]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create customer'], 500);
        }
    }

    public function edit(Customer $customer): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $customer]);
    }

    public function update(CustomerUpdateRequest $request, Customer $customer): JsonResponse
    {
        try {
            $update = [
                'name' => $request->validated()['name'],
                'phone' => $request->validated()['phone'],
                'email' => $request->validated()['email'] ?? null,
                'address' => $request->validated()['address'] ?? null,
                'is_active' => $request->validated()['is_active'] ?? $customer->is_active,
            ];

            if ($request->hasFile('photo')) {
                if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
                    Storage::disk('public')->delete($customer->photo);
                }
                $update['photo'] = $request->file('photo')->store('customers', 'public');
            }

            $customer->update($update);

            return response()->json(['success' => true, 'message' => 'Customer updated successfully', 'data' => $customer->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update customer'], 500);
        }
    }

    public function destroy(Customer $customer): JsonResponse
    {
        try {
            if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
                Storage::disk('public')->delete($customer->photo);
            }
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete customer'], 500);
        }
    }
}