<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role' => 'admin',
            ],
            [
                'username' => 'manager',
                'password' => Hash::make('manager'),
                'role' => 'manager',
            ],
            [
                'username' => 'manager',
                'password' => Hash::make('manager'),
                'role' => 'manager',
            ],
        ];

        User::insert($users);
    }
}
