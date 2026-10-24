<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the language chosen with the language switcher (stored in the session).
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->hasSession() ? $request->session()->get('locale') : null;

        if (is_string($locale) && in_array($locale, config()->array('app.supported_locales'), true)) {
            app()->setLocale($locale);
        }

        Number::useLocale(app()->getLocale());

        return $next($request);
    }
}
