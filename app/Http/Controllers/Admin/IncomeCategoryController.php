<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeCategoryStoreRequest;
use App\Http\Requests\IncomeCategoryUpdateRequest;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class IncomeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Income Category List';
        $data['menu'] = 'income-category';
        return view('admin.IncomeCategory.index', $data);
    }

    /**
     * Get all income categories for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $incomeCategories = IncomeCategory::latest()->get();

            return response()->json([
                'success' => true,
                'data' => $incomeCategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch income categories'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param IncomeCategoryStoreRequest $request
     * @return JsonResponse
     */
    public function store(IncomeCategoryStoreRequest $request): JsonResponse
    {
        try {
            $incomeCategory = IncomeCategory::create([
                'name' => $request->validated()['name']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Income category created successfully',
                'data' => $incomeCategory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create income category'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param IncomeCategory $incomeCategory
     * @return JsonResponse
     */
    public function edit(IncomeCategory $incomeCategory): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $incomeCategory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Income category not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param IncomeCategoryUpdateRequest $request
     * @param IncomeCategory $incomeCategory
     * @return JsonResponse
     */
    public function update(IncomeCategoryUpdateRequest $request, IncomeCategory $incomeCategory): JsonResponse
    {
        try {
            $incomeCategory->update([
                'name' => $request->validated()['name']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Income category updated successfully',
                'data' => $incomeCategory->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update income category'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param IncomeCategory $incomeCategory
     * @return JsonResponse
     */
    public function destroy(IncomeCategory $incomeCategory): JsonResponse
    {
        try {
            $incomeCategory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Income category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete income category'
            ], 500);
        }
    }
}