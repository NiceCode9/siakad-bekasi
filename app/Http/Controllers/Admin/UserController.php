<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $users = User::with(['roles', 'permissions'])->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('role_names', function ($user) {
                    $roles = $user->roles->pluck('name')->toArray();
                    if (empty($roles)) {
                        return '<span class="badge badge-secondary">Belum ada role</span>';
                    }
                    return collect($roles)->map(function ($role) {
                        return '<span class="badge badge-primary mr-1">' . $role . '</span>';
                    })->implode('');
                })
                ->addColumn('status', function ($user) {
                    if ($user->is_active) {
                        return '<span class="badge badge-success">Aktif</span>';
                    }
                    return '<span class="badge badge-danger">Non-Aktif</span>';
                })
                ->addColumn('last_login_formatted', function ($user) {
                    return $user->last_login ? $user->last_login->format('d M Y H:i') : '-';
                })
                ->addColumn('permissions_count', function ($user) {
                    $directPermissions = $user->permissions->count();
                    $rolePermissions = $user->getPermissionsViaRoles()->count();
                    $total = $user->getAllPermissions()->count();

                    return $total . ' <small class="text-muted">(' . $directPermissions . ' langsung)</small>';
                })
                ->addColumn('action', function ($user) {
                    $editBtn = '<button type="button" class="btn btn-sm btn-primary btn-edit" data-id="' . $user->id . '" title="Edit">
                                    <i class="simple-icon-pencil"></i>
                                </button>';

                    $roleBtn = '<button type="button" class="btn btn-sm btn-info btn-assign-role" data-id="' . $user->id . '" title="Assign Role">
                                    <i class="simple-icon-people"></i>
                                </button>';

                    $permissionBtn = '<button type="button" class="btn btn-sm btn-warning btn-assign-permission" data-id="' . $user->id . '" title="Assign Permission">
                                        <i class="simple-icon-lock"></i>
                                      </button>';

                    $toggleBtn = $user->is_active
                        ? '<button type="button" class="btn btn-sm btn-secondary btn-toggle-status" data-id="' . $user->id . '" data-status="0" title="Non-Aktifkan">
                                <i class="simple-icon-close"></i>
                           </button>'
                        : '<button type="button" class="btn btn-sm btn-success btn-toggle-status" data-id="' . $user->id . '" data-status="1" title="Aktifkan">
                                <i class="simple-icon-check"></i>
                           </button>';

                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $user->id . '" title="Delete">
                                    <i class="simple-icon-trash"></i>
                                  </button>';

                    return '<div class="btn-group" role="group">' . $editBtn . $roleBtn . $permissionBtn . $toggleBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['role_names', 'status', 'permissions_count', 'action'])
                ->make(true);
        }

        return view('admin.user.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ]);
    }

    public function show($id)
    {
        $user = User::with(['roles', 'permissions'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent toggling own account
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengubah status akun Anda sendiri'
            ], 422);
        }

        $user->is_active = $request->status;
        $user->save();

        $status = $request->status ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ' . $status
        ]);
    }

    public function getRoles($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $allRoles = Role::all(['id', 'name']);
        $assignedRoles = $user->roles->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'all_roles' => $allRoles,
                'assigned_roles' => $assignedRoles,
                'user' => $user
            ]
        ]);
    }

    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Get role names
        $roles = Role::whereIn('id', $validated['roles'] ?? [])->pluck('name')->toArray();

        // Sync roles
        $user->syncRoles($roles);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil di-assign ke user'
        ]);
    }

    public function getPermissions($id)
    {
        $user = User::with('permissions', 'roles.permissions')->findOrFail($id);
        $allPermissions = Permission::all(['id', 'name']);

        // Direct permissions
        $directPermissions = $user->permissions->pluck('id')->toArray();

        // Permissions via roles
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('id')->toArray();

        // All permissions (direct + via roles)
        $allUserPermissions = $user->getAllPermissions()->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'all_permissions' => $allPermissions,
                'direct_permissions' => $directPermissions,
                'role_permissions' => $rolePermissions,
                'all_user_permissions' => $allUserPermissions,
                'user' => $user
            ]
        ]);
    }

    public function assignPermission(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Get permission names
        $permissions = Permission::whereIn('id', $validated['permissions'] ?? [])->pluck('name')->toArray();

        // Sync direct permissions (tidak mempengaruhi permissions dari role)
        $user->syncPermissions($permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil di-assign ke user'
        ]);
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset'
        ]);
    }
}
