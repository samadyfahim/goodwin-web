<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
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

        $projects = Project::all();
        $goodwin = User::where('email', 'valves@goodwingroup.com')->first();

        if ($projects->isEmpty()) {
            return;
        }

        // For each project, create 5-10 notes
        foreach ($projects as $project) {
            $team = $project->users;
            for ($i = 0; $i < rand(5, 10); $i++) {
                $author = $team->isNotEmpty() ? $team->random() : $admin;
                Note::factory()->create([
                    'project_id' => $project->id,
                    'created_by' => $author->id,
                ]);
            }
        }
    }
} 