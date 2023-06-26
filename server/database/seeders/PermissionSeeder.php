<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class PermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $permissions = [
      'create-admins',
      'update-admins',
      'delete-admins',
      'view-admins',
      'accept-users',
      'ban-users',
      'view-users',
    ];

    $superAdminPermissions = [
      'create-admins',
      'update-admins',
      'delete-admins',
      'view-admins',
      'accept-users',
      'ban-users',
      'view-users',
    ];

    $adminPermissions = [
      'accept-users',
      'ban-users',
      'view-users',
    ];


    foreach ($permissions as $permission) {
      Permission::create(['name' => $permission]);
    }

    $roles = [
      "super_admin",
      "admin",
      "user",
    ];

    foreach ($roles as $role) {
      $$role = Role::create(['name' => $role]);
    }

    $super_admin->syncPermissions($superAdminPermissions);
    $admin->syncPermissions($adminPermissions);


    $superAdmin1 = User::create([
      'name' => 'superadmin1',
      'email' => 'superadmin@admin.com',
      'password' => Hash::make('12345678'),
    ]);

    $admin1 = User::create([
      'name' => 'admin1',
      'email' => 'admin@admin.com',
      'password' => Hash::make('12345678'),
    ]);

    $superAdmin1->assignRole($super_admin);
    $admin1->assignRole($admin);
  }
}
