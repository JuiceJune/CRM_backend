<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMailboxAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->isAdmin()) {
            return $next($request); // Адмін має доступ до всіх пошт
        }

        $mailbox = $request->route('mailbox');

        // Перевірте, чи користувач має доступ до цієї поштової скриньки
        if (!$mailbox || !$request->user()->projects()->whereHas('mailboxes', function ($query) use ($mailbox) {
                $query->where('mailbox_id', $mailbox->id);
            })->exists()) {
            return response()->json(['message' => 'Access Denied'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
