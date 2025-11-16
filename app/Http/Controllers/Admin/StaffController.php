<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffStoreRequest;
use App\Http\Requests\StaffUpdateRequest;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Apply permission middleware
     */
    public function __construct()
    {
        $this->middleware('permission:manage_staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Staff List';
        $data['menu'] = 'staff';
        $data['stores'] = Store::active()->select('id', 'name')->get();
        $data['roles'] = Role::all();
        return view('admin.Staff.index', $data);
    }

    /**
     * Get all staff for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $staff = User::with(['store', 'roles'])->latest()->get();

            return response()->json([
                'success' => true,
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff'
            ], 500);
        }
    }

    /**
     * Get all stores for dropdown.
     *
     * @return JsonResponse
     */
    public function getStores(): JsonResponse
    {
        try {
            $stores = Store::active()->select('id', 'name')->get();

            return response()->json([
                'success' => true,
                'data' => $stores
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stores'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StaffStoreRequest $request
     * @return JsonResponse
     */
    public function store(StaffStoreRequest $request): JsonResponse
    {
        try {
            $avatarPath = null;

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $staff = User::create([
                'name' => $request->validated()['name'],
                'email' => $request->validated()['email'],
                'password' => Hash::make($request->validated()['password']),
                'phone' => $request->validated()['phone'] ?? null,
                'designation' => $request->validated()['designation'] ?? null,
                'address' => $request->validated()['address'] ?? null,
                'store_id' => $request->validated()['store_id'],
                'avatar' => $avatarPath
            ]);

            // Assign role to staff
            $role = Role::find($request->validated()['role_id']);
            if ($role) {
                $staff->assignRole($role);
            }

            // Load the store and roles relationships for response
            $staff->load(['store', 'roles']);

            return response()->json([
                'success' => true,
                'message' => 'Staff created successfully',
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create staff: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $staff
     * @return JsonResponse
     */
    public function edit(User $staff): JsonResponse
    {
        try {
            $staff->load(['store', 'roles']);

            return response()->json([
                'success' => true,
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StaffUpdateRequest $request
     * @param User $staff
     * @return JsonResponse
     */
    public function update(StaffUpdateRequest $request, User $staff): JsonResponse
    {
        try {
            $updateData = [
                'name' => $request->validated()['name'],
                'email' => $request->validated()['email'],
                'phone' => $request->validated()['phone'] ?? null,
                'designation' => $request->validated()['designation'] ?? null,
                'address' => $request->validated()['address'] ?? null,
                'store_id' => $request->validated()['store_id']
            ];

            // Only update password if provided
            if (!empty($request->validated()['password'])) {
                $updateData['password'] = Hash::make($request->validated()['password']);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($staff->avatar && Storage::disk('public')->exists($staff->avatar)) {
                    Storage::disk('public')->delete($staff->avatar);
                }

                // Store new avatar
                $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $staff->update($updateData);

            // Update role
            $role = Role::find($request->validated()['role_id']);
            if ($role) {
                $staff->syncRoles([$role]);
            }

            // Load the store and roles relationships for response
            $staff->load(['store', 'roles']);

            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully',
                'data' => $staff->fresh(['store', 'roles'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update staff: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $staff
     * @return JsonResponse
     */
    public function destroy(User $staff): JsonResponse
    {
        try {
            // Delete avatar file if exists
            if ($staff->avatar && Storage::disk('public')->exists($staff->avatar)) {
                Storage::disk('public')->delete($staff->avatar);
            }

            $staff->delete();

            return response()->json([
                'success' => true,
                'message' => 'Staff deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete staff'
            ], 500);
        }
    }
}
