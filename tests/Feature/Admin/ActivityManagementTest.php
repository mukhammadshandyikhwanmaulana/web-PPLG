<?php

namespace Tests\Feature\Admin;

use App\Enums\PublishStatus;
use App\Models\Activity;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
    }

    protected function admin(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('admin');
        return $u;
    }

    protected function guru(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('guru');
        return $u;
    }

    // ACCESS
    public function test_guest_denied(): void
    {
        $this->get(route('admin.kegiatan.index'))->assertRedirect(route('admin.login'));
    }

    public function test_guru_denied(): void
    {
        $this->actingAs($this->guru())->get(route('admin.kegiatan.index'))->assertForbidden();
    }

    public function test_admin_allowed(): void
    {
        $this->actingAs($this->admin())->get(route('admin.kegiatan.index'))->assertOk();
    }

    // CREATE
    public function test_admin_can_create_activity(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Workshop Laravel',
            'event_date' => '2026-01-15',
            'status' => PublishStatus::Draft->value,
        ])->assertRedirect(route('admin.kegiatan.index'));

        $this->assertDatabaseHas('activities', [
            'title' => 'Workshop Laravel',
            'slug' => 'workshop-laravel',
        ]);
    }

    public function test_past_event_date_is_allowed(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Lalu',
            'event_date' => '2020-01-01',
            'status' => PublishStatus::Draft->value,
        ])->assertRedirect(route('admin.kegiatan.index'));

        $this->assertDatabaseHas('activities', ['title' => 'Kegiatan Lalu']);
    }

    public function test_future_event_date_is_allowed(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Mendatang',
            'event_date' => now()->addYear()->format('Y-m-d'),
            'status' => PublishStatus::Draft->value,
        ])->assertRedirect(route('admin.kegiatan.index'));

        $this->assertDatabaseHas('activities', ['title' => 'Kegiatan Mendatang']);
    }

    public function test_cover_upload_creates_media_and_links(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Cover',
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Draft->value,
            'cover' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $activity = Activity::where('title', 'Kegiatan Cover')->firstOrFail();
        $this->assertNotNull($activity->cover_media_id);
        Storage::disk('public')->assertExists($activity->cover->file_path);
    }

    public function test_gallery_images_are_attached(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Galeri',
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Draft->value,
            'images' => [
                UploadedFile::fake()->image('a.jpg'),
                UploadedFile::fake()->image('b.jpg'),
            ],
        ]);

        $activity = Activity::where('title', 'Kegiatan Galeri')->firstOrFail();
        $this->assertCount(2, $activity->galleries);
    }

    public function test_more_than_8_gallery_images_rejected(): void
    {
        $images = collect(range(1, 9))->map(fn () => UploadedFile::fake()->image('x.jpg'))->all();

        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Banyak Gambar',
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Draft->value,
            'images' => $images,
        ])->assertSessionHasErrors('images');
    }

    public function test_exactly_8_gallery_images_accepted(): void
    {
        $images = collect(range(1, 8))->map(fn () => UploadedFile::fake()->image('x.jpg'))->all();

        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Delapan Gambar',
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Draft->value,
            'images' => $images,
        ])->assertSessionHasNoErrors();

        $activity = Activity::where('title', 'Kegiatan Delapan Gambar')->firstOrFail();
        $this->assertCount(8, $activity->galleries);
    }

    public function test_oversized_gallery_image_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Besar',
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Draft->value,
            'images' => [UploadedFile::fake()->image('big.jpg')->size(3000)],
        ])->assertSessionHasErrors('images.0');
    }

    public function test_pdf_gallery_upload_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan PDF',
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Draft->value,
            'images' => [UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')],
        ])->assertSessionHasErrors('images.0');
    }

    public function test_pdf_cover_upload_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Cover PDF',
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Draft->value,
            'cover' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('cover');
    }

    public function test_title_is_required(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Draft->value,
        ])->assertSessionHasErrors('title');
    }

    public function test_event_date_is_required(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Tanpa Tanggal',
            'status' => PublishStatus::Draft->value,
        ])->assertSessionHasErrors('event_date');
    }

    public function test_invalid_event_date_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Tanggal Salah',
            'event_date' => 'not-a-date',
            'status' => PublishStatus::Draft->value,
        ])->assertSessionHasErrors('event_date');
    }

    public function test_invalid_status_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Status Salah',
            'event_date' => '2026-01-01',
            'status' => 'archived',
        ])->assertSessionHasErrors('status');
    }

    // PUBLISHING
    public function test_published_at_set_when_first_published(): void
    {
        $this->actingAs($this->admin())->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Publish',
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Published->value,
        ]);

        $activity = Activity::where('title', 'Kegiatan Publish')->firstOrFail();
        $this->assertNotNull($activity->published_at);
    }

    public function test_republish_does_not_overwrite_published_at(): void
    {
        $admin = $this->admin();
        $activity = Activity::factory()->create([
            'status' => PublishStatus::Published->value,
            'published_at' => now()->subDays(5),
        ]);
        $original = $activity->published_at;

        $this->actingAs($admin)->put(route('admin.kegiatan.update', $activity), [
            'title' => $activity->title,
            'event_date' => $activity->event_date->format('Y-m-d'),
            'status' => PublishStatus::Draft->value,
        ]);
        $this->actingAs($admin)->put(route('admin.kegiatan.update', $activity->fresh()), [
            'title' => $activity->title,
            'event_date' => $activity->event_date->format('Y-m-d'),
            'status' => PublishStatus::Published->value,
        ]);

        $this->assertEquals($original->timestamp, $activity->fresh()->published_at->timestamp);
    }

    // UPDATE
    public function test_slug_stable_when_title_unchanged(): void
    {
        $admin = $this->admin();
        $activity = Activity::factory()->create(['title' => 'Judul Tetap', 'slug' => 'judul-tetap-custom']);

        $this->actingAs($admin)->put(route('admin.kegiatan.update', $activity), [
            'title' => 'Judul Tetap',
            'event_date' => $activity->event_date->format('Y-m-d'),
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertEquals('judul-tetap-custom', $activity->fresh()->slug);
    }

    public function test_slug_regenerated_when_title_changes(): void
    {
        $admin = $this->admin();
        $activity = Activity::factory()->create(['title' => 'Judul Lama']);

        $this->actingAs($admin)->put(route('admin.kegiatan.update', $activity), [
            'title' => 'Judul Baru Sekali',
            'event_date' => $activity->event_date->format('Y-m-d'),
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertEquals('judul-baru-sekali', $activity->fresh()->slug);
    }

    public function test_cover_replacement_updates_reference(): void
    {
        $admin = $this->admin();
        $oldMedia = Media::factory()->create();
        $activity = Activity::factory()->create(['cover_media_id' => $oldMedia->id]);

        $this->actingAs($admin)->put(route('admin.kegiatan.update', $activity), [
            'title' => $activity->title,
            'event_date' => $activity->event_date->format('Y-m-d'),
            'status' => PublishStatus::Draft->value,
            'cover' => UploadedFile::fake()->image('new-cover.jpg'),
        ]);

        $this->assertNotEquals($oldMedia->id, $activity->fresh()->cover_media_id);
    }

    public function test_cover_unchanged_when_no_new_file_uploaded(): void
    {
        $admin = $this->admin();
        $media = Media::factory()->create();
        $activity = Activity::factory()->create(['cover_media_id' => $media->id]);

        $this->actingAs($admin)->put(route('admin.kegiatan.update', $activity), [
            'title' => 'Judul Diperbarui',
            'event_date' => $activity->event_date->format('Y-m-d'),
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertEquals($media->id, $activity->fresh()->cover_media_id);
    }

    public function test_removing_gallery_does_not_affect_cover(): void
    {
        $admin = $this->admin();
        $media = Media::factory()->create();
        $activity = Activity::factory()->create(['cover_media_id' => $media->id]);
        $galleryMedia = Media::factory()->create();
        $gallery = Gallery::create([
            'media_id' => $galleryMedia->id,
            'galleryable_id' => $activity->id,
            'galleryable_type' => Activity::class,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)->put(route('admin.kegiatan.update', $activity), [
            'title' => $activity->title,
            'event_date' => $activity->event_date->format('Y-m-d'),
            'status' => PublishStatus::Draft->value,
            'remove_gallery_ids' => [$gallery->id],
        ]);

        $this->assertEquals($media->id, $activity->fresh()->cover_media_id);
        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    }

    // DELETE
    public function test_destroy_soft_deletes(): void
    {
        $admin = $this->admin();
        $activity = Activity::factory()->create();

        $this->actingAs($admin)->delete(route('admin.kegiatan.destroy', $activity));

        $this->assertSoftDeleted('activities', ['id' => $activity->id]);
        $this->assertDatabaseMissing('activities', ['id' => $activity->id, 'deleted_at' => null]);
    }

    // SECURITY
    public function test_created_by_cannot_be_injected(): void
    {
        $admin = $this->admin();
        $other = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.kegiatan.store'), [
            'title' => 'Kegiatan Injeksi',
            'event_date' => '2026-01-01',
            'status' => PublishStatus::Draft->value,
            'created_by' => $other->id,
        ]);

        $activity = Activity::where('title', 'Kegiatan Injeksi')->firstOrFail();
        $this->assertEquals($admin->id, $activity->created_by);
    }

    public function test_remove_gallery_ids_scoped_to_own_activity(): void
    {
        $admin = $this->admin();
        $activityA = Activity::factory()->create();
        $activityB = Activity::factory()->create();
        $mediaB = Media::factory()->create();
        $galleryB = Gallery::create([
            'media_id' => $mediaB->id,
            'galleryable_id' => $activityB->id,
            'galleryable_type' => Activity::class,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)->put(route('admin.kegiatan.update', $activityA), [
            'title' => $activityA->title,
            'event_date' => $activityA->event_date->format('Y-m-d'),
            'status' => PublishStatus::Draft->value,
            'remove_gallery_ids' => [$galleryB->id],
        ]);

        $this->assertDatabaseHas('galleries', ['id' => $galleryB->id]);
    }

    public function test_deleting_one_gallery_does_not_delete_others(): void
    {
        $admin = $this->admin();
        $activity = Activity::factory()->create();
        $media1 = Media::factory()->create();
        $media2 = Media::factory()->create();
        $gallery1 = Gallery::create(['media_id' => $media1->id, 'galleryable_id' => $activity->id, 'galleryable_type' => Activity::class, 'sort_order' => 0]);
        $gallery2 = Gallery::create(['media_id' => $media2->id, 'galleryable_id' => $activity->id, 'galleryable_type' => Activity::class, 'sort_order' => 1]);

        $this->actingAs($admin)->put(route('admin.kegiatan.update', $activity), [
            'title' => $activity->title,
            'event_date' => $activity->event_date->format('Y-m-d'),
            'status' => PublishStatus::Draft->value,
            'remove_gallery_ids' => [$gallery1->id],
        ]);

        $this->assertDatabaseMissing('galleries', ['id' => $gallery1->id]);
        $this->assertDatabaseHas('galleries', ['id' => $gallery2->id]);
    }

    // ORDERING
    public function test_index_orders_by_event_date_desc(): void
    {
        $admin = $this->admin();
        Activity::factory()->create(['title' => 'Kegiatan Lama', 'event_date' => '2020-01-01']);
        Activity::factory()->create(['title' => 'Kegiatan Baru', 'event_date' => '2026-01-01']);

        $response = $this->actingAs($admin)->get(route('admin.kegiatan.index'));

        $content = $response->getContent();
        $this->assertTrue(strpos($content, 'Kegiatan Baru') < strpos($content, 'Kegiatan Lama'));
    }

    public function test_latest6_scope_still_orders_by_event_date_desc(): void
    {
        Activity::factory()->count(3)->create(['status' => PublishStatus::Published->value, 'event_date' => '2020-01-01']);
        Activity::factory()->create(['status' => PublishStatus::Published->value, 'event_date' => '2026-06-01']);

        $latest = Activity::latest6()->first();

        $this->assertEquals('2026-06-01', $latest->event_date->format('Y-m-d'));
    }
}