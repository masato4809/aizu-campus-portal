<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Campus\CurrentStudent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CampusAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app(CurrentStudent::class)->get($request)) {
            return redirect('/campus/login');
        }

        return $next($request);
    }
}
