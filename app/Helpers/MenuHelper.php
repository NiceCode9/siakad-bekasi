<?php

namespace App\Helpers;

use App\Models\User;
use App\Services\MenuService;

class MenuHelper
{
    /**
     * Get menus for authenticated user
     */
    public static function getUserMenus(): array
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        $menuService = app(MenuService::class);
        $menus = $menuService->getMenusForUser($user);

        return $menuService->buildMenuTree($menus);
    }

    /**
     * Check if user has access to specific menu
     */
    public static function canAccessMenu(string $menuSlug): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        $menu = \App\Models\Menu::where('slug', $menuSlug)->first();

        if (!$menu) {
            return false;
        }

        return $user->hasAccessToMenu($menu);
    }
}
