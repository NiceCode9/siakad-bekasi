# Laravel 11 Starter Kit - Complete Backend Setup

## 1. Instalasi Spatie Laravel Permission

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

## 2. Database Migrations

### Migration: Create Menus Table
**File: `database/migrations/xxxx_create_menus_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
```

### Migration: Menu Role Pivot Table
**File: `database/migrations/xxxx_create_menu_role_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['menu_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_role');
    }
};
```

### Migration: Menu Permission Pivot Table
**File: `database/migrations/xxxx_create_menu_permission_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['menu_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_permission');
    }
};
```

## 3. Models

### Menu Model
**File: `app/Models/Menu.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'url',
        'parent_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Relasi hierarki
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    // Relasi dengan Role
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'menu_role');
    }

    // Relasi dengan Permission
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'menu_permission');
    }

    // Scope untuk menu aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk parent menu
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    // Get all children recursively
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }
}
```

### User Model (Update)
**File: `app/Models/User.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Method untuk check apakah user punya akses ke menu
    public function hasAccessToMenu(Menu $menu): bool
    {
        // Cek apakah user punya role yang di-assign ke menu
        $hasRoleAccess = $this->roles()
            ->whereHas('menus', function ($query) use ($menu) {
                $query->where('menus.id', $menu->id);
            })
            ->exists();

        if ($hasRoleAccess) {
            return true;
        }

        // Cek apakah user punya permission yang di-assign ke menu
        $hasPermissionAccess = $this->permissions()
            ->whereHas('menus', function ($query) use ($menu) {
                $query->where('menus.id', $menu->id);
            })
            ->exists();

        return $hasPermissionAccess;
    }
}
```

## 4. Seeders

### Permission Seeder
**File: `database/seeders/PermissionSeeder.php`**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Role Management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            
            // Permission Management
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
            
            // Menu Management
            'view-menus',
            'create-menus',
            'edit-menus',
            'delete-menus',
            
            // Dashboard
            'view-dashboard',
            
            // Reports (example)
            'view-reports',
            'export-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
```

### Role Seeder
**File: `database/seeders/RoleSeeder.php`**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin - all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - most permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'view-dashboard',
            'view-users',
            'create-users',
            'edit-users',
            'view-roles',
            'view-permissions',
            'view-menus',
            'view-reports',
        ]);

        // User - basic permissions
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([
            'view-dashboard',
        ]);

        // Manager - medium permissions
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'view-dashboard',
            'view-users',
            'view-reports',
            'export-reports',
        ]);
    }
}
```

### Menu Seeder
**File: `database/seeders/MenuSeeder.php`**

```php
<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Dashboard
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'slug' => 'dashboard',
            'icon' => 'bi bi-speedometer2',
            'url' => '/dashboard',
            'order' => 1,
        ]);
        $dashboard->permissions()->attach(Permission::where('name', 'view-dashboard')->first());

        // Master Data (Parent)
        $masterData = Menu::create([
            'name' => 'Master Data',
            'slug' => 'master-data',
            'icon' => 'bi bi-database',
            'url' => '#',
            'order' => 2,
        ]);

        // User Management (Child)
        $userMenu = Menu::create([
            'name' => 'Users',
            'slug' => 'users',
            'icon' => 'bi bi-people',
            'url' => '/users',
            'parent_id' => $masterData->id,
            'order' => 1,
        ]);
        $userMenu->permissions()->attach(Permission::where('name', 'view-users')->first());

        // Role Management (Child)
        $roleMenu = Menu::create([
            'name' => 'Roles',
            'slug' => 'roles',
            'icon' => 'bi bi-shield',
            'url' => '/roles',
            'parent_id' => $masterData->id,
            'order' => 2,
        ]);
        $roleMenu->permissions()->attach(Permission::where('name', 'view-roles')->first());

        // Permission Management (Child)
        $permissionMenu = Menu::create([
            'name' => 'Permissions',
            'slug' => 'permissions',
            'icon' => 'bi bi-key',
            'url' => '/permissions',
            'parent_id' => $masterData->id,
            'order' => 3,
        ]);
        $permissionMenu->permissions()->attach(Permission::where('name', 'view-permissions')->first());

        // Menu Management (Child)
        $menuMenu = Menu::create([
            'name' => 'Menus',
            'slug' => 'menus',
            'icon' => 'bi bi-list',
            'url' => '/menus',
            'parent_id' => $masterData->id,
            'order' => 4,
        ]);
        $menuMenu->permissions()->attach(Permission::where('name', 'view-menus')->first());

        // Reports (Parent)
        $reports = Menu::create([
            'name' => 'Reports',
            'slug' => 'reports',
            'icon' => 'bi bi-file-earmark-text',
            'url' => '/reports',
            'order' => 3,
        ]);
        $reports->permissions()->attach(Permission::where('name', 'view-reports')->first());

        // Attach menu ke role (contoh: Dashboard untuk semua role)
        $dashboard->roles()->attach(Role::all());
        
        // Master Data hanya untuk admin dan super-admin
        $masterData->roles()->attach(Role::whereIn('name', ['admin', 'super-admin'])->get());
        
        // Reports untuk manager, admin, super-admin
        $reports->roles()->attach(Role::whereIn('name', ['manager', 'admin', 'super-admin'])->get());
    }
}
```

### User Seeder
**File: `database/seeders/UserSeeder.php`**

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole('super-admin');

        // Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Manager
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole('manager');

        // Regular User
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('user');

        // User dengan multiple roles
        $multiRole = User::create([
            'name' => 'Multi Role User',
            'email' => 'multirole@example.com',
            'password' => Hash::make('password'),
        ]);
        $multiRole->assignRole(['user', 'manager']);

        // User dengan direct permission (di luar role)
        $directPerm = User::create([
            'name' => 'Direct Permission User',
            'email' => 'directperm@example.com',
            'password' => Hash::make('password'),
        ]);
        $directPerm->assignRole('user');
        $directPerm->givePermissionTo(['view-reports', 'view-users']); // Direct permission
    }
}
```

### Database Seeder (Update)
**File: `database/seeders/DatabaseSeeder.php`**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
        ]);
    }
}
```

## 5. Service Class untuk Menu

### Menu Service
**File: `app/Services/MenuService.php`**

```php
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
        });
    }

    /**
     * Check if user can access menu
     */
    protected function canAccessMenu(User $user, Menu $menu): bool
    {
        // Super admin akses semua
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Cek role-based access
        $hasRoleAccess = $user->roles()
            ->whereHas('menus', function ($query) use ($menu) {
                $query->where('menus.id', $menu->id);
            })
            ->exists();

        if ($hasRoleAccess) {
            return true;
        }

        // Cek permission-based access
        $menuPermissions = $menu->permissions->pluck('name')->toArray();
        
        if (empty($menuPermissions)) {
            return false;
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
                'children' => $this->buildMenuTree($menu->children),
            ];
        })->toArray();
    }
}
```

## 6. Controllers

### MenuController
**File: `app/Http/Controllers/MenuController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['parent', 'roles', 'permissions'])
            ->orderBy('order')
            ->get();
        
        return response()->json($menus);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:menus,slug',
            'icon' => 'nullable|string',
            'url' => 'nullable|string',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'integer',
            'is_active' => 'boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $menu = Menu::create($validated);

        if ($request->has('roles')) {
            $menu->roles()->sync($request->roles);
        }

        if ($request->has('permissions')) {
            $menu->permissions()->sync($request->permissions);
        }

        return response()->json($menu->load(['roles', 'permissions']), 201);
    }

    public function show(Menu $menu)
    {
        return response()->json($menu->load(['parent', 'children', 'roles', 'permissions']));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:menus,slug,' . $menu->id,
            'icon' => 'nullable|string',
            'url' => 'nullable|string',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'integer',
            'is_active' => 'boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $menu->update($validated);

        if ($request->has('roles')) {
            $menu->roles()->sync($request->roles);
        }

        if ($request->has('permissions')) {
            $menu->permissions()->sync($request->permissions);
        }

        return response()->json($menu->load(['roles', 'permissions']));
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json(null, 204);
    }
}
```

### RoleController
**File: `app/Http/Controllers/RoleController.php`**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json($role->load('permissions'), 201);
    }

    public function show(Role $role)
    {
        return response()->json($role->load('permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json($role->load('permissions'));
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(null, 204);
    }
}
```

### PermissionController
**File: `app/Http/Controllers/PermissionController.php`**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create($validated);
        return response()->json($permission, 201);
    }

    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update($validated);
        return response()->json($permission);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(null, 204);
    }
}
```

### UserController
**File: `app/Http/Controllers/UserController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'permissions'])->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return response()->json($user->load(['roles', 'permissions']), 201);
    }

    public function show(User $user)
    {
        return response()->json($user->load(['roles', 'permissions']));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $request->password ? Hash::make($validated['password']) : $user->password,
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return response()->json($user->load(['roles', 'permissions']));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
```

## 7. Routes

**File: `routes/web.php` atau `routes/api.php`**

```php
<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    
    // Menu Management
    Route::middleware('permission:view-menus')->group(function () {
        Route::get('/menus', [MenuController::class, 'index']);
        Route::get('/menus/{menu}', [MenuController::class, 'show']);
    });
    Route::post('/menus', [MenuController::class, 'store'])->middleware('permission:create-menus');
    Route::put('/menus/{menu}', [MenuController::class, 'update'])->middleware('permission:edit-menus');
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->middleware('permission:delete-menus');

    // Role Management
    Route::middleware('permission:view-roles')->group(function () {
        Route::get('/roles', [RoleController::class, 'index']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
    });
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:create-roles');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:edit-roles');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete-roles');

    // Permission Management
    Route::middleware('permission:view-permissions')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
    });
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:create-permissions');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->middleware('permission:edit-permissions');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:delete-permissions');

    // User Management
    Route::middleware('permission:view-users')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
    });
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:create-users');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:edit-users');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:delete-users');
});
```

## 8. Cara Menjalankan

```bash
# Jalankan migration
php artisan migrate

# Jalankan seeder
php artisan db:seed

# Atau jalankan seeder spesifik
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=MenuSeeder
php artisan db:seed --class=UserSeeder
```

## 9. Testing Data

Setelah seeding, Anda bisa login dengan:

- **Super Admin**: superadmin@example.com / password
- **Admin**: admin@example.com / password
- **Manager**: manager@example.com / password
- **User**: user@example.com / password
- **Multi Role**: multirole@example.com / password
- **Direct Permission**: directperm@example.com / password

## 10. Menggunakan MenuService di Controller

```php
use App\Services\MenuService;

class DashboardController extends Controller
{
    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $menus = $this->menuService->getMenusForUser($user);
        $menuTree = $this->menuService->buildMenuTree($menus);
        
        return view('dashboard', compact('menus', 'menuTree'));
    }
}
```

## 11. Helper untuk Get User Menus (Optional)

**File: `app/Helpers/MenuHelper.php`**

```php
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
```

**Autoload Helper (tambahkan di `composer.json`):**

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Helpers/MenuHelper.php"
    ]
},
```

Setelah update `composer.json`, jalankan:
```bash
composer dump-autoload
```

## 12. API Resources (Optional - untuk response yang lebih clean)

### MenuResource
**File: `app/Http/Resources/MenuResource.php`**

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'url' => $this->url,
            'parent_id' => $this->parent_id,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'parent' => $this->whenLoaded('parent', fn() => new MenuResource($this->parent)),
            'children' => MenuResource::collection($this->whenLoaded('children')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
```

### RoleResource
**File: `app/Http/Resources/RoleResource.php`**

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
```

### PermissionResource
**File: `app/Http/Resources/PermissionResource.php`**

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
```

### UserResource
**File: `app/Http/Resources/UserResource.php`**

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->toDateTimeString(),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'all_permissions' => $this->when($this->relationLoaded('roles') && $this->relationLoaded('permissions'), 
                fn() => $this->getAllPermissions()->pluck('name')
            ),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
```

## 13. Form Requests (Validation terpisah)

### StoreMenuRequest
**File: `app/Http/Requests/StoreMenuRequest.php`**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-menus');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:menus,slug|max:255',
            'icon' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama menu harus diisi',
            'slug.required' => 'Slug menu harus diisi',
            'slug.unique' => 'Slug menu sudah digunakan',
            'parent_id.exists' => 'Parent menu tidak valid',
            'roles.*.exists' => 'Role tidak valid',
            'permissions.*.exists' => 'Permission tidak valid',
        ];
    }
}
```

### UpdateMenuRequest
**File: `app/Http/Requests/UpdateMenuRequest.php`**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit-menus');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('menus')->ignore($this->menu)],
            'icon' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => ['nullable', 'exists:menus,id', Rule::notIn([$this->menu->id])],
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama menu harus diisi',
            'slug.required' => 'Slug menu harus diisi',
            'slug.unique' => 'Slug menu sudah digunakan',
            'parent_id.exists' => 'Parent menu tidak valid',
            'parent_id.not_in' => 'Menu tidak boleh menjadi parent dari dirinya sendiri',
            'roles.*.exists' => 'Role tidak valid',
            'permissions.*.exists' => 'Permission tidak valid',
        ];
    }
}
```

### StoreUserRequest & UpdateUserRequest
**File: `app/Http/Requests/StoreUserRequest.php`**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-users');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }
}
```

**File: `app/Http/Requests/UpdateUserRequest.php`**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit-users');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }
}
```

## 14. Update Controllers dengan Resources & Requests

### MenuController (Updated)
**File: `app/Http/Controllers/MenuController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Http\Resources\MenuResource;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['parent', 'roles', 'permissions', 'children'])
            ->orderBy('order')
            ->get();
        
        return MenuResource::collection($menus);
    }

    public function store(StoreMenuRequest $request)
    {
        $menu = Menu::create($request->validated());

        if ($request->has('roles')) {
            $menu->roles()->sync($request->roles);
        }

        if ($request->has('permissions')) {
            $menu->permissions()->sync($request->permissions);
        }

        return new MenuResource($menu->load(['roles', 'permissions']));
    }

    public function show(Menu $menu)
    {
        return new MenuResource($menu->load(['parent', 'children', 'roles', 'permissions']));
    }

    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $menu->update($request->validated());

        if ($request->has('roles')) {
            $menu->roles()->sync($request->roles);
        }

        if ($request->has('permissions')) {
            $menu->permissions()->sync($request->permissions);
        }

        return new MenuResource($menu->load(['roles', 'permissions']));
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        
        return response()->json([
            'message' => 'Menu berhasil dihapus'
        ], 200);
    }
}
```

### UserController (Updated)
**File: `app/Http/Controllers/UserController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'permissions'])->get();
        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return new UserResource($user->load(['roles', 'permissions']));
    }

    public function show(User $user)
    {
        return new UserResource($user->load(['roles', 'permissions']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return new UserResource($user->load(['roles', 'permissions']));
    }

    public function destroy(User $user)
    {
        $user->delete();
        
        return response()->json([
            'message' => 'User berhasil dihapus'
        ], 200);
    }
}
```

## 15. Middleware Custom (Optional)

### CheckMenuAccess Middleware
**File: `app/Http/Middleware/CheckMenuAccess.php`**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Menu;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next, string $menuSlug)
    {
        $user = $request->user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Super admin bypass
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        $menu = Menu::where('slug', $menuSlug)->first();
        
        if (!$menu || !$user->hasAccessToMenu($menu)) {
            abort(403, 'Anda tidak memiliki akses ke menu ini');
        }

        return $next($request);
    }
}
```

**Register Middleware di `bootstrap/app.php` (Laravel 11):**

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'menu.access' => \App\Http\Middleware\CheckMenuAccess::class,
    ]);
})
```

**Cara pakai di route:**

```php
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'menu.access:dashboard']);
```

## 16. Testing Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Run migrations fresh dengan seeder
php artisan migrate:fresh --seed

# Atau step by step
php artisan migrate:fresh
php artisan db:seed

# Test spesifik seeder
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=MenuSeeder
php artisan db:seed --class=UserSeeder

# Generate API Resources
php artisan make:resource MenuResource
php artisan make:resource UserResource
php artisan make:resource RoleResource
php artisan make:resource PermissionResource

# Generate Request Classes
php artisan make:request StoreMenuRequest
php artisan make:request UpdateMenuRequest
```

## 17. Contoh Penggunaan API

### Get User Menus (untuk sidebar)
```php
// Di controller atau helper
use App\Services\MenuService;

$menuService = app(MenuService::class);
$menus = $menuService->getMenusForUser(auth()->user());
$menuTree = $menuService->buildMenuTree($menus);

return response()->json($menuTree);
```

### Check Access di Blade (nanti untuk frontend)
```blade
@can('view-users')
    <a href="{{ route('users.index') }}">Users</a>
@endcan

{{-- Atau pakai helper --}}
@if(MenuHelper::canAccessMenu('users'))
    <a href="{{ route('users.index') }}">Users</a>
@endif
```

## 18. Database Schema Summary

```
users
├── id
├── name
├── email
├── password
└── timestamps

roles (Spatie)
├── id
├── name
├── guard_name
└── timestamps

permissions (Spatie)
├── id
├── name  
├── guard_name
└── timestamps

model_has_roles (Spatie - pivot)
├── role_id
├── model_type
└── model_id

model_has_permissions (Spatie - pivot)
├── permission_id
├── model_type
└── model_id

role_has_permissions (Spatie - pivot)
├── permission_id
└── role_id

menus
├── id
├── name
├── slug
├── icon
├── url
├── parent_id (self-reference)
├── order
├── is_active
└── timestamps

menu_role (pivot)
├── id
├── menu_id
├── role_id
└── timestamps

menu_permission (pivot)
├── id
├── menu_id
├── permission_id
└── timestamps
```

---

## ✅ Backend Setup Complete!

Struktur backend sudah lengkap dengan:
- ✅ Database migrations
- ✅ Models & relationships
- ✅ Seeders dengan data dummy
- ✅ Controllers (CRUD lengkap)
- ✅ Form Requests (validation)
- ✅ API Resources (response formatting)
- ✅ Service layer (MenuService)
- ✅ Helper functions
- ✅ Custom middleware
- ✅ Routes dengan permission guard

**Selanjutnya siap untuk Frontend dengan Bootstrap 5!** 🚀