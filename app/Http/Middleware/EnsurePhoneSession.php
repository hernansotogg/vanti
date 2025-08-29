<?php

namespace App\Http\Middleware;

use App\Models\Cliente;
use Closure;
use Illuminate\Http\Request;

class EnsurePhoneSession
{
    public function handle(Request $request, Closure $next)
    {

        $referencia = $request->session()->get('referencia');

        if (!$referencia ) {
            return redirect()->route('index');
        }

        return $next($request);
    }
}
