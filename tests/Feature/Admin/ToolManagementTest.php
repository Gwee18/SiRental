<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Alat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ToolManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_tool_without_photo(): void
    {
        $admin = $this->createAdmin();

        $response = $this
            ->actingAs($admin, 'admin')
            ->post(
                route('admin.alat.store'),
                $this->validToolData([
                    'stok_total' => 7,
                ])
            );

        $response
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('alat', [
            'nama_alat' => 'Tenda Camping',
            'kategori' => 'Tenda',
            'stok_total' => 7,
            'stok_tersedia' => 7,
            'harga_per_hari' => 50000,
            'kondisi' => 'baik',
            'is_active' => true,
        ]);
    }

    public function test_updating_total_stock_preserves_units_currently_rented(): void
    {
        $admin = $this->createAdmin();

        /*
         * Total 5 dan tersedia 3 berarti 2 unit sedang disewa.
         */
        $alat = $this->createTool([
            'stok_total' => 5,
            'stok_tersedia' => 3,
        ]);

        $response = $this
            ->actingAs($admin, 'admin')
            ->put(
                route('admin.alat.update', $alat->id),
                $this->validToolData([
                    'nama_alat' => 'Tenda Camping Premium',
                    'stok_total' => 8,
                ])
            );

        $response
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('success');

        $alat->refresh();

        $this->assertSame(
            'Tenda Camping Premium',
            $alat->nama_alat
        );
        $this->assertSame(8, $alat->stok_total);

        /*
         * Dua unit tetap dianggap sedang disewa:
         * 8 total - 2 disewa = 6 tersedia.
         */
        $this->assertSame(6, $alat->stok_tersedia);
    }

    public function test_total_stock_cannot_be_lower_than_units_currently_rented(): void
    {
        $admin = $this->createAdmin();

        /*
         * Total 5 dan tersedia 2 berarti 3 unit sedang disewa.
         */
        $alat = $this->createTool([
            'stok_total' => 5,
            'stok_tersedia' => 2,
        ]);

        $response = $this
            ->from(route('admin.alat.edit', $alat->id))
            ->actingAs($admin, 'admin')
            ->put(
                route('admin.alat.update', $alat->id),
                $this->validToolData([
                    'stok_total' => 2,
                ])
            );

        $response
            ->assertRedirect(
                route('admin.alat.edit', $alat->id)
            )
            ->assertSessionHasErrors([
                'stok_total' => 'Stok total tidak boleh kurang dari 3 unit karena jumlah tersebut sedang disewa.',
            ]);

        $alat->refresh();

        $this->assertSame(5, $alat->stok_total);
        $this->assertSame(2, $alat->stok_tersedia);
    }

    public function test_admin_can_deactivate_and_reactivate_tool(): void
    {
        $admin = $this->createAdmin();
        $alat = $this->createTool();

        $this
            ->actingAs($admin, 'admin')
            ->patch(
                route(
                    'admin.alat.toggle-status',
                    $alat->id
                )
            )
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('success');

        $this->assertFalse(
            $alat->fresh()->is_active
        );

        $this
            ->actingAs($admin, 'admin')
            ->patch(
                route(
                    'admin.alat.toggle-status',
                    $alat->id
                )
            )
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('success');

        $this->assertTrue(
            $alat->fresh()->is_active
        );
    }

    public function test_unsupported_tool_photo_is_rejected(): void
    {
        $admin = $this->createAdmin();

        $response = $this
            ->from(route('admin.alat.create'))
            ->actingAs($admin, 'admin')
            ->post(
                route('admin.alat.store'),
                $this->validToolData([
                    'foto_alat' => UploadedFile::fake()->create(
                        'dokumen.pdf',
                        100,
                        'application/pdf'
                    ),
                ])
            );

        $response
            ->assertRedirect(route('admin.alat.create'))
            ->assertSessionHasErrors([
                'foto_alat',
            ]);

        $this->assertDatabaseCount('alat', 0);
    }

    private function validToolData(
        array $overrides = []
    ): array {
        return array_merge([
            'nama_alat' => 'Tenda Camping',
            'kategori' => 'Tenda',
            'stok_total' => 5,
            'harga_per_hari' => 50000,
            'kondisi' => 'baik',
            'deskripsi' => 'Alat untuk pengujian.',
        ], $overrides);
    }

    private function createAdmin(): Admin
    {
        $admin = new Admin;

        $admin->forceFill([
            'nama' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $admin->save();

        return $admin;
    }

    private function createTool(
        array $overrides = []
    ): Alat {
        return Alat::create(array_merge([
            'nama_alat' => 'Tenda Camping',
            'kategori' => 'Tenda',
            'stok_total' => 5,
            'stok_tersedia' => 5,
            'harga_per_hari' => 50000,
            'kondisi' => 'baik',
            'deskripsi' => 'Alat untuk pengujian.',
            'foto_alat' => null,
            'is_active' => true,
        ], $overrides));
    }
}
