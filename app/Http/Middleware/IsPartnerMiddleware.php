<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use Redirect;

class IsPartnerMiddleware
{
   public function handle($request, Closure $next)
    {
        $user = Sentinel::getUser();
        if ($user->inRole('partner')) {
            return $next($request);
        } else {
            abort('405');
        }
    }
}
