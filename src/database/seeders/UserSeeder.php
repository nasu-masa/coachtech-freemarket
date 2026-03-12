<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(10)
            ->has(Address::factory()->count(1))
            ->create();

        $avatarFile = 'user.png';

        $avatar = public_path('avatar/' . $avatarFile);
        $avatarFileName = 'avatars/' . Str::random(10) . '.jpg';
        Storage::disk('public')->put($avatarFileName, File::get($avatar));

        User::factory()
            ->has(Address::factory()->count(1))
            ->create([
                'name' => 'テストユーザー',
                'email' => 'test@example.com',
                'password' => bcrypt('test7890'),
                'avatar_path' => $avatarFileName
            ]);

        User::create([
            'name' => '他のユーザー',
            'email' => 'other@example.com',
            'password' => bcrypt('test7890'),
        ]);
    }
}
