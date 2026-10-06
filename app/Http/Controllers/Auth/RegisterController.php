<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function createConsumer(): View
    {
        return view('auth.register');
    }

    public function storeConsumer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create($data); // role default = consumer

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('status', 'Akun berhasil dibuat. Selamat datang di FoodSave!');
    }

    public function createMerchant(): View
    {
        return view('auth.register-merchant');
    }

    public function storeMerchant(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'business_name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:500'],
            'area' => ['required', Rule::in(config('foodsave.areas'))],
            'maps_url' => ['nullable', 'url:https', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
            ]);
            $user->forceFill(['role' => UserRole::Merchant])->save();

            $merchant = new Merchant([
                'business_name' => $data['business_name'],
                'description' => $data['description'] ?? null,
                'phone' => $data['phone'],
                'address' => $data['address'],
                'area' => $data['area'],
                'maps_url' => $data['maps_url'] ?? null,
            ]);
            $merchant->user_id = $user->id;
            $merchant->verification_status = VerificationStatus::Pending;
            $merchant->save();

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('status', 'Pengajuan mitra diterima. Status: Menunggu Verifikasi admin. Anda baru bisa berjualan setelah disetujui.');
    }
}
