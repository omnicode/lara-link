<?php
namespace LaraLink\Middleware;

use Closure;
use Illuminate\Http\Request;

class LaraLinkMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        $isValidate = true;

        if ($request->method() == 'GET') {
            $isValidate = false;
        }

        if ($isValidate) {
            $formProtection = new LinkProtection();
            $validate = $formProtection->validate($request->all());
            if($validate === false) {
                return redirect()->back();
            }
        }

        return $next($request);
    }
}
