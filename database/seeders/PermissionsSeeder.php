<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $actions = [
            'view',
            'view_any',
            'view_own',
            'create',
            'update',
            'restore',
            'restore_any',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any'
        ];

        $models = [
            'user',
            'absence',
            'absence_type',
            'person',
        ];

        $custom_permissions = [
            \App\Enums\Permission::MANAGE_SETTINGS->value,
            \App\Enums\Permission::ABSENCE_REQUEST->value,
        ];

        $permissions = $this->generatePermissions($actions, $models, $custom_permissions);

        foreach ($permissions as $permissionName) {
            Permission::updateOrCreate(['name' => $permissionName]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function generatePermissions(array $actions, array $models, array $custom_permissions): array
    {
        $permissions = [];

        // Combine each action with each model
        foreach ($models as $model) {
            foreach ($actions as $action) {
                $permissions[] = "{$model}_{$action}";
            }
        }

        // Add custom permissions
        foreach ($custom_permissions as $custom_permission) {
            $permissions[] = $custom_permission;
        }

        return $permissions;
    }
}
