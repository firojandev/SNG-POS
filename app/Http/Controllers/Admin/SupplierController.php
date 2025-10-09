<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $data['title'] = 'Suppliers';
        $data['menu'] = 'suppliers';
        return view('admin.Supplier.index', $data);
    }

    public function getData(): JsonResponse
    {
        $suppliers = Supplier::latest()->get();
        return response()->json(['success' => true, 'data' => $suppliers]);
    }

    public function store(SupplierStoreRequest $request): JsonResponse
    {
        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('suppliers', 'public');
            }

            $supplier = Supplier::create([
                'name' => $request->validated()['name'],
                'contact_person' => $request->validated()['contact_person'] ?? null,
                'phone' => $request->validated()['phone'],
                'email' => $request->validated()['email'] ?? null,
                'address' => $request->validated()['address'] ?? null,
                'about' => $request->validated()['about'] ?? null,
                'balance' => $request->validated()['balance'] ?? 0,
                'is_active' => $request->validated()['is_active'] ?? true,
                'photo' => $photoPath,
            ]);

            return response()->json(['success' => true, 'message' => 'Supplier created successfully', 'data' => $supplier]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create supplier'], 500);
        }
    }

    public function edit(Supplier $supplier): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $supplier]);
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier): JsonResponse
    {
        try {
            $update = [
                'name' => $request->validated()['name'],
                'contact_person' => $request->validated()['contact_person'] ?? null,
                'phone' => $request->validated()['phone'],
                'email' => $request->validated()['email'] ?? null,
                'address' => $request->validated()['address'] ?? null,
                'about' => $request->validated()['about'] ?? null,
                'balance' => $request->validated()['balance'] ?? 0,
                'is_active' => $request->validated()['is_active'] ?? $supplier->is_active,
            ];

            if ($request->hasFile('photo')) {
                if ($supplier->photo && Storage::disk('public')->exists($supplier->photo)) {
                    Storage::disk('public')->delete($supplier->photo);
                }
                $update['photo'] = $request->file('photo')->store('suppliers', 'public');
            }

            $supplier->update($update);

            return response()->json(['success' => true, 'message' => 'Supplier updated successfully', 'data' => $supplier->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update supplier'], 500);
        }
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        try {
            if ($supplier->photo && Storage::disk('public')->exists($supplier->photo)) {
                Storage::disk('public')->delete($supplier->photo);
            }
            $supplier->delete();
            return response()->json(['success' => true, 'message' => 'Supplier deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete supplier'], 500);
        }
    }
}


