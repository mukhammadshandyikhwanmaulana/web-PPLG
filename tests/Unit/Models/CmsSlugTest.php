<?php

namespace Tests\Unit\Models;

use App\Models\Achievement;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_slug_is_rejected_by_database(): void
    {
        Achievement::factory()->create(['slug' => 'prestasi-sama']);

        $this->expectException(QueryException::class);

        Achievement::factory()->create(['slug' => 'prestasi-sama']);
    }
}