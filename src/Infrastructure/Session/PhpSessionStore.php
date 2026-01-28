<?php

declare(strict_types=1);

namespace App\Infrastructure\Session;

use App\Application\Session\SessionStore;

final class PhpSessionStore implements SessionStore
{
    public function startIfNeeded(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getString(string $key): ?string
    {
        $this->startIfNeeded();
        $value = $_SESSION[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    public function setString(string $key, string $value): void
    {
        $this->startIfNeeded();
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        $this->startIfNeeded();
        return array_key_exists($key, $_SESSION) && $_SESSION[$key] !== null && $_SESSION[$key] !== '';
    }

    public function clear(): void
    {
        $this->startIfNeeded();
        $_SESSION = [];
    }

    public function regenerateId(bool $deleteOldSession = true): void
    {
        $this->startIfNeeded();
        session_regenerate_id($deleteOldSession);
    }

    public function destroy(): void
    {
        $this->startIfNeeded();
        session_destroy();
    }
}
