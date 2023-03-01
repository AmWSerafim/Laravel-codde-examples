<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowIpAddressAccessMiddleware
{
    // Allowed IP addresses
    public $restrictedIp = ['192.168.10.1', '95.42.119.42', '188.163.14.166'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!in_array($request->ip(), $this->restrictedIp)) {
            if(preg_match('/(^198\.54\.106\.[0-9]{1,3})|(^204\.96\.189\.[0-9]{1,3})|(^75\.15\.140\.[0-9]{1,3})/', $request->ip())){
                return $next($request);
            }
            return response('You are not allowed to access this site.', 200)->header('Content-Type', 'text/plain');
        }
        return $next($request);
    }
}
