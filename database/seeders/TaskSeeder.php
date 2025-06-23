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
        $goodwin = User::where('email', 'valves@goodwingroup.com')->first();
        $projects = Project::all();

        if ($projects->isEmpty()) {
            return;
        }

        // For each project, create 3-4 tasks and assign users
        foreach ($projects as $project) {
            $team = $project->users;
            for ($i = 0; $i < rand(3, 4); $i++) {
                $task = Task::factory()->create([
                    'project_id' => $project->id,
                    'created_by' => $team->random()->id,
                ]);
                $assignees = $team->random(rand(1, min(3, $team->count())))->pluck('id')->toArray();
                // Assign Goodwin to some tasks if he's a team member
                if ($goodwin && $team->contains($goodwin) && rand(0, 1)) {
                    $assignees[] = $goodwin->id;
                }
                $task->users()->attach(array_unique($assignees));
            }
        }
    }
} 