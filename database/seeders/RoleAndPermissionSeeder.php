<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔄 Clear cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ✅ Buat semua role (jaga-jaga kalau belum ada)   
        $roles = ['super_admin', 'admin', 'consultant', 'member'];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web', // pastikan sesuai auth guard
            ]);
        }

        // ✅ Ambil semua permission (harus sudah digenerate via shield:generate)
        $allPermissions = Permission::all();

        // 👑 Super Admin = semua permission
        Role::where('name', 'super_admin')->first()?->syncPermissions($allPermissions);

        // 🛠 Admin
        // Role::where('name', 'admin')->first()?->syncPermissions([
        //     'view_any_users',
        //     'view_users',
        //     'create_users',
        //     'update_users',
        //     'delete_users',
        //     'view_any_tickets',
        //     'view_tickets',
        //     'create_tickets',
        //     'update_tickets',
        //     'delete_tickets',
        // ]);

        // // 🎓 Consultant
        // Role::where('name', 'consultant')->first()?->syncPermissions([
        //     'view_any_tickets',
        //     'view_tickets',
        //     'update_tickets',
        // ]);

        // // 👤 Member
        // Role::where('name', 'member')->first()?->syncPermissions([
        //     'view_tickets',
        //     'create_tickets',
        // ]);
    }
}
