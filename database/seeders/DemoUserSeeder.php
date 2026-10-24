<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sample users for the admin list and the dashboard charts (local environment only).
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 60) as $i) {
            $createdAt = now()->subDays(fake()->numberBetween(0, 364))->setTime(fake()->numberBetween(7, 22), fake()->numberBetween(0, 59));

            $factory = User::factory();

            $factory = match (true) {
                $i % 7 === 0 => $factory->unverified(),
                $i % 4 === 0 => $factory->withTwoFactor(),
                default => $factory,
            };

            $factory->create([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
