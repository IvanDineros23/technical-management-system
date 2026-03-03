<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        if ($request->attributes->get('audit.explicit_logged', false) === true) {
            return $response;
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode >= 500) {
            return $response;
        }

        $route = $request->route();
        $routeName = $route?->getName() ?? 'unnamed.route';
        $routeUri = $route?->uri() ?? $request->path();

        $payload = $request->except([
            '_token',
            '_method',
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
        ]);

        $files = [];
        foreach ($request->allFiles() as $key => $fileValue) {
            if (is_array($fileValue)) {
                $files[$key] = array_map(function ($file) {
                    return method_exists($file, 'getClientOriginalName')
                        ? $file->getClientOriginalName()
                        : 'uploaded_file';
                }, $fileValue);
            } else {
                $files[$key] = method_exists($fileValue, 'getClientOriginalName')
                    ? $fileValue->getClientOriginalName()
                    : 'uploaded_file';
            }
        }

        if (!empty($files)) {
            $payload['uploaded_files'] = $files;
        }

        $modelId = 0;
        $routeParameters = $route?->parameters() ?? [];
        foreach ($routeParameters as $value) {
            if (is_object($value) && isset($value->id)) {
                $modelId = (int) $value->id;
                break;
            }

            if (is_numeric($value)) {
                $modelId = (int) $value;
                break;
            }
        }

        $action = match ($request->method()) {
            'POST' => 'CREATE',
            'PUT', 'PATCH' => 'UPDATE',
            'DELETE' => 'DELETE',
            default => 'ACTION',
        };

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $routeName,
            'model_id' => $modelId,
            'old_values' => null,
            'new_values' => [
                'route' => $routeUri,
                'status_code' => $statusCode,
                'payload' => $payload,
            ],
            'changed_fields' => array_keys($payload),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'description' => "Auto-audit: {$request->method()} {$routeUri} ({$routeName})",
        ]);

        return $response;
    }
}
