<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function makeMerchant(
        VerificationStatus $status,
        bool $active = true
    ): User {
        $user = User::create([
            'name' => 'Mitra Uji',
            'email' => uniqid('merchant') . '@test.test',
            'password' => 'password',
        ]);

        $user->forceFill(['role' => UserRole::Merchant])->save();

        $merchant = new Merchant([
            'business_name' => 'Mitra Uji',
            'phone' => '0812000000',
            'address' => 'Jl. Uji No. 1',
            'area' => 'Mendalo',
            'verification_status' => $status,
            'is_active' => $active,
        ]);

        $merchant->user_id = $user->id;
        $merchant->save();

        return $user;
    }

    public function test_approved_active_merchant_can_access_dashboard(): void
    {
        $user = $this->makeMerchant(VerificationStatus::Approved);

        $this->actingAs($user)
            ->get('/mitra')
            ->assertOk();
    }

    public function test_pending_merchant_is_redirected_from_dashboard(): void
    {
        $user = $this->makeMerchant(VerificationStatus::Pending);

        $this->actingAs($user)
            ->get('/mitra')
            ->assertRedirect('/')
            ->assertSessionHas(
                'status',
                'Akun mitra kamu masih menunggu persetujuan admin.'
            );
    }

    public function test_rejected_merchant_is_redirected_from_dashboard(): void
    {
        $user = $this->makeMerchant(VerificationStatus::Rejected);

        $this->actingAs($user)
            ->get('/mitra')
            ->assertRedirect('/')
            ->assertSessionHas('status', 'Pengajuan mitra kamu ditolak.');
    }

    public function test_suspended_merchant_is_redirected_from_dashboard(): void
    {
        $user = $this->makeMerchant(VerificationStatus::Suspended);

        $this->actingAs($user)
            ->get('/mitra')
            ->assertRedirect('/')
            ->assertSessionHas('status', 'Akun mitra kamu sedang ditangguhkan.');
    }

    public function test_inactive_approved_merchant_is_redirected_from_dashboard(): void
    {
        $user = $this->makeMerchant(
            VerificationStatus::Approved,
            active: false
        );

        $this->actingAs($user)
            ->get('/mitra')
            ->assertRedirect('/')
            ->assertSessionHas(
                'status',
                'Akun mitra kamu belum dapat digunakan untuk berjualan.'
            );
    }
}
