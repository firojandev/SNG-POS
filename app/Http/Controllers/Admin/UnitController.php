<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitStoreRequest;
use App\Http\Requests\UnitUpdateRequest;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:manage_unit');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Unit List';
        $data['menu'] = 'unit';
        return view('admin.Unit.index', $data);
    }

    /**
     * Get all units for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $units = Unit::latest()->get();

            return response()->json([
                'success' => true,
                'data' => $units
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch units'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UnitStoreRequest $request
     * @return JsonResponse
     */
    public function store(UnitStoreRequest $request): JsonResponse
    {
        try {
            $unit = Unit::create([
                'name' => $request->validated()['name']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully',
                'data' => $unit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create unit'
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Unit $unit
     * @return JsonResponse
     */
    public function edit(Unit $unit): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $unit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unit not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UnitUpdateRequest $request
     * @param Unit $unit
     * @return JsonResponse
     */
    public function update(UnitUpdateRequest $request, Unit $unit): JsonResponse
    {
        try {
            $unit->update([
                'name' => $request->validated()['name']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unit updated successfully',
                'data' => $unit->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update unit'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param Unit $unit
     * @return JsonResponse
     */
    public function destroy(Unit $unit): JsonResponse
    {
        try {
            $unit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unit deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete unit'
            ], 500);
        }
    }
}
