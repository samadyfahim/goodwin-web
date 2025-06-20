<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
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
        $notes = Note::all();

        if ($notes->isEmpty()) {
            return;
        }

        // Create sample comments
        Comment::factory(40)->create([
            'created_by' => $admin->id,
        ]);

        // Create some specific comments for testing
        $note = $notes->first();
        
        Comment::create([
            'note_id' => $note->id,
            'content' => 'Great meeting! I think we should also consider adding analytics tracking to monitor user engagement.',
            'created_by' => $admin->id,
        ]);

        Comment::create([
            'note_id' => $note->id,
            'content' => 'I agree with the timeline. We should also set up weekly check-ins to keep everyone on track.',
            'created_by' => $users->random()->id,
        ]);

        Comment::create([
            'note_id' => $note->id,
            'content' => 'Don\'t forget to include the QA team in the communication plan. They\'ll need early access for testing.',
            'created_by' => $users->random()->id,
        ]);

        // Create comments for other notes
        foreach ($notes->take(5) as $note) {
            Comment::factory(rand(1, 3))->create([
                'note_id' => $note->id,
                'created_by' => $users->random()->id,
            ]);
        }
    }
} 