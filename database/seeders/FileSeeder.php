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

        // $projects = Project::all();
        // $tasks = Task::all();

        // // Create sample files
        // File::factory(30)->create([
        //     'created_by' => $admin->id,
        // ]);

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
    }
} 