<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeStoreRequest;
use App\Http\Requests\IncomeUpdateRequest;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Http\Resources\IncomeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $data['title'] = 'Income List';
        $data['menu'] = 'income';
        $data['categories'] = IncomeCategory::orderBy('name')->get(['id','name']);
        return view('admin.Income.index', $data);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(IncomeStoreRequest $request): JsonResponse
    {
        try {
            $income = Income::create($request->validated());
            $income->load('category');
            return response()->json(['success' => true, 'message' => 'Income created successfully', 'data' => new IncomeResource($income)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create income'], 500);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income): JsonResponse
    {
        try {
            $income->load('category');
            return response()->json(['success' => true, 'data' => new IncomeResource($income)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Income not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IncomeUpdateRequest $request, Income $income): JsonResponse
    {
        try {
            $income->update($request->validated());
            $income->refresh()->load('category');
            return response()->json(['success' => true, 'message' => 'Income updated successfully', 'data' => new IncomeResource($income)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update income'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income): JsonResponse
    {
        try {
            $income->delete();
            return response()->json(['success' => true, 'message' => 'Income deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete income'], 500);
        }
    }

    /**
     * Get all incomes for DataTable.
     */
    public function getData(): JsonResponse
    {
        try {
            $incomes = Income::with('category')->latest()->get();
            return response()->json(['success' => true, 'data' => IncomeResource::collection($incomes)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch incomes'], 500);
        }
    }
}
