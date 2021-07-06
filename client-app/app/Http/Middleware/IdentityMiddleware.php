<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IdentityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = Http::withHeaders(['Authorization' => $request->header('Authorization')])->get(config('services.identity.verify_uri'));

        if ($response->failed())
            abort(500, 'Internal server error');

        if ($response->status() === 401)
            abort(401, 'unauthenticated');

        $isValid = $response->json();

        if (is_null($isValid) || !$isValid['valid'])
            abort(401, 'unauthenticated');

        return $next($request);
    }
}
