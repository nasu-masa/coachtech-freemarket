<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
        ->count(10)
        ->has(Address::factory()->count(1))
        ->create();
    }
}
