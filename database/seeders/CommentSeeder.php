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
        $goodwin = User::where('email', 'valves@goodwingroup.com')->first();
        $notes = Note::all();

        if ($notes->isEmpty()) {
            return;
        }

        // For each note, create 0-7 comments
        foreach ($notes as $note) {
            $numComments = rand(0, 7);
            for ($i = 0; $i < $numComments; $i++) {
                $author = $users->random();
                // Assign Goodwin as author to some comments
                if ($goodwin && rand(0, 4) === 0) {
                    $author = $goodwin;
                }
                Comment::factory()->create([
                    'note_id' => $note->id,
                    'created_by' => $author->id,
                ]);
            }
        }
    }
} 