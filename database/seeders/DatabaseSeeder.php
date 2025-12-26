<?php

namespace Database\Seeders;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create active projects
        $projects = [
            [
                'title' => 'Q3 Marketing Push',
                'icon' => '🚀',
                'color' => '#1392ec',
                'priority' => Priority::High,
                'sort_order' => 0,
            ],
            [
                'title' => 'Website Redesign',
                'icon' => '🌐',
                'color' => '#8b5cf6',
                'priority' => Priority::Medium,
                'sort_order' => 1,
            ],
            [
                'title' => 'Internal Tooling',
                'icon' => '🔧',
                'color' => '#14b8a6',
                'priority' => Priority::Low,
                'sort_order' => 2,
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create([
                'user_id' => $user->id,
                ...$projectData,
            ]);

            // Create tasks for each project
            Task::factory(3)->todo()->create(['project_id' => $project->id, 'assigned_to' => $user->id]);
            Task::factory(2)->doing()->create(['project_id' => $project->id, 'assigned_to' => $user->id]);
            Task::factory(4)->done()->create(['project_id' => $project->id, 'assigned_to' => $user->id]);
        }

        // Create archived projects
        Project::create([
            'user_id' => $user->id,
            'title' => 'Q1 Legacy Data',
            'icon' => '📦',
            'color' => '#6b7280',
            'priority' => Priority::Low,
            'sort_order' => 0,
            'archived_at' => now()->subDays(30),
        ]);

        Project::create([
            'user_id' => $user->id,
            'title' => 'Mobile App V1',
            'icon' => '📱',
            'color' => '#6b7280',
            'priority' => Priority::Medium,
            'sort_order' => 1,
            'archived_at' => now()->subDays(15),
        ]);
    }
}
