<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');

        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'start_date' => $startDate,
            'deadline' => fake()->dateTimeBetween($startDate, '+3 months'),
        ];
    }
}
