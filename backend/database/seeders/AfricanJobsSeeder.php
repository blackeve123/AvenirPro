<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Step;
use Illuminate\Support\Facades\DB;

class AfricanJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to the JSON file
        $jsonPath = database_path('data/african_jobs.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error("The file $jsonPath does not exist. Please ensure the JSON file is generated.");
            return;
        }

        $jsonContent = file_get_contents($jsonPath);
        $jobsData = json_decode($jsonContent, true);

        if (!$jobsData) {
            $this->command->error("The file $jsonPath contains invalid JSON.");
            return;
        }

        $this->command->info("Seeding " . count($jobsData) . " African Jobs...");

        DB::beginTransaction();

        try {
            foreach ($jobsData as $data) {
                // Get or create category
                $category = JobCategory::firstOrCreate(
                    ['name' => $data['category']],
                    ['description' => 'Secteur d\'activité : ' . $data['category']]
                );

                // Convert riasec_types array to string comma separated
                $riasecString = implode(',', $data['riasec_types']);

                // Create or update the Job
                $job = Job::updateOrCreate(
                    ['title' => $data['title']], // Avoid duplicates if run twice
                    [
                        'category_id'  => $category->id,
                        'description'  => $data['description'],
                        'sector'       => $data['sector'],
                        'salary_range' => $data['salary_range'],
                        'riasec_types' => $riasecString,
                        'image_path'   => $data['image_path'],
                    ]
                );

                // If job has no steps (meaning it was just created or steps were wiped), add them
                if ($job->steps()->count() === 0 && isset($data['steps'])) {
                    $order = 1;
                    foreach ($data['steps'] as $stepData) {
                        Step::create([
                            'job_id'      => $job->id,
                            'title'       => $stepData['title'],
                            'description' => $stepData['description'],
                            'duration'    => $stepData['duration'] ?? null,
                            'order'       => $order++,
                        ]);
                    }
                }
            }

            DB::commit();
            $this->command->info("Successfully seeded African Jobs!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Failed to seed jobs: " . $e->getMessage());
        }
    }
}
