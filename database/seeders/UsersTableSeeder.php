<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@bvs.com',
        ], [
            'name'     => 'Admin User',
            'password' => Hash::make('12345678'),
        ]);
        $adminRole = Role::where('name', 'admin')->first();
        $admin->assignRole($adminRole);

        $editor = User::firstOrCreate([
            'email' => 'editor@bvs.com',
        ], [
            'name'     => 'Editor User',
            'password' => Hash::make('editor'),
        ]);
        $editor->assignRole('editor');

        $author = User::firstOrCreate([
            'email' => 'author@bvs.com',
        ], [
            'name'     => 'Author User',
            'password' => Hash::make('author'),
        ]);
        $author->assignRole('author');
    }
}
