<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LendStoreRequest;
use App\Http\Requests\LendUpdateRequest;
use App\Http\Resources\LendResource;
use App\Models\Lend;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class LendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Lend List';
        $data['menu'] = 'lend';
        return view('admin.Lend.index', $data);
    }

    /**
     * Get all lends for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $lends = Lend::latest()->get();

            return response()->json([
                'success' => true,
                'data' => LendResource::collection($lends)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lends'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LendStoreRequest $request
     * @return JsonResponse
     */
    public function store(LendStoreRequest $request): JsonResponse
    {
        try {
            $lend = Lend::create([
                'borrower' => $request->validated()['borrower'],
                'date' => $request->validated()['date'],
                'amount' => $request->validated()['amount'],
                'note' => $request->validated()['note'] ?? null,
                'status' => $request->validated()['status'] ?? 'Due'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lend created successfully',
                'data' => $lend
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lend'
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Lend $lend
     * @return JsonResponse
     */
    public function edit(Lend $lend): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new LendResource($lend)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lend not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LendUpdateRequest $request
     * @param Lend $lend
     * @return JsonResponse
     */
    public function update(LendUpdateRequest $request, Lend $lend): JsonResponse
    {
        try {
            $lend->update([
                'borrower' => $request->validated()['borrower'],
                'date' => $request->validated()['date'],
                'amount' => $request->validated()['amount'],
                'note' => $request->validated()['note'] ?? null,
                'status' => $request->validated()['status'] ?? 'Due'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lend updated successfully',
                'data' => $lend->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lend'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param Lend $lend
     * @return JsonResponse
     */
    public function destroy(Lend $lend): JsonResponse
    {
        try {
            $lend->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lend deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lend'
            ], 500);
        }
    }
}
