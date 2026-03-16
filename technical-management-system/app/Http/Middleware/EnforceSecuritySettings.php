<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class EnforceSecuritySettings
{
    /**
     * Enforce runtime security settings such as session timeout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $settings = $this->getSecuritySettings();
        $timeoutMinutes = max(1, (int) ($settings['session_timeout'] ?? config('session.lifetime', 120)));

        $lastActivity = (int) $request->session()->get('security.last_activity', 0);
        $now = time();

        if ($lastActivity > 0 && ($now - $lastActivity) > ($timeoutMinutes * 60)) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('warning', 'Session expired due to inactivity. Please sign in again.');
        }

        $request->session()->put('security.last_activity', $now);

        return $next($request);
    }

    private function getSecuritySettings(): array
    {
        $defaults = [
            'session_timeout' => (int) config('session.lifetime', 120),
        ];

        try {
            if (!Storage::disk('local')->exists('security_settings.json')) {
                return $defaults;
            }

            $decoded = json_decode((string) Storage::disk('local')->get('security_settings.json'), true);
            if (!is_array($decoded)) {
                return $defaults;
            }

            return [
                'session_timeout' => (int) ($decoded['session_timeout'] ?? $defaults['session_timeout']),
            ];
        } catch (\Throwable $e) {
            return $defaults;
        }
    }
}
