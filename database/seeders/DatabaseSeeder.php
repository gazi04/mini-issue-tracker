<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Alice Owner',
            'email' => 'alice@example.com',
        ]);

        User::factory()->create([
            'name' => 'Bob Member',
            'email' => 'bob@example.com',
        ]);

        $this->call([
            TagSeeder::class,
            ProjectSeeder::class,
            IssueSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
