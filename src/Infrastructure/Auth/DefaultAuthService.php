<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Application\Auth\AuthService;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\Redirector;
use App\Application\Http\RequestContext;
use App\Application\Session\SessionStore;
use App\Domain\Auth\ContextoUsuario;

final class DefaultAuthService implements AuthService
{
    public function __construct(
        private readonly SessionStore $session,
        private readonly RequestContext $requestContext,
        private readonly Redirector $redirector,
        private readonly FlashMessenger $flash
    ) {}

    public function requireLogin(): void
    {
        if ($this->isLoggedIn()) {
            return;
        }

        if (!$this->session->has('siguiente_url')) {
            $this->session->setString('siguiente_url', $this->requestContext->path());
        }

        $this->redirector->redirect('login');
    }

    public function requireOppositionContext(): void
    {
        $this->requireLogin();

        $code = $this->oppositionCode();
        if ($code === null || $code === '') {
            $this->flash->set('Error', 'No tienes una oposición activa configurada.');
            $this->redirector->redirect('login');
        }
    }

    public function isLoggedIn(): bool
    {
        return ($this->username() ?? '') !== '';
    }

    public function username(): ?string
    {
        return $this->session->getString('usuario');
    }

    public function oppositionCode(): ?string
    {
        return $this->session->getString('codigoOposicion');
    }

    public function userContext(): ContextoUsuario
    {
        $this->requireOppositionContext();

        return new ContextoUsuario(
            (string) $this->username(),
            (string) $this->oppositionCode()
        );
    }

    public function logout(): void
    {
        $this->session->startIfNeeded();

        $this->session->clear();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        $this->session->destroy();

        $this->session->startIfNeeded();
        $this->session->regenerateId(true);

        $this->redirector->redirect('login');
    }
}
