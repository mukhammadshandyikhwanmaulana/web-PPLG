<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CmsMigrationStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_cms_tables_exist(): void
    {
        $tables = [
            'media', 'galleries', 'profile_contents', 'staff_members',
            'principal_welcomes', 'facilities', 'achievements',
            'student_works', 'activities', 'industry_partners', 'unit_usaha_links',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table [$table] should exist.");
        }
    }

    public function test_achievements_has_achievement_date_not_year(): void
    {
        $this->assertTrue(Schema::hasColumn('achievements', 'achievement_date'));
        $this->assertFalse(Schema::hasColumn('achievements', 'year'));
    }

    public function test_profile_contents_has_about_excerpt(): void
    {
        $this->assertTrue(Schema::hasColumn('profile_contents', 'about_excerpt'));
    }

    public function test_principal_welcomes_references_staff_member_not_duplicated_fields(): void
    {
        $this->assertTrue(Schema::hasColumn('principal_welcomes', 'staff_member_id'));
        $this->assertFalse(Schema::hasColumn('principal_welcomes', 'principal_name'));
        $this->assertFalse(Schema::hasColumn('principal_welcomes', 'photo_media_id'));
    }

    public function test_no_berita_related_tables_exist(): void
    {
        foreach (['news', 'berita', 'articles'] as $table) {
            $this->assertFalse(Schema::hasTable($table), "Table [$table] should NOT exist.");
        }
    }

    public function test_no_business_units_table_exists(): void
    {
        $this->assertFalse(Schema::hasTable('business_units'));
        $this->assertTrue(Schema::hasTable('unit_usaha_links'));
    }
}