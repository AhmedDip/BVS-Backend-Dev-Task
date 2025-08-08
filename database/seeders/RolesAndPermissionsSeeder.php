<?php
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles
        $admin  = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Admin']);
        $editor = Role::firstOrCreate(['name' => 'editor'], ['display_name' => 'Editor']);
        $author = Role::firstOrCreate(['name' => 'author'], ['display_name' => 'Author']);

        // Permissions
        $permissions = [
            'view-users'        => 'View Users',
            'assign-roles'      => 'Assign Roles',
            'create-article'    => 'Create Articles',
            'edit-own-article'  => 'Edit Own Articles',
            'publish-article'   => 'Publish Articles',
            'delete-article'    => 'Delete Articles',
            'view-published'    => 'View Published Content',
            'view-own-articles' => 'View Own Articles',
        ];

        $now = now(); 

        foreach ($permissions as $name => $displayName) {
            $perm = Permission::firstOrCreate(['name' => $name], ['display_name' => $displayName]);

            if (in_array($name, ['publish-article'])) {
                $this->attachWithTimestamps($perm, [$admin->id, $editor->id], $now);
            } elseif ($name === 'delete-article') {
                $this->attachWithTimestamps($perm, [$admin->id], $now);
            } elseif ($name === 'view-users' || $name === 'assign-roles') {
                $this->attachWithTimestamps($perm, [$admin->id], $now);
            } elseif (in_array($name, ['create-article', 'edit-own-article', 'view-own-articles'])) {
                $this->attachWithTimestamps($perm, [$author->id], $now);
            } elseif ($name === 'view-published') {
                $this->attachWithTimestamps($perm, [$admin->id, $editor->id, $author->id], $now);
            }
        }
    }


    private function attachWithTimestamps(Permission $permission, array $roleIds, $timestamp)
    {
        foreach ($roleIds as $roleId) {
            if (! $permission->roles()->where('role_id', $roleId)->exists()) {
                $permission->roles()->attach($roleId, [
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        }
    }
}
