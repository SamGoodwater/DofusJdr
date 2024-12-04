<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->makeUsers();
    }

    protected function makeUsers(): void
    {
        User::factory()->create([
            'name' => 'Goodwater',
            'email' => 'contact@jdr.iota21.fr',
            'role' => User::ROLES['super_admin'],
            'password' =>  Hash::make('0238962625'),
            'uniqid' => uniqid(),
        ]);
    }
}
