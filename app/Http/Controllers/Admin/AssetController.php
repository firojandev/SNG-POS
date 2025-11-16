<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetStoreRequest;
use App\Http\Requests\AssetUpdateRequest;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AssetController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:manage_asset');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Asset List';
        $data['menu'] = 'asset';
        return view('admin.Asset.index', $data);
    }

    /**
     * Get all assets for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $assets = Asset::latest()->get();

            return response()->json([
                'success' => true,
                'data' => $assets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assets'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AssetStoreRequest $request
     * @return JsonResponse
     */
    public function store(AssetStoreRequest $request): JsonResponse
    {
        try {
            $asset = Asset::create([
                'name' => $request->validated()['name'],
                'amount' => $request->validated()['amount'],
                'note' => $request->validated()['note'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Asset created successfully',
                'data' => $asset
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create asset'
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Asset $asset
     * @return JsonResponse
     */
    public function edit(Asset $asset): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $asset
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AssetUpdateRequest $request
     * @param Asset $asset
     * @return JsonResponse
     */
    public function update(AssetUpdateRequest $request, Asset $asset): JsonResponse
    {
        try {
            $asset->update([
                'name' => $request->validated()['name'],
                'amount' => $request->validated()['amount'],
                'note' => $request->validated()['note'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Asset updated successfully',
                'data' => $asset->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update asset'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param Asset $asset
     * @return JsonResponse
     */
    public function destroy(Asset $asset): JsonResponse
    {
        try {
            $asset->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asset deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete asset'
            ], 500);
        }
    }
}
