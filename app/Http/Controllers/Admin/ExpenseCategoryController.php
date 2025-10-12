<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseCategoryStoreRequest;
use App\Http\Requests\ExpenseCategoryUpdateRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Expense Category List';
        $data['menu'] = 'expense-category';
        return view('admin.ExpenseCategory.index', $data);
    }

    /**
     * Get all expense categories for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $expenseCategories = ExpenseCategory::latest()->get();

            return response()->json([
                'success' => true,
                'data' => $expenseCategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expense categories'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ExpenseCategoryStoreRequest $request
     * @return JsonResponse
     */
    public function store(ExpenseCategoryStoreRequest $request): JsonResponse
    {
        try {
            $expenseCategory = ExpenseCategory::create([
                'name' => $request->validated()['name']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense category created successfully',
                'data' => $expenseCategory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create expense category'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ExpenseCategory $expenseCategory
     * @return JsonResponse
     */
    public function edit(ExpenseCategory $expenseCategory): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $expenseCategory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Expense category not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ExpenseCategoryUpdateRequest $request
     * @param ExpenseCategory $expenseCategory
     * @return JsonResponse
     */
    public function update(ExpenseCategoryUpdateRequest $request, ExpenseCategory $expenseCategory): JsonResponse
    {
        try {
            $expenseCategory->update([
                'name' => $request->validated()['name']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense category updated successfully',
                'data' => $expenseCategory->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update expense category'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param ExpenseCategory $expenseCategory
     * @return JsonResponse
     */
    public function destroy(ExpenseCategory $expenseCategory): JsonResponse
    {
        try {
            $expenseCategory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Expense category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete expense category'
            ], 500);
        }
    }
}
