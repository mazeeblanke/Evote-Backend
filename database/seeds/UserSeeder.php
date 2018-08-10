<?php

use App\User;
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
        $user = factory(User::class)->create([
            'email' => 'admin@evote.com',
            'password' => bcrypt('secret')
        ]);

        $user->syncRoles(['admin', 'regular']);

        factory(User::class, 50)->create()->each(function ($u, $index) {
            $roleName = $index === 0 ? 'admin' : 'regular';
            // $role = Role::whereName($roleName)->first();
            $u->assignRole($roleName);
        });
    }
}
