<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthenticationActivity
{
    public function __construct(private readonly ActivityLogger $logger)
    {
    }

    /**
     * Universal Handler untuk Event Login & Logout
     */
    public function handle(mixed $event): void
    {
        if ($event instanceof Login) {
            $this->handleLogin($event);
        } elseif ($event instanceof Logout) {
            $this->handleLogout($event);
        }
    }

    /**
     * Handle a login event.
     */
    public function handleLogin(Login $event): void
    {
        $user = $event->user;

        if (! $user) {
            return;
        }

        $this->logger->log(
            action: 'login',
            description: $this->describe($user, 'berhasil masuk ke sistem'),
            subject: $user,
            user: $user,
        );
    }

    /**
     * Handle a logout event.
     */
    public function handleLogout(Logout $event): void
    {
        $user = $event->user;

        if (! $user) {
            return;
        }

        $this->logger->log(
            action: 'logout',
            description: $this->describe($user, 'telah keluar dari sistem'),
            subject: $user,
            user: $user,
        );
    }

    private function describe(mixed $user, string $verb): string
    {
        $identity = $user->name ?? $user->email ?? ('Pengguna #' . $user->getKey());

        return "{$identity} {$verb}.";
    }
}