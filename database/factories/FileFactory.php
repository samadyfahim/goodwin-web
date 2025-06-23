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
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = $this->faker->randomElement([
            'document.pdf',
            'presentation.pptx',
            'spreadsheet.xlsx',
            'image.jpg',
            'report.docx',
            'design.sketch',
            'code.zip',
            'video.mp4',
            'manual.txt',
            'archive.rar',
            'photo.png',
        ]);

        return [
            'project_id' => null, // Set in seeder
            'task_id' => null, // Set in seeder if needed
            'filename' => $filename,
            'filepath' => 'uploads/' . $this->faker->uuid . '/' . $filename,
            'created_by' => User::factory(),
        ];
    }
} 