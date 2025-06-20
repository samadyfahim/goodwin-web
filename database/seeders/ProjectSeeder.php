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

        // Get customers
        $customers = Customer::all();

        // Create sample projects
        Project::factory(15)->create([
            'created_by' => $admin->id,
        ])->each(function ($project) use ($users, $customers) {
            // Assign random users to project
            $project->users()->attach(
                $users->random(rand(1, 3))->pluck('id')->toArray()
            );

            // Assign random customers to project
            if ($customers->count() > 0) {
                $project->customers()->attach(
                    $customers->random(rand(1, 2))->pluck('id')->toArray()
                );
            }
        });

        // Create some specific projects for testing
        $project1 = Project::create([
            'name' => 'Website Redesign',
            'description' => 'Complete redesign of the company website with modern UI/UX',
            'status' => 'active',
            'deadline' => now()->addMonths(3),
            'created_by' => $admin->id,
        ]);

        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => 'Development of a new mobile application for iOS and Android',
            'status' => 'planned',
            'deadline' => now()->addMonths(6),
            'created_by' => $admin->id,
        ]);

        // Assign users and customers to specific projects
        $project1->users()->attach($users->take(2)->pluck('id')->toArray());
        $project2->users()->attach($users->take(3)->pluck('id')->toArray());

        if ($customers->count() > 0) {
            $project1->customers()->attach($customers->first()->id);
            $project2->customers()->attach($customers->take(2)->pluck('id')->toArray());
        }
    }
} 