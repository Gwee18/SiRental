<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Daftar file lokal sementara yang dibuat selama test.
     *
     * @var array<int, string>
     */
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        /*
         * Hapus hanya file yang dibuat oleh test ini.
         * Konfigurasi dan instance disk "public" tidak pernah diubah,
         * sehingga tidak dapat memengaruhi kelas test berikutnya.
         */
        foreach (array_unique($this->temporaryFiles) as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }

        parent::tearDown();
    }

    public function test_customer_profile_page_can_be_rendered(): void
    {
        $customer = $this->createCustomer();

        $response = $this
            ->actingAs($customer, 'web')
            ->get(route('customer.profil'));

        $response->assertOk();
        $response->assertViewIs(
            'customer.profil.index'
        );
        $response->assertViewHas(
            'customer',
            fn (Customer $item) =>
                $item->is($customer)
        );
        $response->assertSee($customer->email);
    }

    public function test_customer_can_update_personal_information_without_changing_email(): void
    {
        $customer = $this->createCustomer([
            'foto_profil' =>
                'https://example.com/avatar.jpg',
        ]);

        $response = $this
            ->actingAs($customer, 'web')
            ->put(
                route('customer.profil.update'),
                [
                    'nama_lengkap' =>
                        'Customer Diperbarui',
                    'no_telp' => '081234567890',
                    'alamat' =>
                        'Jl. Pengujian No. 10',
                ]
            );

        $response
            ->assertRedirect(
                route('customer.profil')
            )
            ->assertSessionHas(
                'success',
                'Profil berhasil diperbarui.'
            );

        $customer->refresh();

        $this->assertSame(
            'Customer Diperbarui',
            $customer->nama_lengkap
        );
        $this->assertSame(
            '081234567890',
            $customer->no_telp
        );
        $this->assertSame(
            'Jl. Pengujian No. 10',
            $customer->alamat
        );
        $this->assertSame(
            'customer@example.com',
            $customer->email
        );
        $this->assertSame(
            'https://example.com/avatar.jpg',
            $customer->foto_profil
        );
    }

    public function test_customer_can_replace_local_profile_photo(): void
    {
        $oldPhoto =
            'foto-profil/testing-old-' .
            Str::uuid() .
            '.jpg';

        Storage::disk('public')->put(
            $oldPhoto,
            'old-image'
        );

        $this->rememberTemporaryFile($oldPhoto);

        $customer = $this->createCustomer([
            'foto_profil' => $oldPhoto,
        ]);

        $response = $this
            ->actingAs($customer, 'web')
            ->put(
                route('customer.profil.update'),
                [
                    'nama_lengkap' =>
                        $customer->nama_lengkap,
                    'no_telp' => $customer->no_telp,
                    'alamat' => $customer->alamat,
                    'foto_profil' =>
                        UploadedFile::fake()->image(
                            'foto-baru.jpg',
                            400,
                            400
                        ),
                ]
            );

        $response
            ->assertRedirect(
                route('customer.profil')
            )
            ->assertSessionHas('success');

        $customer->refresh();

        $this->assertNotSame(
            $oldPhoto,
            $customer->foto_profil
        );

        $this->rememberTemporaryFile(
            $customer->foto_profil
        );

        Storage::disk('public')->assertExists(
            $customer->foto_profil
        );

        Storage::disk('public')->assertMissing(
            $oldPhoto
        );
    }

    public function test_replacing_google_photo_does_not_treat_remote_url_as_local_file(): void
    {
        $customer = $this->createCustomer([
            'foto_profil' =>
                'https://example.com/google-avatar.jpg',
        ]);

        $response = $this
            ->actingAs($customer, 'web')
            ->put(
                route('customer.profil.update'),
                [
                    'nama_lengkap' =>
                        $customer->nama_lengkap,
                    'no_telp' => $customer->no_telp,
                    'alamat' => $customer->alamat,
                    'foto_profil' =>
                        UploadedFile::fake()->image(
                            'foto-lokal.jpg',
                            400,
                            400
                        ),
                ]
            );

        $response->assertSessionHas('success');

        $customer->refresh();

        $this->assertFalse(
            str_starts_with(
                $customer->foto_profil,
                'http'
            )
        );

        $this->rememberTemporaryFile(
            $customer->foto_profil
        );

        Storage::disk('public')->assertExists(
            $customer->foto_profil
        );
    }

    public function test_profile_name_is_required(): void
    {
        $customer = $this->createCustomer();

        $response = $this
            ->from(route('customer.profil'))
            ->actingAs($customer, 'web')
            ->put(
                route('customer.profil.update'),
                [
                    'nama_lengkap' => '',
                    'no_telp' => '081234567890',
                    'alamat' => 'Jl. Pengujian',
                ]
            );

        $response
            ->assertRedirect(
                route('customer.profil')
            )
            ->assertSessionHasErrors([
                'nama_lengkap',
            ]);

        $this->assertSame(
            'Customer Test',
            $customer->fresh()->nama_lengkap
        );
    }

    public function test_unsupported_profile_file_is_rejected(): void
    {
        $customer = $this->createCustomer();

        $response = $this
            ->from(route('customer.profil'))
            ->actingAs($customer, 'web')
            ->put(
                route('customer.profil.update'),
                [
                    'nama_lengkap' =>
                        $customer->nama_lengkap,
                    'no_telp' => $customer->no_telp,
                    'alamat' => $customer->alamat,
                    'foto_profil' =>
                        UploadedFile::fake()->create(
                            'dokumen.pdf',
                            100,
                            'application/pdf'
                        ),
                ]
            );

        $response
            ->assertRedirect(
                route('customer.profil')
            )
            ->assertSessionHasErrors([
                'foto_profil',
            ]);

        $this->assertNull(
            $customer->fresh()->foto_profil
        );
    }

    public function test_profile_photo_larger_than_two_megabytes_is_rejected(): void
    {
        $customer = $this->createCustomer();

        $response = $this
            ->from(route('customer.profil'))
            ->actingAs($customer, 'web')
            ->put(
                route('customer.profil.update'),
                [
                    'nama_lengkap' =>
                        $customer->nama_lengkap,
                    'no_telp' => $customer->no_telp,
                    'alamat' => $customer->alamat,
                    'foto_profil' =>
                        UploadedFile::fake()
                            ->image('foto-besar.jpg')
                            ->size(2049),
                ]
            );

        $response
            ->assertRedirect(
                route('customer.profil')
            )
            ->assertSessionHasErrors([
                'foto_profil',
            ]);

        $this->assertNull(
            $customer->fresh()->foto_profil
        );
    }

    private function rememberTemporaryFile(
        ?string $path
    ): void {
        if (
            $path &&
            !Str::startsWith(
                $path,
                ['http://', 'https://']
            )
        ) {
            $this->temporaryFiles[] = $path;
        }
    }

    private function createCustomer(
        array $overrides = []
    ): Customer {
        return Customer::create(array_merge([
            'nama_lengkap' => 'Customer Test',
            'email' => 'customer@example.com',
            'email_verified_at' => now(),
            'password' => null,
            'no_telp' => '081111111111',
            'alamat' => 'Jl. Lama No. 1',
            'foto_profil' => null,
        ], $overrides));
    }
}