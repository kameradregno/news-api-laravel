<?php

namespace App\Http\Middleware;

use App\Models\comments;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PemilikComment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id_commentator = comments::findOrFail($request->id)->user_id;
        $user = Auth::user()->id;
        
        
        if ($id_commentator != $user) {
            return response()->json('anda bukan komentator dari postingan ini');
        }

        return $next($request);
    }
}
