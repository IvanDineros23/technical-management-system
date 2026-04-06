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
        $routeUri = $route?->uri() ?? $request->path();
        $routeName = $route?->getName() ?? $routeUri;

        // Login/logout are already handled with explicit audit entries.
        if ($request->is('login') || $request->is('logout')) {
            return $response;
        }

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
        $modelType = 'Unknown';
        $routeParameters = $route?->parameters() ?? [];
        
        // Try to extract model type from route parameter objects
        foreach ($routeParameters as $key => $value) {
            if (is_object($value) && isset($value->id)) {
                $modelType = class_basename($value);
                $modelId = (int) $value->id;
                break;
            }

            if (is_numeric($value)) {
                $modelId = (int) $value;
            }
        }
        
        // If model type not found from objects, try to extract from route name
        if ($modelType === 'Unknown') {
            $modelType = $this->extractModelTypeFromRoute($routeName, $routeUri);
        }

        $action = match ($request->method()) {
            'POST' => 'CREATE',
            'PUT', 'PATCH' => 'UPDATE',
            'DELETE' => 'DELETE',
            default => 'ACTION',
        };

        $changedFields = array_keys($payload);
        $humanFields = empty($changedFields) ? 'no payload fields' : implode(', ', $changedFields);
        $description = $this->buildDescription(
            action: $action,
            routeUri: $routeUri,
            routeName: $routeName,
            humanFields: $humanFields
        );

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => null,
            'new_values' => [
                'route' => $routeUri,
                'status_code' => $statusCode,
                'payload' => $payload,
            ],
            'changed_fields' => $changedFields,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'description' => $description,
        ]);

        return $response;
    }

    /**
     * Extract model type from route name or URI
     */
    private function extractModelTypeFromRoute(string $routeName, string $routeUri): string
    {
        // Map route names and patterns to model types (order matters - more specific first)
        $patterns = [
            // Job Orders
            'job-order' => 'JobOrder',
            'job_order' => 'JobOrder',
            'joborder' => 'JobOrder',
            'job-orders' => 'JobOrder',
            'job_orders' => 'JobOrder',
            'joborders' => 'JobOrder',
            'requests' => 'JobOrder',
            
            // Calibrations
            'calibration' => 'Calibration',
            'calibrations' => 'Calibration',
            
            // Certificates
            'certificate' => 'Certificate',
            'certificates' => 'Certificate',
            'cert' => 'Certificate',
            
            // Payments
            'payment' => 'Payment',
            'payments' => 'Payment',
            
            // Equipment
            'equipment' => 'Equipment',
            'equipments' => 'Equipment',
            
            // Assignments
            'assignment' => 'Assignment',
            'assignments' => 'Assignment',
            'assign' => 'Assignment',
            
            // Invoices
            'invoice' => 'Invoice',
            'invoices' => 'Invoice',
            
            // Accounting/Releases
            'accounting' => 'AccountingRelease',
            'release' => 'Release',
            'releases' => 'Release',
            
            // Signatory
            'signatory' => 'SignatoryApproval',
            'approval' => 'SignatoryApproval',
            
            // Attachments
            'attachment' => 'JobAttachment',
            'attachments' => 'JobAttachment',
            
            // Checklists
            'checklist' => 'ChecklistItem',
            'checklists' => 'ChecklistItem',
            'crew' => 'JobOrderCrewMember',
        ];

        // Check route name first
        $routeLower = strtolower($routeName);
        foreach ($patterns as $pattern => $modelType) {
            if (str_contains($routeLower, $pattern)) {
                return $modelType;
            }
        }

        // Try to extract from URI
        $uriLower = strtolower($routeUri);
        foreach ($patterns as $pattern => $modelType) {
            if (str_contains($uriLower, $pattern)) {
                return $modelType;
            }
        }

        // URI-based extraction (first meaningful path segment)
        $uriParts = explode('/', trim($routeUri, '/'));
        if (!empty($uriParts)) {
            $firstPart = strtolower($uriParts[0]);
            if ($firstPart && !in_array($firstPart, ['admin', 'marketing', 'technician', 'tech-head', 'signatory', 'accounting', 'customer', 'api', 'auth'])) {
                // Use the first part as a hint but still check patterns
                foreach ($patterns as $pattern => $modelType) {
                    if (str_contains($firstPart, $pattern)) {
                        return $modelType;
                    }
                }
            }
        }

        // Last resort: check second part of URI for meaningful segments
        if (count($uriParts) > 1) {
            $secondPart = strtolower($uriParts[1]);
            foreach ($patterns as $pattern => $modelType) {
                if (str_contains($secondPart, $pattern)) {
                    return $modelType;
                }
            }
        }

        return 'Unknown';
    }

    /**
     * Build human-readable audit description.
     */
    private function buildDescription(string $action, string $routeUri, string $routeName, string $humanFields): string
    {
        if ($routeName === 'verification.send') {
            return 'Resent verification link to the user\'s email address.';
        }

        return sprintf(
            '%s on %s via %s (fields: %s)',
            $action,
            $routeUri,
            $routeName,
            $humanFields
        );
    }
}
