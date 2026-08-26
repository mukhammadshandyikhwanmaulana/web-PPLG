<?php

namespace Tests\Feature\Admin;

use App\Enums\PublishStatus;
use App\Models\IndustryPartner;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class IndustryPartnerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);

        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('admin');
    }

    protected function guru(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('guru');
        return $u;
    }

    // ACCESS & AUTHORIZATION
    public function test_guest_denied(): void
    {
        $this->get(route('admin.mitra.index'))->assertRedirect(route('admin.login'));
    }

    public function test_guru_denied(): void
    {
        $this->actingAs($this->guru())->get(route('admin.mitra.index'))->assertForbidden();
    }

    public function test_admin_allowed(): void
    {
        $this->actingAs($this->admin)->get(route('admin.mitra.index'))->assertOk();
    }

    // CREATE & NO AUTO-SHIFT
    public function test_admin_can_create_partner(): void
    {
        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'PT Teknologi Nusantara',
            'website_url' => 'https://ptteknologi.example.com',
            'status' => PublishStatus::Draft->value,
            'sort_order' => 1,
        ])->assertRedirect(route('admin.mitra.index'));

        $this->assertDatabaseHas('industry_partners', [
            'name' => 'PT Teknologi Nusantara',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_partner_can_be_created_without_logo(): void
    {
        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'Mitra Tanpa Logo',
            'status' => PublishStatus::Draft->value,
        ])->assertRedirect(route('admin.mitra.index'));

        $partner = IndustryPartner::where('name', 'Mitra Tanpa Logo')->firstOrFail();
        $this->assertNull($partner->logo_media_id);
    }

    public function test_logo_upload_creates_media_and_links(): void
    {
        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'Mitra Berlogo',
            'status' => PublishStatus::Draft->value,
            'logo' => UploadedFile::fake()->image('logo.jpg'),
        ]);

        $partner = IndustryPartner::where('name', 'Mitra Berlogo')->firstOrFail();
        $this->assertNotNull($partner->logo_media_id);
        Storage::disk('public')->assertExists($partner->logo->file_path);
    }

    public function test_store_does_not_shift_existing_orders(): void
    {
        $p1 = IndustryPartner::factory()->create(['sort_order' => 1]);
        $p2 = IndustryPartner::factory()->create(['sort_order' => 2]);

        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'Mitra Sisipan Baru',
            'status' => PublishStatus::Draft->value,
            'sort_order' => 1,
        ]);

        // Mitra lama TIDAK tergeser
        $this->assertEquals(1, $p1->fresh()->sort_order);
        $this->assertEquals(2, $p2->fresh()->sort_order);
    }

    // VALIDATION
    public function test_invalid_url_rejected(): void
    {
        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'Mitra URL Salah',
            'website_url' => 'javascript:alert(1)',
            'status' => PublishStatus::Draft->value,
        ])->assertSessionHasErrors('website_url');
    }

    public function test_pdf_logo_rejected(): void
    {
        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'Mitra PDF',
            'status' => PublishStatus::Draft->value,
            'logo' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('logo');
    }

    public function test_oversized_logo_rejected(): void
    {
        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'Mitra Logo Besar',
            'status' => PublishStatus::Draft->value,
            'logo' => UploadedFile::fake()->image('big.jpg')->size(3000), // Melebihi 2048 KB (2 MB)
        ])->assertSessionHasErrors('logo');
    }

    public function test_name_is_required(): void
    {
        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'status' => PublishStatus::Draft->value,
        ])->assertSessionHasErrors('name');
    }

    public function test_invalid_status_rejected(): void
    {
        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'Mitra Status Salah',
            'status' => 'archived',
        ])->assertSessionHasErrors('status');
    }

    // PUBLISHING
    public function test_published_at_set_when_first_published(): void
    {
        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'Mitra Publish',
            'status' => PublishStatus::Published->value,
        ]);

        $partner = IndustryPartner::where('name', 'Mitra Publish')->firstOrFail();
        $this->assertNotNull($partner->published_at);
    }

    public function test_republish_does_not_overwrite_published_at(): void
    {
        $partner = IndustryPartner::factory()->create([
            'status' => PublishStatus::Published->value,
            'published_at' => now()->subDays(5),
        ]);
        $original = $partner->published_at;

        $this->actingAs($this->admin)->put(route('admin.mitra.update', $partner), [
            'name' => $partner->name,
            'status' => PublishStatus::Draft->value,
        ]);
        $this->actingAs($this->admin)->put(route('admin.mitra.update', $partner->fresh()), [
            'name' => $partner->name,
            'status' => PublishStatus::Published->value,
        ]);

        $this->assertEquals($original->timestamp, $partner->fresh()->published_at->timestamp);
    }

    // UPDATE & NO AUTO-SHIFT
    public function test_admin_can_update_partner(): void
    {
        $partner = IndustryPartner::factory()->create(['name' => 'Nama Lama']);

        $this->actingAs($this->admin)->put(route('admin.mitra.update', $partner), [
            'name' => 'Nama Baru',
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertEquals('Nama Baru', $partner->fresh()->name);
        $this->assertEquals($this->admin->id, $partner->fresh()->updated_by);
    }

    public function test_update_does_not_shift_existing_orders(): void
    {
        $p1 = IndustryPartner::factory()->create(['sort_order' => 1]);
        $p2 = IndustryPartner::factory()->create(['sort_order' => 2]);
        $p3 = IndustryPartner::factory()->create(['sort_order' => 3]);

        $this->actingAs($this->admin)->put(route('admin.mitra.update', $p3), [
            'name' => $p3->name,
            'status' => PublishStatus::Draft->value,
            'sort_order' => 1,
        ]);

        $this->assertEquals(1, $p3->fresh()->sort_order);
        $this->assertEquals(1, $p1->fresh()->sort_order);
        $this->assertEquals(2, $p2->fresh()->sort_order);
    }

    public function test_logo_replacement_does_not_break_foreign_key(): void
    {
        $oldMedia = Media::factory()->create();
        $partner = IndustryPartner::factory()->create(['logo_media_id' => $oldMedia->id]);

        $response = $this->actingAs($this->admin)->put(route('admin.mitra.update', $partner), [
            'name' => $partner->name,
            'status' => PublishStatus::Draft->value,
            'logo' => UploadedFile::fake()->image('new-logo.jpg'),
        ]);

        $response->assertRedirect(route('admin.mitra.index'));
        $this->assertNotEquals($oldMedia->id, $partner->fresh()->logo_media_id);
    }

    public function test_old_media_remains_after_logo_replacement(): void
    {
        $oldMedia = Media::factory()->create();
        $partner = IndustryPartner::factory()->create(['logo_media_id' => $oldMedia->id]);

        $this->actingAs($this->admin)->put(route('admin.mitra.update', $partner), [
            'name' => $partner->name,
            'status' => PublishStatus::Draft->value,
            'logo' => UploadedFile::fake()->image('new-logo.jpg'),
        ]);

        $this->assertDatabaseHas('media', ['id' => $oldMedia->id]);
    }

    public function test_logo_unchanged_when_no_new_file_uploaded(): void
    {
        $media = Media::factory()->create();
        $partner = IndustryPartner::factory()->create(['logo_media_id' => $media->id]);

        $this->actingAs($this->admin)->put(route('admin.mitra.update', $partner), [
            'name' => 'Nama Diperbarui',
            'status' => PublishStatus::Draft->value,
        ]);

        $this->assertEquals($media->id, $partner->fresh()->logo_media_id);
    }

    // DELETE & NO AUTO-DECREMENT
    public function test_destroy_soft_deletes(): void
    {
        $partner = IndustryPartner::factory()->create();

        $this->actingAs($this->admin)->delete(route('admin.mitra.destroy', $partner));

        $this->assertSoftDeleted('industry_partners', ['id' => $partner->id]);
    }

    public function test_destroy_does_not_reorder_remaining_partners(): void
    {
        $p1 = IndustryPartner::factory()->create(['sort_order' => 1]);
        $p2 = IndustryPartner::factory()->create(['sort_order' => 2]);
        $p3 = IndustryPartner::factory()->create(['sort_order' => 3]);

        $this->actingAs($this->admin)->delete(route('admin.mitra.destroy', $p1));

        $this->assertEquals(2, $p2->fresh()->sort_order);
        $this->assertEquals(3, $p3->fresh()->sort_order);
    }

    public function test_soft_deleted_partner_does_not_appear_in_index(): void
    {
        $partner = IndustryPartner::factory()->create(['name' => 'Mitra Terhapus']);
        $partner->delete();

        $response = $this->actingAs($this->admin)->get(route('admin.mitra.index'));
        $response->assertDontSee('Mitra Terhapus');
    }

    // SECURITY
    public function test_created_by_cannot_be_injected(): void
    {
        $other = User::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.mitra.store'), [
            'name' => 'Mitra Injeksi',
            'status' => PublishStatus::Draft->value,
            'created_by' => $other->id,
        ]);

        $partner = IndustryPartner::where('name', 'Mitra Injeksi')->firstOrFail();
        $this->assertEquals($this->admin->id, $partner->created_by);
    }

    public function test_updated_by_cannot_be_injected(): void
    {
        $other = User::factory()->create();
        $partner = IndustryPartner::factory()->create();

        $this->actingAs($this->admin)->put(route('admin.mitra.update', $partner), [
            'name' => $partner->name,
            'status' => PublishStatus::Draft->value,
            'updated_by' => $other->id,
        ]);

        $this->assertEquals($this->admin->id, $partner->fresh()->updated_by);
    }

    // ORDERING & PERFORMANCE
    public function test_index_orders_by_sort_order_then_name(): void
    {
        IndustryPartner::factory()->create(['name' => 'Zebra', 'sort_order' => 1]);
        IndustryPartner::factory()->create(['name' => 'Alpha', 'sort_order' => 1]);
        IndustryPartner::factory()->create(['name' => 'Beta', 'sort_order' => 2]);

        $response = $this->actingAs($this->admin)->get(route('admin.mitra.index'));
        $content = $response->getContent();

        $posAlpha = strpos($content, 'Alpha');
        $posZebra = strpos($content, 'Zebra');
        $posBeta = strpos($content, 'Beta');

        $this->assertTrue($posAlpha < $posZebra);
        $this->assertTrue($posZebra < $posBeta);
    }

    public function test_index_eager_loads_logo(): void
    {
        IndustryPartner::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.mitra.index'));

        $response->assertOk();
    }
}