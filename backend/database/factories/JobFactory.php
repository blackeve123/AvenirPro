<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id'  => 1, // Handled dynamically in tests usually
            'title'        => $this->faker->jobTitle(),
            'description'  => $this->faker->paragraph(),
            'sector'       => $this->faker->word(),
            'salary_range' => '$50k - $80k',
            'riasec_types' => 'I,C',
            'image_path'   => null,
        ];
    }
}
