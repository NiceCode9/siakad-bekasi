<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $roles = Role::withCount('permissions', 'users')->get();

            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('permissions_count', function ($role) {
                    return $role->permissions_count;
                })
                ->addColumn('users_count', function ($role) {
                    return $role->users_count;
                })
                ->addColumn('permissions_list', function ($role) {
                    $permissions = $role->permissions->take(3)->pluck('name')->toArray();
                    $count = $role->permissions->count();

                    if ($count > 3) {
                        $list = implode(', ', $permissions) . ' <span class="badge badge-info">+' . ($count - 3) . ' lainnya</span>';
                    } else {
                        $list = $permissions ? implode(', ', $permissions) : '-';
                    }

                    return $list;
                })
                ->addColumn('guard', function ($role) {
                    return '<span class="badge badge-secondary">' . $role->guard_name . '</span>';
                })
                ->addColumn('action', function ($role) {
                    $editBtn = '<button type="button" class="btn btn-sm btn-primary btn-edit" data-id="' . $role->id . '" title="Edit">
                                    <i class="simple-icon-pencil"></i>
                                </button>';

                    $permissionBtn = '<button type="button" class="btn btn-sm btn-info btn-assign-permission" data-id="' . $role->id . '" title="Assign Permission">
                                        <i class="simple-icon-lock"></i>
                                      </button>';

                    $deleteBtn = '';
                    // Prevent deleting super-admin role
                    if ($role->name !== 'super-admin') {
                        $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $role->id . '" title="Delete">
                                        <i class="simple-icon-trash"></i>
                                      </button>';
                    }

                    return '<div class="btn-group" role="group">' . $editBtn . $permissionBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['permissions_list', 'guard', 'action'])
                ->make(true);
        }

        return view('admin.role.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'nullable|string|max:255',
        ]);

        $validated['guard_name'] = $validated['guard_name'] ?? 'web';

        $role = Role::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil ditambahkan',
            'data' => $role
        ]);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Prevent updating super-admin role name
        if ($role->name === 'super-admin' && $request->name !== 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'Role super-admin tidak dapat diubah namanya'
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'guard_name' => 'nullable|string|max:255',
        ]);

        $validated['guard_name'] = $validated['guard_name'] ?? 'web';

        $role->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diupdate',
            'data' => $role
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deleting super-admin role
        if ($role->name === 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'Role super-admin tidak dapat dihapus'
            ], 422);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Role ini masih digunakan oleh ' . $role->users()->count() . ' user. Hapus user terlebih dahulu.'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus'
        ]);
    }

    public function getPermissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $allPermissions = Permission::all(['id', 'name'])->groupBy(function ($permission) {
            // Group by prefix (e.g., 'user-create' -> 'user')
            $parts = explode('-', $permission->name);
            return $parts[0] ?? 'other';
        });

        $assignedPermissions = $role->permissions->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'all_permissions' => $allPermissions,
                'assigned_permissions' => $assignedPermissions,
                'role' => $role
            ]
        ]);
    }

    public function assignPermission(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Sync permissions (empty array will remove all permissions)
        $role->syncPermissions($validated['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil di-assign ke role'
        ]);
    }
}
