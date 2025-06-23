<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // // Get or create admin user
        // $admin = User::firstOrCreate(
        //     ['email' => 'admin@example.com'],
        //     [
        //         'name' => 'Admin User',
        //         'password' => bcrypt('password'),
        //     ]
        // );

        $projects = \App\Models\Project::all();
        $goodwin = \App\Models\User::where('email', 'valves@goodwingroup.com')->first();

        // // Create some specific files for testing
        // if ($projects->count() > 0) {
        //     $project = $projects->first();
            
        //     File::create([
        //         'project_id' => $project->id,
        //         'task_id' => null,
        //         'filename' => 'project_requirements.pdf',
        //         'filepath' => 'uploads/projects/' . $project->id . '/project_requirements.pdf',
        //         'created_by' => $admin->id,
        //     ]);

        //     File::create([
        //         'project_id' => $project->id,
        //         'task_id' => null,
        //         'filename' => 'design_mockups.sketch',
        //         'filepath' => 'uploads/projects/' . $project->id . '/design_mockups.sketch',
        //         'created_by' => $admin->id,
        //     ]);
        // }

        // if ($tasks->count() > 0) {
        //     $task = $tasks->first();
            
        //     File::create([
        //         'project_id' => null,
        //         'task_id' => $task->id,
        //         'filename' => 'task_documentation.docx',
        //         'filepath' => 'uploads/tasks/' . $task->id . '/task_documentation.docx',
        //         'created_by' => $admin->id,
        //     ]);

        //     File::create([
        //         'project_id' => null,
        //         'task_id' => $task->id,
        //         'filename' => 'screenshots.zip',
        //         'filepath' => 'uploads/tasks/' . $task->id . '/screenshots.zip',
        //         'created_by' => $admin->id,
        //     ]);
        // }

        // // Create some standalone files
        // File::create([
        //     'project_id' => null,
        //     'task_id' => null,
        //     'filename' => 'company_logo.png',
        //     'filepath' => 'uploads/general/company_logo.png',
        //     'created_by' => $admin->id,
        // ]);

        // File::create([
        //     'project_id' => null,
        //     'task_id' => null,
        //     'filename' => 'style_guide.pdf',
        //     'filepath' => 'uploads/general/style_guide.pdf',
        //     'created_by' => $admin->id,
        // ]);

        // For each project, create 5-20 files
        foreach ($projects as $project) {
            $team = $project->users;
            for ($i = 0; $i < rand(5, 20); $i++) {
                $author = $team->isNotEmpty() ? $team->random() : null;
                \App\Models\File::factory()->create([
                    'project_id' => $project->id,
                    'created_by' => $author ? $author->id : null,
                ]);
            }
        }
    }
} 