<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        $users = User::all();
        $projects = Project::all();

        if ($projects->isEmpty()) {
            return;
        }

        // Create sample tasks
        Task::factory(50)->create([
            'created_by' => $admin->id,
        ])->each(function ($task) use ($users) {
            // Get users assigned to this task's project
            $projectUsers = $task->project->users;
            
            if ($projectUsers->count() > 0) {
                // Assign random users from the project to the task
                $task->users()->attach(
                    $projectUsers->random(rand(1, min(3, $projectUsers->count())))->pluck('id')->toArray()
                );
            }
        });

        // Create some specific tasks for testing
        $project = $projects->first();
        
        $task1 = Task::create([
            'project_id' => $project->id,
            'name' => 'Design Homepage Layout',
            'description' => 'Create wireframes and mockups for the homepage redesign',
            'status' => 'in_progress',
            'created_by' => $admin->id,
        ]);

        $task2 = Task::create([
            'project_id' => $project->id,
            'name' => 'Implement User Authentication',
            'description' => 'Set up login, registration, and password reset functionality',
            'status' => 'to_do',
            'created_by' => $admin->id,
        ]);

        $task3 = Task::create([
            'project_id' => $project->id,
            'name' => 'Write API Documentation',
            'description' => 'Create comprehensive API documentation for developers',
            'status' => 'done',
            'created_by' => $admin->id,
        ]);

        // Assign users to specific tasks (only if they're assigned to the project)
        if ($project->users->count() > 0) {
            $task1->users()->attach($project->users->first()->id);
            $task2->users()->attach($project->users->take(2)->pluck('id')->toArray());
            $task3->users()->attach($project->users->random()->id);
        }
    }
} 