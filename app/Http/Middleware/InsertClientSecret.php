<?php

namespace TravelCompanion\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class InsertClientSecret
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
        if ($request->has('username') && $request->has('password'))
        {
            $res = DB::select('SELECT * FROM oauth_clients WHERE id = ? LIMIT 0,1', [1]);

            $request->merge([
                'grant_type' => 'password',
                'client_id' => $res[0]->id,
                'client_secret' => $res[0]->secret,
            ]);
        }

        return $next($request);
    }
}
