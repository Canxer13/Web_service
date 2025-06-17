<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Kita tetap butuh model User untuk Auth

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Roles
        $adminRole = DB::table('roles')->insertGetId(['name' => 'Admin', 'slug' => 'admin', 'created_at' => now(), 'updated_at' => now()]);
        $staffRole = DB::table('roles')->insertGetId(['name' => 'Staff', 'slug' => 'staff', 'created_at' => now(), 'updated_at' => now()]);

        // Buat Permissions
        $p_view = DB::table('permissions')->insertGetId(['name' => 'View Items', 'slug' => 'view-items', 'created_at' => now(), 'updated_at' => now()]);
        $p_create = DB::table('permissions')->insertGetId(['name' => 'Create Items', 'slug' => 'create-items', 'created_at' => now(), 'updated_at' => now()]);
        $p_edit = DB::table('permissions')->insertGetId(['name' => 'Edit Items', 'slug' => 'edit-items', 'created_at' => now(), 'updated_at' => now()]);
        $p_delete = DB::table('permissions')->insertGetId(['name' => 'Delete Items', 'slug' => 'delete-items', 'created_at' => now(), 'updated_at' => now()]);

        // Hubungkan Permissions ke Roles
        // Admin bisa melakukan semuanya
        DB::table('permission_role')->insert(['role_id' => $adminRole, 'permission_id' => $p_view]);
        DB::table('permission_role')->insert(['role_id' => $adminRole, 'permission_id' => $p_create]);
        DB::table('permission_role')->insert(['role_id' => $adminRole, 'permission_id' => $p_edit]);
        DB::table('permission_role')->insert(['role_id' => $adminRole, 'permission_id' => $p_delete]);

        // Staff hanya bisa melihat item
        DB::table('permission_role')->insert(['role_id' => $staffRole, 'permission_id' => $p_view]);

        // Buat User contoh
        $adminUser = User::create(['name' => 'Admin User', 'email' => 'admin@mail.com', 'password' => Hash::make('password')]);
        $staffUser = User::create(['name' => 'Staff User', 'email' => 'staff@mail.com', 'password' => Hash::make('password')]);

        // Hubungkan User ke Role
        DB::table('role_user')->insert(['user_id' => $adminUser->id, 'role_id' => $adminRole]);
        DB::table('role_user')->insert(['user_id' => $staffUser->id, 'role_id' => $staffRole]);
    }
}