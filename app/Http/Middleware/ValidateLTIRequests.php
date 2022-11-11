<?php

namespace App\Http\Middleware;
use \IMSGlobal\LTI;

use Closure;

class ValidateLTIRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		try {
			LTI\LTI_Message_Launch::new(new \App\Database())->validate();
		} catch (\Exception $e) {
			abort(403, 'Access denied');
		}

	    return $next($request);
    }
}
