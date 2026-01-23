<?php

namespace App\View\Composers;

use App\Services\MenuService;
use Illuminate\View\View;

class MenuComposer
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $user = auth()->user();

        if (!$user) {
            $view->with('userMenus', []);
            return;
        }

        $menus = $this->menuService->getMenusForUser($user);
        $menuTree = $this->menuService->buildMenuTree($menus);

        $view->with('userMenus', $menuTree);
    }
}
