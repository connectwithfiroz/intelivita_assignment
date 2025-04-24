<?php

namespace Database\Seeders;

use App\Models\UserActivity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => fake()->randomElement(['walking', 'running', 'playing']),
                'points' => 20,
                'rank' => null,
            ]);
        }
    }
}
