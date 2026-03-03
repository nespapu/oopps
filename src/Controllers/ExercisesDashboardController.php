<?php

namespace App\Controllers;

use App\Application\Auth\AuthService;
use App\Core\View;

final class ExercisesDashboardController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function show(): void
    {
        $this->authService->requireLogin();

        // View data
        $title = 'Exercises dashboard';
        $username = $this->authService->username() ?? '';

        $exercises = require __DIR__ . '/../../config/Ejercicios.php';

        View::render('panel-control-ejercicios/index.php', [
            'title' => $title,
            'usuario' => $username,
            'ejercicios' => $exercises,
        ]);
    }
}