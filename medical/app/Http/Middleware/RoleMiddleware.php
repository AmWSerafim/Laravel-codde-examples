<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next, $role, $permission = null){

        $role = explode("|", $role);

        if(!empty($permission)){
            $permission = explode("|", $permission);
        }

        //dump($role);
        if(is_array($role)){

            $allow_access = false;
            foreach ($role as $item) {
                if ($request->user()->hasRole($item)) {
                    $allow_access = true;
                    break;
                }
            }
            if(!$allow_access){
                abort(404);
            }
        } else {
            if(!$request->user()->hasRole($role)) {
                abort(404);
            }
        }

        //dump($permission);
        if($permission !== null && is_array($permission)){

            $allow_access = false;
            foreach ($permission as $item) {
                if ($request->user()->can($item)) {
                    $allow_access = true;
                    break;
                }
            }
            if(!$allow_access){
                abort(404);
            }
        } else {
            if(!$request->user()->can($permission)) {

                abort(404);
            }
        }

        return $next($request);
    }

}
