<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // User::factory(1)->create();
        $member = [
            [
                'fullname' => 'member-1',
                'name' => 'member-1',
                'email' => 'member-1@nuparis.id',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'address' => '-',
                'phone' => '-',
                'code_ref' => 'consultant-8c562'
            ],
            [
                'fullname' => 'member-2',
                'name' => 'member-2',
                'email' => 'member-2@nuparis.id',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'address' => '-',
                'phone' => '-',
                'code_ref' => 'consultant-8c562'
            ],
            [
                'fullname' => 'member-3',
                'name' => 'member-3',
                'email' => 'member-3@nuparis.id',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'address' => '-',
                'phone' => '-',
                'code_ref' => 'consultant-8c562'
            ],
            [
                'fullname' => 'member-4',
                'name' => 'member-4',
                'email' => 'member-4@nuparis.id',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'address' => '-',
                'phone' => '-',
                'code_ref' => 'consultant-8c562'
            ],
            [
                'fullname' => 'member-5',
                'name' => 'member-5',
                'email' => 'member-5@nuparis.id',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'address' => '-',
                'phone' => '-',
                'code_ref' => 'consultant-8c562'
            ],
            [
                'fullname' => 'member-6',
                'name' => 'member-6',
                'email' => 'member-6@nuparis.id',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'address' => '-',
                'phone' => '-',
                'code_ref' => 'consultant-8c562'
            ]
        ];

        // 1. Buat user suadmin jika belum ada
        $user = User::firstOrCreate(
            ['email' => 'suadmin@nuparis.id'],
            [
                'fullname' => 'suadmin',
                'name' => 'suadmin',
                'phone' => '-',
                'address' => '-',
                'password' => bcrypt('password'), // ganti sesuai kebutuhan
            ]
        );

        // 2. Buat role super_admin kalau belum ada
        $role = Role::firstOrCreate(['name' => 'super_admin']);

        // 3. Ambil semua permission yang tersedia
        $permissions = Permission::all();

        // 4. Assign semua permission ke role super_admin
        $role->syncPermissions($permissions);

        // 5. Assign role super_admin ke user suadmin
        $user->assignRole($role);

        User::create([
            'fullname' => 'consultant-1',
            'name' => 'consultant-1',
            'email' => 'consultant-1@nuparis.id',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'address' => '-',
            'phone' => '-',
            'consultant_specialization_uuid' => '1768c562-62a0-41e0-8f6d-76f5ec5b5e7c', // bidasng 1
            'consultant_code' => 'consultant-8c562'
        ]);
        User::create([
            'fullname' => 'consultant-2',
            'name' => 'consultant-2',
            'email' => 'consultant-2@nuparis.id',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'address' => '-',
            'phone' => '-',
            'consultant_specialization_uuid' => '1768c562-62a0-41e0-8f6d-76f5ec5b5e7c', // bidasng 1
            'consultant_code' => 'consultant-8c562'
        ]);
        User::create([
            'fullname' => 'consultant-3',
            'name' => 'consultant-3',
            'email' => 'consultant-3@nuparis.id',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'address' => '-',
            'phone' => '-',
            'consultant_specialization_uuid' => '1768c562-62a0-41e0-8f6d-76f5ec5b5e7c', // bidasng 1
            'consultant_code' => 'consultant-8c562'
        ]);
        User::create([
            'fullname' => 'consultant-4',
            'name' => 'consultant-4',
            'email' => 'consultant-4@nuparis.id',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'address' => '-',
            'phone' => '-',
            'consultant_specialization_uuid' => '1768c562-62a0-41e0-8f6d-76f5ec5b5e7c', // bidasng 1
            'consultant_code' => 'consultant-8c562'
        ]);
        User::create([
            'fullname' => 'consultant-5',
            'name' => 'consultant-5',
            'email' => 'consultant-5@nuparis.id',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'address' => '-',
            'phone' => '-',
            'consultant_specialization_uuid' => '1768c562-62a0-41e0-8f6d-76f5ec5b5e7c', // bidasng 1
            'consultant_code' => 'consultant-8c562'
        ]);
        User::create([
            'fullname' => 'consultant-6',
            'name' => 'consultant-6',
            'email' => 'consultant-6@nuparis.id',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'address' => '-',
            'phone' => '-',
            'consultant_specialization_uuid' => '1768c562-62a0-41e0-8f6d-76f5ec5b5e7c', // bidasng 1
            'consultant_code' => 'consultant-8c562'
        ]);

        User::create([
            'fullname' => 'admin',
            'name' => 'admin',
            'email' => 'admin@nuparis.id',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'address' => '-',
            'phone' => '-',
        ]);

        foreach ($member as $data) {
            $model = new User($data);
            $model->timestamps = false;
            $model->save();
        }
    }
}
