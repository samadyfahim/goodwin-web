<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Goodwin user
        $goodwin = \App\Models\User::firstOrCreate([
            'email' => 'valves@goodwingroup.com',
        ], [
            'name' => 'Goodwin',
            'password' => bcrypt('12345678'),
        ]);

        // Create the main admin user first
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Call seeders in order to maintain referential integrity
        $this->call([
            CustomerSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
            FileSeeder::class,
            NoteSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
