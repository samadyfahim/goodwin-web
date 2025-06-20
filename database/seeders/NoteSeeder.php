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

        if ($projects->isEmpty()) {
            return;
        }

        // Create sample notes
        Note::factory(25)->create([
            'created_by' => $admin->id,
        ]);

        // Create some specific notes for testing
        $project = $projects->first();
        
        Note::create([
            'project_id' => $project->id,
            'content' => "Project kickoff meeting notes:\n\n- Discussed project scope and timeline\n- Identified key stakeholders\n- Set initial milestones\n- Agreed on communication channels\n\nNext steps: Create detailed project plan and assign team members.",
            'created_by' => $admin->id,
        ]);

        Note::create([
            'project_id' => $project->id,
            'content' => "Technical requirements review:\n\n- Frontend: React.js with TypeScript\n- Backend: Laravel API\n- Database: MySQL\n- Hosting: AWS\n- CI/CD: GitHub Actions\n\nAll team members should review these requirements and provide feedback.",
            'created_by' => $admin->id,
        ]);

        Note::create([
            'project_id' => $project->id,
            'content' => "Client feedback from latest demo:\n\n✅ Positive feedback:\n- Clean and intuitive design\n- Fast loading times\n- Good mobile responsiveness\n\n⚠️ Areas for improvement:\n- Add more color options\n- Include additional payment methods\n- Improve search functionality\n\nAction items: Address feedback in next sprint.",
            'created_by' => $admin->id,
        ]);
    }
} 