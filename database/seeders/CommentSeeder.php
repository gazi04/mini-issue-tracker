<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds. Each issue gets a few comments.
     */
    public function run(): void
    {
        Issue::all()->each(function (Issue $issue): void {
            Comment::factory()
                ->count(fake()->numberBetween(2, 5))
                ->for($issue)
                ->create();
        });
    }
}
