<?php

declare(strict_types=1);

namespace App\Application\Session;

interface SessionStore
{
    public function getString(string $key): ?string;

    public function setString(string $key, string $value): void;

    public function has(string $key): bool;

    public function clear(): void;

    public function startIfNeeded(): void;

    public function regenerateId(bool $deleteOldSession = true): void;

    public function destroy(): void;
}
