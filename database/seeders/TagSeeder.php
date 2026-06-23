<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'bug' => '#ef4444',
            'feature' => '#3b82f6',
            'enhancement' => '#8b5cf6',
            'urgent' => '#f97316',
            'documentation' => '#10b981',
            'question' => '#eab308',
        ];

        foreach ($tags as $name => $color) {
            Tag::query()->firstOrCreate(['name' => $name], ['color' => $color]);
        }
    }
}
