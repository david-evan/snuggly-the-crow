<?php

namespace Database\Seeders;

use Domain\Users\Entities\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'snuggly',
            'password' => 'password'
        ]);
    }
}
