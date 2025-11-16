<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityMoneyStoreRequest;
use App\Http\Requests\SecurityMoneyUpdateRequest;
use App\Http\Resources\SecurityMoneyResource;
use App\Models\SecurityMoney;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SecurityMoneyController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:manage_security_money');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Security Money List';
        $data['menu'] = 'security-money';
        return view('admin.SecurityMoney.index', $data);
    }

    /**
     * Get all security money for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $securityMoney = SecurityMoney::latest()->get();

            return response()->json([
                'success' => true,
                'data' => SecurityMoneyResource::collection($securityMoney)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch security money'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SecurityMoneyStoreRequest $request
     * @return JsonResponse
     */
    public function store(SecurityMoneyStoreRequest $request): JsonResponse
    {
        try {
            $securityMoney = SecurityMoney::create([
                'receiver' => $request->validated()['receiver'],
                'date' => $request->validated()['date'],
                'amount' => $request->validated()['amount'],
                'note' => $request->validated()['note'] ?? null,
                'status' => $request->validated()['status'] ?? 'Paid'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Security money created successfully',
                'data' => $securityMoney
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create security money'
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param SecurityMoney $securityMoney
     * @return JsonResponse
     */
    public function edit(SecurityMoney $securityMoney): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new SecurityMoneyResource($securityMoney)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Security money not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SecurityMoneyUpdateRequest $request
     * @param SecurityMoney $securityMoney
     * @return JsonResponse
     */
    public function update(SecurityMoneyUpdateRequest $request, SecurityMoney $securityMoney): JsonResponse
    {
        try {
            $securityMoney->update([
                'receiver' => $request->validated()['receiver'],
                'date' => $request->validated()['date'],
                'amount' => $request->validated()['amount'],
                'note' => $request->validated()['note'] ?? null,
                'status' => $request->validated()['status'] ?? 'Paid'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Security money updated successfully',
                'data' => $securityMoney->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update security money'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param SecurityMoney $securityMoney
     * @return JsonResponse
     */
    public function destroy(SecurityMoney $securityMoney): JsonResponse
    {
        try {
            $securityMoney->delete();

            return response()->json([
                'success' => true,
                'message' => 'Security money deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete security money'
            ], 500);
        }
    }
}
