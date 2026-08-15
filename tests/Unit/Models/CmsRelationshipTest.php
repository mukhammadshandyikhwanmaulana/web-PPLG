<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Media;
use App\Models\PrincipalWelcome;
use App\Models\StaffMember;
use App\Models\StudentWork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_member_has_many_student_works(): void
    {
        $staff = StaffMember::factory()->create();
        StudentWork::factory()->count(3)->create(['supervisor_id' => $staff->id]);

        $this->assertCount(3, $staff->studentWorks);
    }

    public function test_student_work_belongs_to_supervisor(): void
    {
        $staff = StaffMember::factory()->create();
        $work = StudentWork::factory()->create(['supervisor_id' => $staff->id]);

        $this->assertTrue($work->supervisor->is($staff));
    }

    public function test_principal_welcome_belongs_to_staff_member(): void
    {
        $staff = StaffMember::factory()->create();
        $welcome = PrincipalWelcome::create([
            'staff_member_id' => $staff->id,
            'content' => 'Sambutan contoh.',
        ]);

        $this->assertTrue($welcome->staffMember->is($staff));
    }

    public function test_student_work_gallery_is_polymorphic(): void
    {
        $work = StudentWork::factory()->create();
        $media = Media::create([
            'file_name' => 'contoh.jpg',
            'file_path' => 'student-works/contoh.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
        ]);

        $work->galleries()->create(['media_id' => $media->id, 'sort_order' => 0]);

        $this->assertCount(1, $work->galleries);
        $this->assertEquals(StudentWork::class, $work->galleries->first()->galleryable_type);
    }

    public function test_activity_gallery_is_polymorphic_and_independent_from_student_work(): void
    {
        $activity = Activity::factory()->create();
        $media = Media::create([
            'file_name' => 'kegiatan.jpg',
            'file_path' => 'activities/kegiatan.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 2048,
        ]);

        $activity->galleries()->create(['media_id' => $media->id, 'sort_order' => 0]);

        $this->assertCount(1, $activity->galleries);
        $this->assertEquals(Activity::class, $activity->galleries->first()->galleryable_type);
    }
}