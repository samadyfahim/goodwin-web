<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    // /**
    //  * Define the model's default state.
    //  *
    //  * @return array<string, mixed>
    //  */
    // public function definition(): array
    // {
    //     $filename = fake()->randomElement([
    //         'document.pdf',
    //         'presentation.pptx',
    //         'spreadsheet.xlsx',
    //         'image.jpg',
    //         'report.docx',
    //         'design.sketch',
    //         'code.zip',
    //         'video.mp4'
    //     ]);

    //     return [
    //         'project_id' => fake()->optional()->randomElement([Project::factory(), null]),
    //         'task_id' => fake()->optional()->randomElement([Task::factory(), null]),
    //         'filename' => $filename,
    //         'filepath' => 'uploads/' . fake()->uuid() . '/' . $filename,
    //         'created_by' => User::factory(),
    //     ];
    // }
} 