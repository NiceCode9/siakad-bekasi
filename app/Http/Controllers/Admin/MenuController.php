<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $menus = Menu::with(['parent', 'roles', 'permissions'])
                ->orderBy('order')
                ->get();

            return DataTables::of($menus)
                ->addIndexColumn()
                ->addColumn('parent_name', function ($menu) {
                    return $menu->parent ? $menu->parent->name : '-';
                })
                ->addColumn('roles_list', function ($menu) {
                    return $menu->roles->pluck('name')->implode(', ') ?: '-';
                })
                ->addColumn('permissions_list', function ($menu) {
                    return $menu->permissions->pluck('name')->implode(', ') ?: '-';
                })
                ->addColumn('status', function ($menu) {
                    $badge = $menu->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-secondary">Inactive</span>';
                    return $badge;
                })
                ->addColumn('action', function ($menu) {
                    return '
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary btn-edit" data-id="' . $menu->id . '" title="Edit">
                                <i class="simple-icon-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-info btn-assign-role" data-id="' . $menu->id . '" title="Assign Role">
                                <i class="simple-icon-user"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning btn-assign-permission" data-id="' . $menu->id . '" title="Assign Permission">
                                <i class="simple-icon-lock"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $menu->id . '" title="Delete">
                                <i class="simple-icon-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.menu.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:menus,slug',
            'icon' => 'nullable|string|max:255',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $menu = Menu::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan',
            'data' => $menu
        ]);
    }

    public function show($id)
    {
        $menu = Menu::with(['parent', 'roles', 'permissions'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $menu
        ]);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:menus,slug,' . $id,
            'icon' => 'nullable|string|max:255',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Prevent self-referencing parent
        if ($validated['parent_id'] == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak bisa menjadi parent dari dirinya sendiri'
            ], 422);
        }

        $menu->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diupdate',
            'data' => $menu
        ]);
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // Check if menu has children
        if ($menu->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Menu ini memiliki submenu. Hapus submenu terlebih dahulu.'
            ], 422);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus'
        ]);
    }

    public function getParents()
    {
        $parents = Menu::whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $parents
        ]);
    }

    public function assignRole(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $menu->roles()->sync($validated['roles']);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil di-assign ke menu'
        ]);
    }

    public function getRoles($id)
    {
        $menu = Menu::with('roles')->findOrFail($id);
        $allRoles = Role::all(['id', 'name']);
        $assignedRoles = $menu->roles->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'all_roles' => $allRoles,
                'assigned_roles' => $assignedRoles,
                'menu' => $menu
            ]
        ]);
    }

    public function assignPermission(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $menu->permissions()->sync($validated['permissions']);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil di-assign ke menu'
        ]);
    }

    public function getPermissions($id)
    {
        $menu = Menu::with('permissions')->findOrFail($id);
        $allPermissions = Permission::all(['id', 'name']);
        $assignedPermissions = $menu->permissions->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'all_permissions' => $allPermissions,
                'assigned_permissions' => $assignedPermissions,
                'menu' => $menu
            ]
        ]);
    }
}
