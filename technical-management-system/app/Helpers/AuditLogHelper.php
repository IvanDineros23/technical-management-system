<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogHelper
{
    /**
     * Log an audit event.
     *
     * @param string $action
     * @param string $modelType
     * @param int|string|null $modelId
     * @param string|null $description
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param array|null $changedFields
     * @return void
     */
    public static function log($action, $modelType, $modelId = null, $description = null, $oldValues = null, $newValues = null, $changedFields = null)
    {
        $user = Auth::user();
        AuditLog::create([
            'user_id' => $user ? $user->id : null,
            'action' => strtoupper($action),
            'model_type' => $modelType,
            'model_id' => $modelId ?? 0,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'session_id' => session()->getId(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'description' => $description,
        ]);
    }
}
