<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 50)->create()->each(function ($u, $index) {
            $roleName = $index === 0 ? 'admin' : 'regular';
            $role = Role::whereName($roleName)->first();
            $u->assignRole($role);
        });
    }
}
