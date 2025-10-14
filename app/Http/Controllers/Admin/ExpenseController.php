<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Http\Resources\ExpenseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $data['title'] = 'Expense List';
        $data['menu'] = 'expense';
        $data['categories'] = ExpenseCategory::orderBy('name')->get(['id','name']);
        return view('admin.Expense.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseStoreRequest $request): JsonResponse
    {
        try {
            $expense = Expense::create($request->validated());
            $expense->load('category');
            return response()->json(['success' => true, 'message' => 'Expense created successfully', 'data' => new ExpenseResource($expense)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create expense'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense): JsonResponse
    {
        try {
            $expense->load('category');
            return response()->json(['success' => true, 'data' => new ExpenseResource($expense)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Expense not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExpenseUpdateRequest $request, Expense $expense): JsonResponse
    {
        try {
            $expense->update($request->validated());
            $expense->refresh()->load('category');
            return response()->json(['success' => true, 'message' => 'Expense updated successfully', 'data' => new ExpenseResource($expense)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update expense'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense): JsonResponse
    {
        try {
            $expense->delete();
            return response()->json(['success' => true, 'message' => 'Expense deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete expense'], 500);
        }
    }

    /**
     * Get all expenses for DataTable.
     */
    public function getData(): JsonResponse
    {
        try {
            $expenses = Expense::with('category')->latest()->get();
            return response()->json(['success' => true, 'data' => ExpenseResource::collection($expenses)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch expenses'], 500);
        }
    }
}
