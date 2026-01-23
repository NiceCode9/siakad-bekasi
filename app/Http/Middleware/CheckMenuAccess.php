<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $menuSlug): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $menu = Menu::where('slug', $menuSlug)->first();

        if (!$menu) {
            abort(404, 'Menu not found');
        }

        if (!$this->menuService->canAccessMenu($user, $menu)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}
