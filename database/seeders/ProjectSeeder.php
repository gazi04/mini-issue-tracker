<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds. Each existing user owns two projects.
     */
    public function run(): void
    {
        User::all()->each(function (User $user): void {
            Project::factory()
                ->count(2)
                ->for($user, 'owner')
                ->create();
        });
    }
}
