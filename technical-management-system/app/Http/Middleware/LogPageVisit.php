<?php

namespace App\Http\Middleware;

use App\Helpers\AuditLogHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogPageVisit
{
    /**
     * Pages to log visits for
     */
    protected array $loggedPages = [
        'admin.dashboard' => 'Dashboard',
        'admin.users.index' => 'Users',
        'admin.equipment.index' => 'Equipment',
        'admin.inventory.index' => 'Inventory',
        'admin.accounting.index' => 'Accounting',
        'admin.audit-logs.index' => 'Audit Logs',
        'tech-head.dashboard' => 'Dashboard',
        'tech-head.inventory.index' => 'Inventory',
        'technician.dashboard' => 'Dashboard',
        'technician.inventory' => 'Inventory',
        'accounting.dashboard' => 'Dashboard',
        'accounting.invoices' => 'Invoices',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log GET requests that result in 200 OK
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $routeName = $request->route()?->getName();
            
            if ($routeName && isset($this->loggedPages[$routeName])) {
                AuditLogHelper::log(
                    action: 'VIEW',
                    modelType: 'Page',
                    modelId: 0,
                    description: "Viewed {$this->loggedPages[$routeName]} page"
                );
            }
        }

        return $response;
    }
}
