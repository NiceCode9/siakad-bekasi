<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $permissions = Permission::withCount('roles', 'users')->get();

            return DataTables::of($permissions)
                ->addIndexColumn()
                ->addColumn('roles_count', function ($permission) {
                    return $permission->roles_count;
                })
                ->addColumn('users_count', function ($permission) {
                    return $permission->users_count;
                })
                ->addColumn('roles_list', function ($permission) {
                    $roles = $permission->roles->take(3)->pluck('name')->toArray();
                    $count = $permission->roles->count();

                    if ($count > 3) {
                        $list = implode(', ', $roles) . ' <span class="badge badge-info">+' . ($count - 3) . ' lainnya</span>';
                    } else {
                        $list = $roles ? implode(', ', $roles) : '-';
                    }

                    return $list;
                })
                ->addColumn('category', function ($permission) {
                    // Extract category from permission name (e.g., 'user-create' -> 'user')
                    $parts = explode('-', $permission->name);
                    $category = $parts[0] ?? 'other';

                    $badges = [
                        'user' => 'primary',
                        'role' => 'success',
                        'menu' => 'info',
                        'siswa' => 'warning',
                        'guru' => 'secondary',
                        'kelas' => 'dark',
                        'mapel' => 'primary',
                        'nilai' => 'danger',
                        'absensi' => 'success',
                        'laporan' => 'info',
                    ];

                    $badgeColor = $badges[$category] ?? 'secondary';

                    return '<span class="badge badge-' . $badgeColor . '">' . ucfirst($category) . '</span>';
                })
                ->addColumn('guard', function ($permission) {
                    return '<span class="badge badge-secondary">' . $permission->guard_name . '</span>';
                })
                ->addColumn('action', function ($permission) {
                    $editBtn = '<button type="button" class="btn btn-sm btn-primary btn-edit" data-id="' . $permission->id . '" title="Edit">
                                    <i class="simple-icon-pencil"></i>
                                </button>';

                    $roleBtn = '<button type="button" class="btn btn-sm btn-info btn-assign-role" data-id="' . $permission->id . '" title="Assign ke Role">
                                    <i class="simple-icon-user"></i>
                                </button>';

                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $permission->id . '" title="Delete">
                                    <i class="simple-icon-trash"></i>
                                  </button>';

                    return '<div class="btn-group" role="group">' . $editBtn . $roleBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['category', 'guard', 'roles_list', 'action'])
                ->make(true);
        }

        return view('admin.permission.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'nullable|string|max:255',
        ]);

        $validated['guard_name'] = $validated['guard_name'] ?? 'web';

        $permission = Permission::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil ditambahkan',
            'data' => $permission
        ]);
    }

    public function show($id)
    {
        $permission = Permission::with('roles')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $permission
        ]);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'guard_name' => 'nullable|string|max:255',
        ]);

        $validated['guard_name'] = $validated['guard_name'] ?? 'web';

        $permission->update($validated);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil diupdate',
            'data' => $permission
        ]);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        // Check if permission is assigned to roles
        if ($permission->roles()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Permission ini masih digunakan oleh ' . $permission->roles()->count() . ' role. Hapus dari role terlebih dahulu.'
            ], 422);
        }

        // Check if permission is assigned to users
        if ($permission->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Permission ini masih digunakan oleh ' . $permission->users()->count() . ' user.'
            ], 422);
        }

        $permission->delete();

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dihapus'
        ]);
    }

    public function getRoles($id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        $allRoles = Role::all(['id', 'name']);
        $assignedRoles = $permission->roles->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'all_roles' => $allRoles,
                'assigned_roles' => $assignedRoles,
                'permission' => $permission
            ]
        ]);
    }

    public function assignRole(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Get role models
        $roles = Role::whereIn('id', $validated['roles'] ?? [])->get();

        // Sync permission to roles
        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }

        // Remove permission from roles not in the list
        $allRoles = Role::all();
        foreach ($allRoles as $role) {
            if (!in_array($role->id, $validated['roles'] ?? [])) {
                $role->revokePermissionTo($permission);
            }
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil di-assign ke role'
        ]);
    }

    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'required|string|max:50',
            'actions' => 'required|array|min:1',
            'actions.*' => 'required|string',
            'guard_name' => 'nullable|string|max:255',
        ]);

        $guardName = $validated['guard_name'] ?? 'web';
        $created = [];
        $skipped = [];

        foreach ($validated['actions'] as $action) {
            $permissionName = $validated['prefix'] . '-' . $action;

            // Check if already exists
            if (Permission::where('name', $permissionName)->exists()) {
                $skipped[] = $permissionName;
                continue;
            }

            $permission = Permission::create([
                'name' => $permissionName,
                'guard_name' => $guardName
            ]);

            $created[] = $permissionName;
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . ' permission berhasil dibuat',
            'data' => [
                'created' => $created,
                'skipped' => $skipped
            ]
        ]);
    }
}
