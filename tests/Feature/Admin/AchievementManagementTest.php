<?php

namespace Tests\Feature\Admin;

use App\Enums\AchievementLevel;
use App\Enums\PublishStatus;
use App\Models\Achievement;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AchievementManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        return $admin;
    }

    protected function guru(): User
    {
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $guru = User::factory()->create(['is_active' => true]);
        $guru->assignRole('guru');

        return $guru;
    }

    // ACCESS
    public function test_guest_is_redirected_from_achievement_index(): void
    {
        $this->get(route('admin.prestasi.index'))->assertRedirect(route('admin.login'));
    }

    public function test_guru_cannot_access_achievement_management(): void
    {
        $this->actingAs($this->guru())
            ->get(route('admin.prestasi.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_achievement_index(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.prestasi.index'))
            ->assertOk();
    }

    // CREATE
    public function test_admin_can_create_achievement(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.prestasi.store'), [
            'title' => 'Juara 1 LKS Tingkat Provinsi',
            'level' => AchievementLevel::Provinsi->value,
            'achievement_date' => '2026-05-10',
            'contributor_name' => 'Budi Santoso',
            'status' => PublishStatus::Draft->value,
        ]);

        $response->assertRedirect(route('admin.prestasi.index'));
        $this->assertDatabaseHas('achievements', [
            'title' => 'Juara 1 LKS Tingkat Provinsi',
            'level' => AchievementLevel::Provinsi->value,
            'status' => PublishStatus::Draft->value,
        ]);
    }

    public function test_created_by_is_set_from_authenticated_admin(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.prestasi.store'), [
            'title' => 'Prestasi Uji',
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertDatabaseHas('achievements', [
            'title' => 'Prestasi Uji',
            'created_by' => $admin->id,
        ]);
    }

    public function test_created_by_cannot_be_injected_from_request(): void
    {
        $admin = $this->admin();
        $intruder = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.prestasi.store'), [
            'title' => 'Prestasi Injeksi',
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Draft->value,
            'created_by' => $intruder->id,
        ]);

        $this->assertDatabaseHas('achievements', [
            'title' => 'Prestasi Injeksi',
            'created_by' => $admin->id,
        ]);
        $this->assertDatabaseMissing('achievements', [
            'created_by' => $intruder->id,
        ]);
    }

    public function test_slug_is_generated_automatically_from_title(): void
    {
        $this->actingAs($this->admin())->post(route('admin.prestasi.store'), [
            'title' => 'Juara Dua Lomba Coding',
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Kabupaten->value,
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertDatabaseHas('achievements', ['slug' => 'juara-dua-lomba-coding']);
    }

    public function test_slug_collision_generates_safe_suffix(): void
    {
        Achievement::factory()->create(['slug' => 'juara-umum']);

        $this->actingAs($this->admin())->post(route('admin.prestasi.store'), [
            'title' => 'Juara Umum',
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertDatabaseHas('achievements', ['slug' => 'juara-umum-1']);
    }

    public function test_invalid_level_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.prestasi.store'), [
                'title' => 'Prestasi Invalid',
                'achievement_date' => '2026-01-01',
                'level' => 'tidak-ada-level-ini',
                'status' => PublishStatus::Draft->value,
            ])
            ->assertSessionHasErrors('level');
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.prestasi.store'), [
                'title' => 'Prestasi Invalid Status',
                'achievement_date' => '2026-01-01',
                'level' => AchievementLevel::Sekolah->value,
                'status' => 'archived',
            ])
            ->assertSessionHasErrors('status');
    }

    // DATE
    public function test_invalid_date_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.prestasi.store'), [
                'title' => 'Prestasi Tanggal Salah',
                'level' => AchievementLevel::Sekolah->value,
                'achievement_date' => 'bukan-tanggal',
                'status' => PublishStatus::Draft->value,
            ])
            ->assertSessionHasErrors('achievement_date');
    }

    // DOCUMENT
    public function test_image_document_upload_is_accepted(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post(route('admin.prestasi.store'), [
            'title' => 'Prestasi Dengan Foto',
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Draft->value,
            'document' => UploadedFile::fake()->image('sertifikat.jpg'),
        ]);

        $achievement = Achievement::where('title', 'Prestasi Dengan Foto')->first();
        $this->assertNotNull($achievement->document_media_id);
        Storage::disk('public')->assertExists($achievement->document->file_path);
    }

    public function test_pdf_document_upload_is_accepted(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post(route('admin.prestasi.store'), [
            'title' => 'Prestasi Dengan PDF',
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Draft->value,
            'document' => UploadedFile::fake()->create('sertifikat.pdf', 500, 'application/pdf'),
        ]);

        $achievement = Achievement::where('title', 'Prestasi Dengan PDF')->first();
        $this->assertNotNull($achievement->document_media_id);
    }

    public function test_unsupported_document_format_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.prestasi.store'), [
                'title' => 'Prestasi Format Salah',
                'achievement_date' => '2026-01-01',
                'level' => AchievementLevel::Sekolah->value,
                'status' => PublishStatus::Draft->value,
                'document' => UploadedFile::fake()->create('script.exe', 100),
            ])
            ->assertSessionHasErrors('document');
    }

    public function test_oversized_document_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.prestasi.store'), [
                'title' => 'Prestasi Terlalu Besar',
                'achievement_date' => '2026-01-01',
                'level' => AchievementLevel::Sekolah->value,
                'status' => PublishStatus::Draft->value,
                'document' => UploadedFile::fake()->create('besar.pdf', 6000, 'application/pdf'),
            ])
            ->assertSessionHasErrors('document');
    }

    // UPDATE
    public function test_admin_can_update_achievement(): void
    {
        $achievement = Achievement::factory()->create(['title' => 'Judul Lama']);

        $this->actingAs($this->admin())->put(route('admin.prestasi.update', $achievement), [
            'title' => 'Judul Baru',
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Nasional->value,
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertDatabaseHas('achievements', ['id' => $achievement->id, 'title' => 'Judul Baru']);
    }

    public function test_updated_by_is_set_on_update(): void
    {
        $admin = $this->admin();
        $achievement = Achievement::factory()->create();

        $this->actingAs($admin)->put(route('admin.prestasi.update', $achievement), [
            'title' => $achievement->title,
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertDatabaseHas('achievements', ['id' => $achievement->id, 'updated_by' => $admin->id]);
    }

    public function test_document_replacement_does_not_break_foreign_key(): void
    {
        Storage::fake('public');

        $oldMedia = Media::create([
            'file_name' => 'lama.jpg',
            'file_path' => 'achievements/lama.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'uploaded_by' => null,
        ]);
        
        $achievement = Achievement::factory()->create(['document_media_id' => $oldMedia->id]);

        $this->actingAs($this->admin())->put(route('admin.prestasi.update', $achievement), [
            'title' => $achievement->title,
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Draft->value,
            'document' => UploadedFile::fake()->image('baru.jpg'),
        ]);

        $achievement->refresh();
        $this->assertNotEquals($oldMedia->id, $achievement->document_media_id);
        $this->assertDatabaseHas('media', ['id' => $oldMedia->id]); // media lama tetap ada (known limitation)
    }

    // DELETE
    public function test_destroy_soft_deletes_achievement(): void
    {
        $achievement = Achievement::factory()->create();

        $this->actingAs($this->admin())->delete(route('admin.prestasi.destroy', $achievement));

        $this->assertSoftDeleted('achievements', ['id' => $achievement->id]);
    }

    public function test_soft_deleted_achievement_does_not_appear_in_default_index_query(): void
    {
        $achievement = Achievement::factory()->create();
        $achievement->delete();

        $this->assertDatabaseHas('achievements', ['id' => $achievement->id]);
        $this->assertCount(0, Achievement::where('id', $achievement->id)->get());
    }

    // PUBLISHING
    public function test_draft_does_not_appear_in_published_scope(): void
    {
        Achievement::factory()->create(['status' => PublishStatus::Draft]);

        $this->assertCount(0, Achievement::published()->get());
    }

    public function test_published_appears_in_published_scope(): void
    {
        Achievement::factory()->create(['status' => PublishStatus::Published, 'published_at' => now()]);

        $this->assertCount(1, Achievement::published()->get());
    }

    public function test_published_at_is_filled_on_first_publish(): void
    {
        $achievement = Achievement::factory()->create([
            'status' => PublishStatus::Draft,
            'published_at' => null,
        ]);

        $this->actingAs($this->admin())->put(route('admin.prestasi.update', $achievement), [
            'title' => $achievement->title,
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Published->value,
        ]);

        $achievement->refresh();
        $this->assertNotNull($achievement->published_at);
    }

    public function test_published_at_is_preserved_when_reverted_to_draft(): void
    {
        $originalPublishedAt = now()->subDays(5);
        $achievement = Achievement::factory()->create([
            'status' => PublishStatus::Published,
            'published_at' => $originalPublishedAt,
        ]);

        $this->actingAs($this->admin())->put(route('admin.prestasi.update', $achievement), [
            'title' => $achievement->title,
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Draft->value,
        ]);

        $achievement->refresh();
        $this->assertEquals(
            $originalPublishedAt->format('Y-m-d H:i:s'),
            $achievement->published_at->format('Y-m-d H:i:s')
        );
    }

    // ORDERING
    public function test_latest3_orders_by_achievement_date_desc(): void
    {
        Achievement::factory()->create([
            'status' => PublishStatus::Published, 'published_at' => now(),
            'achievement_date' => '2025-01-01',
        ]);
        $newest = Achievement::factory()->create([
            'status' => PublishStatus::Published, 'published_at' => now(),
            'achievement_date' => '2026-06-01',
        ]);

        $this->assertEquals($newest->id, Achievement::latest3()->first()->id);
    }

    // SECURITY
    public function test_mass_assignment_ignores_undefined_fields(): void
    {
        $this->actingAs($this->admin())->post(route('admin.prestasi.store'), [
            'title' => 'Prestasi Aman',
            'achievement_date' => '2026-01-01',
            'level' => AchievementLevel::Sekolah->value,
            'status' => PublishStatus::Draft->value,
            'id' => 99999,
        ]);

        $this->assertDatabaseMissing('achievements', ['id' => 99999]);
    }
}