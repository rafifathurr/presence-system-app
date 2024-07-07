<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $admin_account = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'employee_number' => '12345678',
            'email' => 'admin@gmail.com',
            'password'=> bcrypt('admin123')
        ]);

        $admin_account->assignRole('admin');

        $staff_account = User::create([
            'name' => 'Staff',
            'username' => 'staff',
            'employee_number' => '87654321',
            'email' => 'staff@gmail.com',
            'password'=> bcrypt('staff123')
        ]);

        $staff_account->assignRole('staff');

    }
}
