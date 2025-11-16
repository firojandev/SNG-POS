<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxStoreRequest;
use App\Http\Requests\TaxUpdateRequest;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TaxController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:manage_tax');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Tax List';
        $data['menu'] = 'tax';
        return view('admin.Tax.index', $data);
    }

    /**
     * Get all taxes for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $taxes = Tax::latest()->get();

            return response()->json([
                'success' => true,
                'data' => $taxes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch taxes'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaxStoreRequest $request
     * @return JsonResponse
     */
    public function store(TaxStoreRequest $request): JsonResponse
    {
        try {
            $tax = Tax::create([
                'name' => $request->validated()['name'],
                'value' => $request->validated()['value']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax created successfully',
                'data' => $tax
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tax'
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Tax $tax
     * @return JsonResponse
     */
    public function edit(Tax $tax): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $tax
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tax not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TaxUpdateRequest $request
     * @param Tax $tax
     * @return JsonResponse
     */
    public function update(TaxUpdateRequest $request, Tax $tax): JsonResponse
    {
        try {
            $tax->update([
                'name' => $request->validated()['name'],
                'value' => $request->validated()['value']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tax updated successfully',
                'data' => $tax->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tax'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param Tax $tax
     * @return JsonResponse
     */
    public function destroy(Tax $tax): JsonResponse
    {
        try {
            $tax->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tax deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tax'
            ], 500);
        }
    }
}