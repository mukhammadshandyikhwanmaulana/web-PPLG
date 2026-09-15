<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLogger
{
    private const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'secret',
        'api_token',
        'authorization',
        'csrf_token',
        '_token',
    ];

    /**
     * Catat aktivitas pengguna ke dalam database.
     */
    public function log(
        string $action,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        ?User $user = null
    ): ActivityLog {
        $user ??= Auth::user();

        $req = request();
        $ip = app()->runningInConsole() ? '127.0.0.1' : ($req?->ip() ?? '127.0.0.1');
        $userAgent = app()->runningInConsole() ? 'CLI/Artisan' : ($req?->userAgent() ?? 'Unknown');

        return ActivityLog::create([
            'user_id'      => $user?->getKey(),
            'action'       => Str::limit($action, 100, ''),
            'description'  => Str::limit($description, 500, ''),
            'subject_type' => $subject?->getMorphClass(),
            'subject_id'   => $subject?->getKey(),
            'properties'   => $this->sanitize($properties),
            'ip_address'   => $ip,
            'user_agent'   => Str::limit($userAgent, 255, ''),
        ]);
    }

    /**
     * Sensor kunci/data sensitif sebelum dimasukkan ke kolom JSON.
     */
    private function sanitize(array $properties): array
    {
        foreach ($properties as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), self::SENSITIVE_KEYS, true)) {
                $properties[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $properties[$key] = $this->sanitize($value);
            } elseif ($value instanceof Model) {
                $properties[$key] = [
                    'class' => get_class($value),
                    'id'    => $value->getKey(),
                ];
            }
        }

        return $properties;
    }
}