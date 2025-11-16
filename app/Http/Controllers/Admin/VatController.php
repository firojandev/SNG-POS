<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VatStoreRequest;
use App\Http\Requests\VatUpdateRequest;
use App\Models\Vat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class VatController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:manage_vat');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'VAT List';
        $data['menu'] = 'vat';
        return view('admin.Vat.index', $data);
    }

    /**
     * Get all vats for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $vats = Vat::latest()->get();

            return response()->json([
                'success' => true,
                'data' => $vats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch VATs'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VatStoreRequest $request
     * @return JsonResponse
     */
    public function store(VatStoreRequest $request): JsonResponse
    {
        try {
            $vat = Vat::create([
                'name' => $request->validated()['name'],
                'value' => $request->validated()['value']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'VAT created successfully',
                'data' => $vat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create VAT'
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Vat $vat
     * @return JsonResponse
     */
    public function edit(Vat $vat): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $vat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'VAT not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param VatUpdateRequest $request
     * @param Vat $vat
     * @return JsonResponse
     */
    public function update(VatUpdateRequest $request, Vat $vat): JsonResponse
    {
        try {
            $vat->update([
                'name' => $request->validated()['name'],
                'value' => $request->validated()['value']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'VAT updated successfully',
                'data' => $vat->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update VAT'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param Vat $vat
     * @return JsonResponse
     */
    public function destroy(Vat $vat): JsonResponse
    {
        try {
            $vat->delete();

            return response()->json([
                'success' => true,
                'message' => 'VAT deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete VAT'
            ], 500);
        }
    }
}
