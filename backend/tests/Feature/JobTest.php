<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_paginated_jobs()
    {
        $category = JobCategory::factory()->create();
        Job::factory()->count(15)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/v1/jobs');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'title', 'category']
                     ],
                     'current_page',
                     'total'
                 ]);

        $this->assertEquals(10, count($response->json('data')));
    }

    public function test_can_search_jobs()
    {
        $category = JobCategory::factory()->create();
        Job::factory()->create(['title' => 'Software Engineer', 'category_id' => $category->id]);
        Job::factory()->create(['title' => 'Graphic Designer', 'category_id' => $category->id]);

        $response = $this->getJson('/api/v1/jobs?search=Software');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals('Software Engineer', $response->json('data.0.title'));
    }

    public function test_can_filter_jobs_by_category_id()
    {
        $category1 = JobCategory::factory()->create();
        $category2 = JobCategory::factory()->create();

        Job::factory()->create(['category_id' => $category1->id]);
        Job::factory()->create(['category_id' => $category2->id]);

        $response = $this->getJson('/api/v1/jobs?category=' . $category1->id);

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }
}
