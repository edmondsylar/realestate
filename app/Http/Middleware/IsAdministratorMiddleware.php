<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use Redirect;

class IsAdministratorMiddleware
{
   public function handle($request, Closure $next)
    {
        $user = Sentinel::getUser();
        if ($user->inRole('administrator')) {
            return $next($request);
        } else {
            abort('405');
        }
    }
}
