<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuthenticate
{
    public function handle($request, Closure $next)
    {
        if (! auth()->check()) {
            abort(401, 'Unauthenticated. Not logged in!');
        }
        if (auth()->user()->is_admin == true) {
            return $next($request);
        } else {
            abort(401, "Unauthorized. You aren't an admin!");

        }

    }
}
