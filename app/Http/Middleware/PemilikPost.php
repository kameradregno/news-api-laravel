<?php

namespace App\Http\Middleware;

use App\Models\posts;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PemilikPost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id_author = posts::findOrFail($request->id);
        $user = Auth::user();
        // dd($id_author);
        

        if ($id_author->author != $user->id) {
            return response()->json('anda bukan pemilik postingan');
        }


        return $next($request);
    }
}
