<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    /**
     * Run the database seeds. Each project gets a handful of issues with
     * randomly attached tags and assigned members.
     */
    public function run(): void
    {
        $tagIds = Tag::query()->pluck('id');
        $userIds = User::query()->pluck('id');

        Project::all()->each(function (Project $project) use ($tagIds, $userIds): void {
            Issue::factory()
                ->count(fake()->numberBetween(3, 6))
                ->for($project)
                ->create()
                ->each(function (Issue $issue) use ($tagIds, $userIds): void {
                    $issue->tags()->attach(
                        $tagIds->random(fake()->numberBetween(1, 3))->all()
                    );
                    $issue->members()->attach(
                        $userIds->random(fake()->numberBetween(1, 2))->all()
                    );
                });
        });
    }
}
