<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\StoreUpdateRequest;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:admin_permission');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Store List';
        $data['menu'] = 'store';
        return view('admin.Store.index', $data);
    }

    /**
     * Get all stores for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $stores = Store::latest()->get();

            return response()->json([
                'success' => true,
                'data' => $stores
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stores'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreStoreRequest $request): JsonResponse
    {
        try {
            $store = Store::create([
                'name' => $request->validated()['name'],
                'contact_person' => $request->validated()['contact_person'],
                'phone_number' => $request->validated()['phone_number'],
                'address' => $request->validated()['address'],
                'email' => $request->validated()['email'],
                'details' => $request->validated()['details'] ?? null,
                'is_active' => $request->validated()['is_active'] ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Store created successfully',
                'data' => $store
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create store'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Store $store
     * @return JsonResponse
     */
    public function edit(Store $store): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $store
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreUpdateRequest $request
     * @param Store $store
     * @return JsonResponse
     */
    public function update(StoreUpdateRequest $request, Store $store): JsonResponse
    {
        try {
            $store->update([
                'name' => $request->validated()['name'],
                'contact_person' => $request->validated()['contact_person'],
                'phone_number' => $request->validated()['phone_number'],
                'address' => $request->validated()['address'],
                'email' => $request->validated()['email'],
                'details' => $request->validated()['details'] ?? null,
                'is_active' => $request->validated()['is_active'] ?? $store->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Store updated successfully',
                'data' => $store->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update store'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param Store $store
     * @return JsonResponse
     */
    public function destroy(Store $store): JsonResponse
    {
        try {
            $store->delete();

            return response()->json([
                'success' => true,
                'message' => 'Store deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete store'
            ], 500);
        }
    }
}
