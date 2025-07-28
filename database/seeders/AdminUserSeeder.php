<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run()
    {
        $user =  User::updateOrCreate(
            ['email' => 'user1@teguh.com'],
            [
                'name' => 'User 1',
                'password' => Hash::make('user1password'),
            ]
        );

        $user->assignRole('editor'); // if Spatie roles are set up
    }
}
