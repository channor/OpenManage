<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define roles
        $roles = [
            UserRole::SUPER_ADMIN->value,
            UserRole::EMPLOYEE->value,
            UserRole::HR_MANAGER->value,
            UserRole::HR_COORDINATOR,
            UserRole::PAYROLL_CLERK,
        ];

        // Create or update roles
        foreach ($roles as $roleName) {
            Role::updateOrCreate(['name' => $roleName]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Assigns specific permissions to each role.
     */
    protected function assignPermissionsToRoles(): void
    {
        $allPermissions = Permission::all();

        $rolePermissions = [
            UserRole::SUPER_ADMIN->value => $allPermissions->pluck('name')->toArray(), // Admin has all permissions

            UserRole::EMPLOYEE->value => [
                'absence_view_own',
                'absence_request',
                'absence_update',
                'absence_view_sensitive',
            ],

            UserRole::PAYROLL_CLERK->value => [
                'absence_view_any',
                'absence_create',
                'absence_update',
            ],

            UserRole::HR_MANAGER->value => [
                'absence_view',
                'absence_view_any',
                'absence_view_own',
                'absence_create',
                'absence_update',
                'absence_restore',
                'absence_restore_any',
                'absence_delete',
                'absence_delete_any',
                'absence_force_delete',
                'absence_force_delete_any',
                \App\Enums\Permission::ABSENCE_VIEW_SENSITIVE->value,
            ],

            UserRole::HR_COORDINATOR->value => [
                'absence_view',
                'absence_view_own',
                'absence_create',
                'absence_update',
                'absence_restore',
                'absence_delete',
                \App\Enums\Permission::ABSENCE_VIEW_SENSITIVE->value,
            ]
        ];

        // Assign permissions to roles
        foreach ($rolePermissions as $role => $permissions) {
            $roleModel = Role::findByName($role);

            // Ensure permissions exist before assigning
            $validPermissions = Permission::whereIn('name', $permissions)->pluck('name')->toArray();

            $roleModel->syncPermissions($validPermissions);
        }
    }
}
