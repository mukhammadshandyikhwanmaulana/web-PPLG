<?php

namespace Tests\Unit\Models;

use App\Enums\PublishStatus;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\StudentWork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsScopeAndOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_achievement_published_scope_excludes_draft(): void
    {
        Achievement::factory()->create(['status' => PublishStatus::Published->value]);
        Achievement::factory()->create(['status' => PublishStatus::Draft->value]);

        $this->assertCount(1, Achievement::published()->get());
    }

    public function test_achievement_latest3_orders_by_achievement_date_desc(): void
    {
        $old = Achievement::factory()->create(['achievement_date' => now()->subYears(2)]);
        $mid = Achievement::factory()->create(['achievement_date' => now()->subYear()]);
        $new = Achievement::factory()->create(['achievement_date' => now()]);

        $result = Achievement::latest3()->pluck('id');

        $this->assertEquals([$new->id, $mid->id, $old->id], $result->toArray());
    }

    public function test_achievement_latest3_limits_to_3(): void
    {
        Achievement::factory()->count(5)->create();

        $this->assertCount(3, Achievement::latest3()->get());
    }

    public function test_student_work_featured_scope_only_returns_featured_and_published(): void
    {
        StudentWork::factory()->create(['is_featured' => true, 'status' => PublishStatus::Published->value]);
        StudentWork::factory()->create(['is_featured' => false, 'status' => PublishStatus::Published->value]);
        StudentWork::factory()->create(['is_featured' => true, 'status' => PublishStatus::Draft->value]);

        $this->assertCount(1, StudentWork::featured()->get());
    }

    public function test_activity_latest6_orders_by_event_date_desc_and_limits_6(): void
    {
        Activity::factory()->count(8)->sequence(
            fn ($sequence) => ['event_date' => now()->subDays($sequence->index)]
        )->create();

        $result = Activity::latest6()->get();

        $this->assertCount(6, $result);
        $this->assertTrue($result->first()->event_date->gte($result->last()->event_date));
    }
}