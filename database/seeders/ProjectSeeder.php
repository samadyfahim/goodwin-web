<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create users
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        $users = User::factory(5)->create();
        $goodwin = User::where('email', 'valves@goodwingroup.com')->first();

        // Get customers
        $customers = Customer::all();

        // Create sample projects
        Project::factory(15)->create([
            'created_by' => $admin->id,
        ])->each(function ($project) use ($users, $customers, $goodwin) {
            // Assign 3-8 random users to project
            $team = $users->random(rand(3, min(8, $users->count())))->pluck('id')->toArray();
            // Add Goodwin to some projects
            if ($goodwin && rand(0, 1)) {
                $team[] = $goodwin->id;
            }
            $project->users()->attach($team);

            // Assign 1-3 random customers to project
            if ($customers->count() > 0) {
                $project->customers()->attach(
                    $customers->random(rand(1, min(3, $customers->count())))->pluck('id')->toArray()
                );
            }
        });

        // Create some specific projects for testing
        $project1 = Project::create([
            'name' => 'Website Redesign',
            'description' => fake()->optional()->paragraphs(30, true),
            'status' => 'active',
            'deadline' => now()->addMonths(3),
            'created_by' => $admin->id,
        ]);

        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => fake()->optional()->paragraphs(30, true),
            'status' => 'planned',
            'deadline' => now()->addMonths(6),
            'created_by' => $admin->id,
        ]);

        // Assign users and customers to specific projects
        $project1Team = $users->random(rand(3, min(8, $users->count())))->pluck('id')->toArray();
        if ($goodwin) { $project1Team[] = $goodwin->id; }
        $project1->users()->attach($project1Team);

        $project2Team = $users->random(rand(3, min(8, $users->count())))->pluck('id')->toArray();
        if ($goodwin && rand(0, 1)) { $project2Team[] = $goodwin->id; }
        $project2->users()->attach($project2Team);

        if ($customers->count() > 0) {
            $project1->customers()->attach($customers->random(rand(1, min(3, $customers->count())))->pluck('id')->toArray());
            $project2->customers()->attach($customers->random(rand(1, min(3, $customers->count())))->pluck('id')->toArray());
        }
    }
} 