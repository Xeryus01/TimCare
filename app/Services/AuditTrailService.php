<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Http\Request;

class AuditTrailService
{
    public function record(?Request $request, ?int $actorId, string $entityType, ?int $entityId, string $action, array $meta = []): Log
    {
        $context = [];

        if ($request) {
            $context = [
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'method' => $request->method(),
                'path' => $request->path(),
                'request_id' => $request->headers->get('X-Request-Id'),
            ];
        }

        return Log::create([
            'actor_id' => $actorId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'ip_address' => $context['ip'] ?? null,
            'user_agent' => $context['user_agent'] ?? null,
            'request_method' => $context['method'] ?? null,
            'request_path' => $context['path'] ?? null,
            'meta' => array_filter([
                'context' => $context,
                'data' => $this->redact($meta),
            ], fn ($value) => $value !== [] && $value !== null),
        ]);
    }

    private function redact(array $data): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'access_token', 'refresh_token', 'id_token', 'client_secret', 'secret'];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $data[$key] = $this->redact($value);
            }
        }

        return $data;
    }
}
