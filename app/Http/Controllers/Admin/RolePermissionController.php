<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of roles.
     *
     * @return View
     */
    public function index(): View
    {
        $data['title'] = 'Role & Permission Management';
        $data['menu'] = 'roles';
        return view('admin.Roles.index', $data);
    }

    /**
     * Get all roles for DataTable.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        try {
            $roles = Role::with('permissions')->latest()->get();

            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch roles'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new role.
     *
     * @return View
     */
    public function create(): View
    {
        $data['title'] = 'Create New Role';
        $data['menu'] = 'roles';

        // Get all permissions grouped by module
        $permissions = Permission::all();
        $data['groupedPermissions'] = $this->groupPermissions($permissions);

        return view('admin.Roles.create', $data);
    }

    /**
     * Store a newly created role in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            // Assign permissions to role
            if ($request->has('permissions') && is_array($request->permissions)) {
                // Get Permission models by IDs
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param Role $role
     * @return View
     */
    public function edit(Role $role): View
    {
        $data['title'] = 'Edit Role';
        $data['menu'] = 'roles';
        $data['role'] = $role->load('permissions');

        // Get all permissions grouped by module
        $permissions = Permission::all();
        $data['groupedPermissions'] = $this->groupPermissions($permissions);

        return view('admin.Roles.edit', $data);
    }

    /**
     * Update the specified role in storage.
     *
     * @param Request $request
     * @param Role $role
     * @return JsonResponse
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $role->update([
                'name' => $request->name
            ]);

            // Sync permissions
            if ($request->has('permissions') && is_array($request->permissions)) {
                // Get Permission models by IDs
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role from storage.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            // Prevent deleting Admin role
            if ($role->name === 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete Admin role'
                ], 403);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role'
            ], 500);
        }
    }

    /**
     * Group permissions by category.
     *
     * @param $permissions
     * @return array
     */
    private function groupPermissions($permissions): array
    {
        // Define permission categories matching the new structure
        $categories = [
            'Dashboard & Core' => [
                'view_dashboard',
                'manage_supplier',
                'manage_customer'
            ],
            'Product Management' => [
                'manage_category', 'manage_unit', 'manage_tax', 'manage_vat', 'manage_product'
            ],
            'Purchase & Sales' => ['manage_purchase', 'manage_sale', 'manage_payment'],
            'Financial Management' => [
                'manage_income', 'manage_expense', 'manage_debt',
                'manage_asset', 'manage_lend', 'manage_security_money'
            ],
            'Reports' => [
                'view_purchase_reports', 'view_sale_reports',
                'view_stock_reports', 'view_revenue_reports', 'view_balance_sheet'
            ],
            'Administration' => ['admin_permission', 'manage_staff'],
        ];

        $grouped = [];

        foreach ($categories as $categoryName => $permissionNames) {
            foreach ($permissions as $permission) {
                if (in_array($permission->name, $permissionNames)) {
                    if (!isset($grouped[$categoryName])) {
                        $grouped[$categoryName] = [];
                    }
                    $grouped[$categoryName][] = $permission;
                }
            }
        }

        return $grouped;
    }
}
