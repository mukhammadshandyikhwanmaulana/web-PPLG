<?php

namespace Tests\Feature;

use App\Enums\PublishStatus;
use App\Enums\UserRole;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat role Spatie untuk pengujian
        Role::findOrCreate(UserRole::Admin->value);
        Role::findOrCreate(UserRole::Guru->value);

        // Palsukan disk storage public
        Storage::fake('public');
    }

    /**
     * Test Guru berhasil membuat kegiatan dan status otomatis dipaksa menjadi Draft.
     */
    public function test_guru_bisa_membuat_kegiatan_dan_status_terkunci_ke_draft(): void
    {
        $guru = User::factory()->guru()->create();
        $guru->assignRole(UserRole::Guru->value);

        $payload = [
            'title'      => 'Kegiatan Ekstrakurikuler Robotik',
            'event_date' => now()->format('Y-m-d'),
            'content'    => 'Deskripsi kegiatan ekstrakurikuler robotik mingguan.',
            'status'     => PublishStatus::Published->value, // Mencoba kirim Published
            'cover'      => UploadedFile::fake()->image('cover.jpg', 800, 600),
        ];

        $response = $this->actingAs($guru)
            ->post(route('guru.kegiatan.store'), $payload);

        $response->assertRedirect(route('guru.kegiatan.index'));
        $response->assertSessionHas('success');

        // Pastikan data tersimpan di database
        $this->assertDatabaseHas('activities', [
            'title'      => 'Kegiatan Ekstrakurikuler Robotik',
            'created_by' => $guru->id,
            'status'     => PublishStatus::Draft->value, // Dipaksa jadi Draft
        ]);

        $activity = Activity::where('title', 'Kegiatan Ekstrakurikuler Robotik')->first();
        $this->assertNull($activity->published_at);
    }

    /**
     * Test Admin berhasil membuat kegiatan dan bisa langsung mempublikasikannya (Published).
     */
    public function test_admin_bisa_membuat_kegiatan_dan_langsung_publish(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole(UserRole::Admin->value);

        $payload = [
            'title'      => 'Pameran Karya Karya Siswa PPLG',
            'event_date' => now()->format('Y-m-d'),
            'content'    => 'Deskripsi pameran karya teknologi siswa.',
            'status'     => PublishStatus::Published->value,
            'cover'      => UploadedFile::fake()->image('pameran.png', 800, 600),
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.kegiatan.store'), $payload);

        $response->assertRedirect();

        // Pastikan data tersimpan dengan status Published
        $this->assertDatabaseHas('activities', [
            'title'  => 'Pameran Karya Karya Siswa PPLG',
            'status' => PublishStatus::Published->value,
        ]);

        $activity = Activity::where('title', 'Pameran Karya Karya Siswa PPLG')->first();
        $this->assertNotNull($activity->published_at);
    }

    /**
     * Test validasi upload menolak file dengan ekstensi selain gambar (misal SVG / EXE).
     */
    public function test_membuat_kegiatan_gagal_jika_format_cover_tidak_valid(): void
    {
        $guru = User::factory()->guru()->create();
        $guru->assignRole(UserRole::Guru->value);

        $payload = [
            'title'      => 'Uji Coba Upload Ekstensi Terlarang',
            'event_date' => now()->format('Y-m-d'),
            'status'     => PublishStatus::Draft->value,
            'cover'      => UploadedFile::fake()->create('malicious.svg', 100, 'image/svg+xml'),
        ];

        $response = $this->actingAs($guru)
            ->post(route('guru.kegiatan.store'), $payload);

        $response->assertSessionHasErrors(['cover']);
    }

    /**
     * Test Guru tidak bisa mengedit atau mengubah kegiatan milik Guru lain (IDOR Protection).
     */
    public function test_guru_tidak_bisa_mengubah_kegiatan_milik_guru_lain(): void
    {
        $guru1 = User::factory()->guru()->create();
        $guru1->assignRole(UserRole::Guru->value);

        $guru2 = User::factory()->guru()->create();
        $guru2->assignRole(UserRole::Guru->value);

        // Guru 1 membuat kegiatan
        $activity = Activity::create([
            'user_id'      => $guru1->id,
            'title'        => 'Kegiatan Guru Pertama',
            'slug'         => 'kegiatan-guru-pertama',
            'event_date'   => now()->format('Y-m-d'),
            'status'       => PublishStatus::Draft,
            'created_by'   => $guru1->id,
            'updated_by'   => $guru1->id,
        ]);

        // Guru 2 mencoba mengakses halaman edit kegiatan milik Guru 1
        $response = $this->actingAs($guru2)
            ->get(route('guru.kegiatan.edit', $activity));

        $response->assertStatus(403);
    }
}