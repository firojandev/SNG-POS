<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DebtStoreRequest;
use App\Http\Requests\DebtUpdateRequest;
use App\Http\Resources\DebtResource;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Debt List';
        $data['menu'] = 'debt';
        return view('admin.Debt.index', $data);
    }

    /**
     * Get all debts for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $debts = Debt::latest()->get();

            return response()->json([
                'success' => true,
                'data' => DebtResource::collection($debts)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch debts'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DebtStoreRequest $request
     * @return JsonResponse
     */
    public function store(DebtStoreRequest $request): JsonResponse
    {
        try {
            $debt = Debt::create([
                'lender' => $request->validated()['lender'],
                'date' => $request->validated()['date'],
                'amount' => $request->validated()['amount'],
                'note' => $request->validated()['note'] ?? null,
                'status' => $request->validated()['status'] ?? 'Unpaid'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Debt created successfully',
                'data' => $debt
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create debt'
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Debt $debt
     * @return JsonResponse
     */
    public function edit(Debt $debt): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new DebtResource($debt)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Debt not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DebtUpdateRequest $request
     * @param Debt $debt
     * @return JsonResponse
     */
    public function update(DebtUpdateRequest $request, Debt $debt): JsonResponse
    {
        try {
            $debt->update([
                'lender' => $request->validated()['lender'],
                'date' => $request->validated()['date'],
                'amount' => $request->validated()['amount'],
                'note' => $request->validated()['note'] ?? null,
                'status' => $request->validated()['status'] ?? 'Unpaid'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Debt updated successfully',
                'data' => $debt->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update debt'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param Debt $debt
     * @return JsonResponse
     */
    public function destroy(Debt $debt): JsonResponse
    {
        try {
            $debt->delete();

            return response()->json([
                'success' => true,
                'message' => 'Debt deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete debt'
            ], 500);
        }
    }
}
