<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('locale')) {
            App::setLocale(session('locale'));
        } elseif (auth()->check() && auth()->user()->locale) {
            App::setLocale(auth()->user()->locale);
            session(['locale' => auth()->user()->locale]);
        }
        
        return $next($request);
    }
}