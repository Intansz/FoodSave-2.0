<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Pemakaian: ->middleware('role:merchant') atau 'role:consumer,admin'.
     */

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless(
            $user && in_array($user->role->value, $roles, true),
            403
        );

        if ($user->role->value === 'merchant') {
            $merchant = $user->merchant;

            abort_unless($merchant, 404);

            if (! $merchant->canSell()) {
                $message = match ($merchant->verification_status->value) {
                    'pending' => 'Akun mitra kamu masih menunggu persetujuan admin.',
                    'rejected' => 'Pengajuan mitra kamu ditolak.',
                    'suspended' => 'Akun mitra kamu sedang ditangguhkan.',
                    default => 'Akun mitra kamu belum dapat digunakan untuk berjualan.',
                };

                return redirect('/')->with('status', $message);
            }
        }

        return $next($request);
    }
}
