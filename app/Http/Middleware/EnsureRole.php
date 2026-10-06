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

        abort_unless($user && in_array($user->role->value, $roles, true), 403);

        if (
            $user->role->value === 'merchant'
            && (! $user->merchant || $user->merchant->verification_status->value !== 'approved')
        ) {
            return redirect('/')->with(
                'status',
                'Akun mitra kamu masih menunggu persetujuan admin.'
            );
        }

        return $next($request);
    }
}
