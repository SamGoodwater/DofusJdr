<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Database\Factories\UserFactory;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'contact@jdr.iota21.fr')->exists()) {
            User::factory()->create([
                'name' => 'Goodwater',
                'email' => 'contact@jdr.iota21.fr',
                'role' => User::ROLES['super_admin'],
                'password' => Hash::make('0238962625'),
                'uniqid' => uniqid(),
            ]);
        }

        User::factory(10)->create();
    }
}
