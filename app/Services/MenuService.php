<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Collection;

class MenuService
{
    /**
     * Get menus accessible by user
     */
    public function getMenusForUser(User $user): Collection
    {
        $menus = Menu::active()
            ->parents()
            ->with(['children' => function ($query) {
                $query->active()->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return $menus->filter(function ($menu) use ($user) {
            return $this->canAccessMenu($user, $menu);
        })->map(function ($menu) use ($user) {
            // Filter children
            if ($menu->children->isNotEmpty()) {
                $menu->setRelation('children', $menu->children->filter(function ($child) use ($user) {
                    return $this->canAccessMenu($user, $child);
                }));
            }
            return $menu;
        })->values(); // Reset collection keys
    }

    /**
     * Check if user can access menu
     */
    public function canAccessMenu(User $user, Menu $menu): bool
    {
        // Super admin akses semua
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Cek role-based access
        $hasRoleAccess = $menu->roles()
            ->whereIn('roles.id', $user->roles->pluck('id'))
            ->exists();

        if ($hasRoleAccess) {
            return true;
        }

        // Cek permission-based access
        $menuPermissions = $menu->permissions->pluck('name')->toArray();

        if (empty($menuPermissions)) {
            // Jika menu tidak punya permission, cek apakah user punya role yang ter-assign
            return $hasRoleAccess;
        }

        return $user->hasAnyPermission($menuPermissions);
    }

    /**
     * Build hierarchical menu tree
     */
    public function buildMenuTree(Collection $menus): array
    {
        return $menus->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'icon' => $menu->icon,
                'url' => $menu->url,
                'order' => $menu->order,
                'has_children' => $menu->children->isNotEmpty(),
                'children' => $menu->children->isNotEmpty()
                    ? $this->buildMenuTree($menu->children)
                    : [],
            ];
        })->toArray();
    }
}
