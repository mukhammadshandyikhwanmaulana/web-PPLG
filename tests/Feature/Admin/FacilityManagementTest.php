<?php

namespace Tests\Feature\Admin;

use App\Models\Facility;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FacilityManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    // ACCESS & AUTHORIZATION

    public function test_guest_cannot_access_facility_management(): void
    {
        $this->get(route('admin.fasilitas.index'))->assertRedirect(route('admin.login'));
    }

    public function test_guru_cannot_access_facility_management(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($guru)->get(route('admin.fasilitas.index'))->assertForbidden();
    }

    public function test_admin_can_view_facility_list(): void
    {
        $this->actingAs($this->admin)->get(route('admin.fasilitas.index'))->assertOk();
    }

    // SEARCH & FILTERING

    public function test_admin_can_filter_facilities_by_search_keyword(): void
    {
        Facility::factory()->create(['name' => 'Laboratorium Komputer']);
        Facility::factory()->create(['name' => 'Lapangan Basket']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.fasilitas.index', ['search' => 'Komputer']));

        $response->assertOk();
        $response->assertSee('Laboratorium Komputer');
        $response->assertDontSee('Lapangan Basket');
    }

    // CREATE

    public function test_admin_can_create_facility(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fasilitas.store'), [
            'name' => 'Lab Komputer',
            'description' => 'Lab pemrograman dengan 30 unit PC.',
            'sort_order' => '1',
        ]);

        $response->assertRedirect(route('admin.fasilitas.index'));

        $facility = Facility::where('name', 'Lab Komputer')->first();
        $this->assertNotNull($facility);
        $this->assertEquals($this->admin->id, $facility->created_by);
        $this->assertNull($facility->photo_media_id);
    }

    public function test_facility_can_be_created_without_photo(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fasilitas.store'), [
            'name' => 'Lab Jaringan',
        ]);

        $response->assertRedirect(route('admin.fasilitas.index'));
        $this->assertDatabaseHas('facilities', ['name' => 'Lab Jaringan']);
    }

    public function test_created_by_cannot_be_manipulated_via_request(): void
    {
        $otherAdmin = User::factory()->create();
        $otherAdmin->assignRole('admin');

        $this->actingAs($this->admin)->post(route('admin.fasilitas.store'), [
            'name' => 'Lab Nakal',
            'created_by' => $otherAdmin->id,
        ]);

        $facility = Facility::where('name', 'Lab Nakal')->first();
        $this->assertEquals($this->admin->id, $facility->created_by);
    }

    // UPDATE

    public function test_admin_can_update_facility(): void
    {
        $facility = Facility::factory()->create(['name' => 'Nama Lama']);

        $response = $this->actingAs($this->admin)->put(route('admin.fasilitas.update', $facility), [
            'name' => 'Nama Baru',
            'sort_order' => '2',
        ]);

        $response->assertRedirect(route('admin.fasilitas.index'));
        $this->assertEquals('Nama Baru', $facility->fresh()->name);
        $this->assertEquals($this->admin->id, $facility->fresh()->updated_by);
    }

    // DELETE (SOFT)

    public function test_destroy_soft_deletes_facility(): void
    {
        $facility = Facility::factory()->create();

        $this->actingAs($this->admin)->delete(route('admin.fasilitas.destroy', $facility));

        $this->assertSoftDeleted('facilities', ['id' => $facility->id]);
    }

    public function test_soft_deleted_facility_does_not_appear_in_index(): void
    {
        $facility = Facility::factory()->create(['name' => 'Fasilitas Terhapus']);
        $facility->delete();

        $response = $this->actingAs($this->admin)->get(route('admin.fasilitas.index'));

        $response->assertDontSee('Fasilitas Terhapus');
    }

    // VALIDATION

    public function test_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fasilitas.store'), [
            'description' => 'Tanpa nama',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_invalid_image_type_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fasilitas.store'), [
            'name' => 'Lab Tes',
            'photo' => UploadedFile::fake()->create('dokumen.pdf', 100),
        ]);

        $response->assertSessionHasErrors('photo');
    }

    public function test_oversized_image_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fasilitas.store'), [
            'name' => 'Lab Tes',
            'photo' => UploadedFile::fake()->image('foto.jpg')->size(3000),
        ]);

        $response->assertSessionHasErrors('photo');
    }

    public function test_negative_sort_order_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fasilitas.store'), [
            'name' => 'Lab Tes',
            'sort_order' => '-1',
        ]);

        $response->assertSessionHasErrors('sort_order');
    }

    // MEDIA & STORAGE CLEANUP

    public function test_photo_upload_creates_media_and_links_to_facility(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post(route('admin.fasilitas.store'), [
            'name' => 'Lab Berfoto',
            'photo' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $facility = Facility::where('name', 'Lab Berfoto')->first();

        $this->assertNotNull($facility->photo_media_id);
        Storage::disk('public')->assertExists($facility->photo->file_path);
    }

    public function test_photo_replacement_deletes_old_media_and_file(): void
    {
        Storage::fake('public');

        // Buat media awal
        $oldFile = UploadedFile::fake()->image('lama.jpg');
        $oldPath = $oldFile->store('facilities', 'public');
        $oldMedia = Media::create([
            'file_name' => basename($oldPath),
            'file_path' => $oldPath,
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'uploaded_by' => $this->admin->id,
        ]);

        $facility = Facility::factory()->create([
            'photo_media_id' => $oldMedia->id,
        ]);

        // Upload foto pengganti saat update
        $this->actingAs($this->admin)->put(route('admin.fasilitas.update', $facility), [
            'name' => $facility->name,
            'photo' => UploadedFile::fake()->image('baru.jpg'),
        ]);

        $facility->refresh();

        // Pastikan media baru berhasil dikaitkan
        $this->assertNotNull($facility->photo_media_id);
        $this->assertNotEquals($oldMedia->id, $facility->photo_media_id);
        Storage::disk('public')->assertExists($facility->photo->file_path);

        // Pastikan file dan record database media lama telah terhapus
        Storage::disk('public')->assertMissing($oldPath);
        $this->assertDatabaseMissing('media', ['id' => $oldMedia->id]);
    }

    // ORDERING & DETERMINISM

    public function test_facility_index_ordering_is_deterministic(): void
    {
        Facility::factory()->create(['name' => 'B Lab', 'sort_order' => 1]);
        Facility::factory()->create(['name' => 'A Lab', 'sort_order' => 1]);

        $response = $this->actingAs($this->admin)->get(route('admin.fasilitas.index'));

        $content = $response->getContent();
        $posA = strpos($content, 'A Lab');
        $posB = strpos($content, 'B Lab');

        $this->assertLessThan($posB, $posA);
    }
}