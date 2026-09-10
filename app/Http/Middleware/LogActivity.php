<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Fields to never store in activity logs (PII/secrets).
     */
    protected array $sensitiveFields = [
        'password', 'password_confirmation', 'current_password',
        'email', 'phone', 'phone_number', 'id_card',
        'message', 'remarks', 'response',
        'address', 'home_address',
        'api_key', 'secret', 'token', 'authorization',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->user()) {
            return $response;
        }

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        $this->log($request, $response);

        return $response;
    }

    private function log(Request $request, Response $response): void
    {
        try {
            $payload = $request->except(['_token', '_method']);

            foreach ($this->sensitiveFields as $field) {
                if (array_key_exists($field, $payload)) {
                    unset($payload[$field]);
                }
            }

            // Truncate overly large values
            foreach ($payload as $key => $value) {
                if (is_string($value) && strlen($value) > 200) {
                    $payload[$key] = substr($value, 0, 200) . '... [truncated]';
                }
            }

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => strtolower($request->method()),
                'module' => $request->route()?->getName(),
                'description' => $request->method().' '.$request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'new_data' => $payload,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to log activity: '.$e->getMessage());
        }
    }
}

