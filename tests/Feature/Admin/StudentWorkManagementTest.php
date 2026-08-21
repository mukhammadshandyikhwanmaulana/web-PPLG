<?php

namespace Tests\Feature\Admin;

use App\Enums\PublishStatus;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\StaffMember;
use App\Models\StudentWork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentWorkManagementTest extends TestCase
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
        $this->get(route('admin.karya-siswa.index'))->assertRedirect(route('admin.login'));
    }

    public function test_guru_denied(): void
    {
        $this->actingAs($this->guru())->get(route('admin.karya-siswa.index'))->assertForbidden();
    }

    public function test_admin_allowed(): void
    {
        $this->actingAs($this->admin())->get(route('admin.karya-siswa.index'))->assertOk();
    }

    // CREATE
    public function test_admin_can_create_student_work(): void
    {
        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'title' => 'Aplikasi Absensi Siswa',
            'contributor_name' => 'Budi',
            'status' => PublishStatus::Published->value,
            'is_featured' => true,
        ])->assertRedirect(route('admin.karya-siswa.index'));

        $this->assertDatabaseHas('student_works', [
            'title' => 'Aplikasi Absensi Siswa',
            'slug' => 'aplikasi-absensi-siswa',
            'is_featured' => true,
        ]);
    }

    public function test_supervisor_is_saved(): void
    {
        $staff = StaffMember::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'title' => 'Karya A',
            'status' => PublishStatus::Draft->value,
            'supervisor_id' => $staff->id,
        ]);

        $this->assertDatabaseHas('student_works', ['title' => 'Karya A', 'supervisor_id' => $staff->id]);
    }

    public function test_gallery_images_are_attached(): void
    {
        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'title' => 'Karya Gambar',
            'status' => PublishStatus::Draft->value,
            'images' => [
                UploadedFile::fake()->image('a.jpg'),
                UploadedFile::fake()->image('b.jpg'),
            ],
        ]);

        $studentWork = StudentWork::where('title', 'Karya Gambar')->firstOrFail();
        $this->assertCount(2, $studentWork->galleries);
    }

    public function test_more_than_5_images_rejected(): void
    {
        $images = collect(range(1, 6))->map(fn () => UploadedFile::fake()->image('x.jpg'))->all();

        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'title' => 'Karya Banyak Gambar',
            'status' => PublishStatus::Draft->value,
            'images' => $images,
        ])->assertSessionHasErrors('images');
    }

    public function test_oversized_image_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'title' => 'Karya Besar',
            'status' => PublishStatus::Draft->value,
            'images' => [UploadedFile::fake()->image('big.jpg')->size(3000)],
        ])->assertSessionHasErrors('images.0');
    }

    public function test_pdf_upload_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'title' => 'Karya PDF',
            'status' => PublishStatus::Draft->value,
            'images' => [UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')],
        ])->assertSessionHasErrors('images.0');
    }

    public function test_invalid_demo_url_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'title' => 'Karya URL',
            'status' => PublishStatus::Draft->value,
            'demo_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('demo_url');
    }

    public function test_title_is_required(): void
    {
        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'status' => PublishStatus::Draft->value,
        ])->assertSessionHasErrors('title');
    }

    public function test_invalid_supervisor_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'title' => 'Karya X',
            'status' => PublishStatus::Draft->value,
            'supervisor_id' => 99999,
        ])->assertSessionHasErrors('supervisor_id');
    }

    // PUBLISHING
    public function test_published_at_set_when_first_published(): void
    {
        $this->actingAs($this->admin())->post(route('admin.karya-siswa.store'), [
            'title' => 'Karya Publish',
            'status' => PublishStatus::Published->value,
        ]);

        $sw = StudentWork::where('title', 'Karya Publish')->firstOrFail();
        $this->assertNotNull($sw->published_at);
    }

    public function test_republish_does_not_overwrite_published_at(): void
    {
        $admin = $this->admin();
        $sw = StudentWork::factory()->create([
            'status' => PublishStatus::Published->value,
            'published_at' => now()->subDays(5),
        ]);
        $original = $sw->published_at;

        $this->actingAs($admin)->put(route('admin.karya-siswa.update', $sw), [
            'title' => $sw->title,
            'status' => PublishStatus::Draft->value,
        ]);
        $this->actingAs($admin)->put(route('admin.karya-siswa.update', $sw->fresh()), [
            'title' => $sw->title,
            'status' => PublishStatus::Published->value,
        ]);

        $this->assertEquals($original->timestamp, $sw->fresh()->published_at->timestamp);
    }

    // UPDATE
    public function test_slug_stable_when_title_unchanged(): void
    {
        $admin = $this->admin();
        $sw = StudentWork::factory()->create(['title' => 'Judul Tetap', 'slug' => 'judul-tetap-custom']);

        $this->actingAs($admin)->put(route('admin.karya-siswa.update', $sw), [
            'title' => 'Judul Tetap',
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertEquals('judul-tetap-custom', $sw->fresh()->slug);
    }

    public function test_slug_regenerated_when_title_changes(): void
    {
        $admin = $this->admin();
        $sw = StudentWork::factory()->create(['title' => 'Judul Lama']);

        $this->actingAs($admin)->put(route('admin.karya-siswa.update', $sw), [
            'title' => 'Judul Baru Sekali',
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertEquals('judul-baru-sekali', $sw->fresh()->slug);
    }

    public function test_inactive_supervisor_remains_selected_on_edit(): void
    {
        $staff = StaffMember::factory()->create(['is_active' => false]);
        $sw = StudentWork::factory()->create(['supervisor_id' => $staff->id]);

        $response = $this->actingAs($this->admin())->get(route('admin.karya-siswa.edit', $sw));
        $response->assertOk();
        $response->assertSee($staff->name);
    }

    // DELETE
    public function test_destroy_soft_deletes(): void
    {
        $admin = $this->admin();
        $sw = StudentWork::factory()->create();

        $this->actingAs($admin)->delete(route('admin.karya-siswa.destroy', $sw));

        $this->assertSoftDeleted('student_works', ['id' => $sw->id]);
        $this->assertDatabaseMissing('student_works', ['id' => $sw->id, 'deleted_at' => null]);
    }

    // SECURITY
    public function test_created_by_cannot_be_injected(): void
    {
        $admin = $this->admin();
        $other = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.karya-siswa.store'), [
            'title' => 'Karya Injeksi',
            'status' => PublishStatus::Draft->value,
            'created_by' => $other->id,
        ]);

        $sw = StudentWork::where('title', 'Karya Injeksi')->firstOrFail();
        $this->assertEquals($admin->id, $sw->created_by);
    }

    public function test_remove_gallery_ids_scoped_to_own_student_work(): void
    {
        $admin = $this->admin();
        $swA = StudentWork::factory()->create();
        $swB = StudentWork::factory()->create();
        $mediaA = Media::factory()->create();
        $galleryB = Gallery::create([
            'media_id' => $mediaA->id,
            'galleryable_id' => $swB->id,
            'galleryable_type' => StudentWork::class,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)->put(route('admin.karya-siswa.update', $swA), [
            'title' => $swA->title,
            'status' => PublishStatus::Draft->value,
            'remove_gallery_ids' => [$galleryB->id],
        ]);

        $this->assertDatabaseHas('galleries', ['id' => $galleryB->id]);
    }

    public function test_deleting_one_gallery_does_not_delete_others(): void
    {
        $admin = $this->admin();
        $sw = StudentWork::factory()->create();
        $media1 = Media::factory()->create();
        $media2 = Media::factory()->create();
        $gallery1 = Gallery::create(['media_id' => $media1->id, 'galleryable_id' => $sw->id, 'galleryable_type' => StudentWork::class, 'sort_order' => 0]);
        $gallery2 = Gallery::create(['media_id' => $media2->id, 'galleryable_id' => $sw->id, 'galleryable_type' => StudentWork::class, 'sort_order' => 1]);

        $this->actingAs($admin)->put(route('admin.karya-siswa.update', $sw), [
            'title' => $sw->title,
            'status' => PublishStatus::Draft->value,
            'remove_gallery_ids' => [$gallery1->id],
        ]);

        $this->assertDatabaseMissing('galleries', ['id' => $gallery1->id]);
        $this->assertDatabaseHas('galleries', ['id' => $gallery2->id]);
    }
}