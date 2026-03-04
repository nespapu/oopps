<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseCatalog;
use App\Core\View;

final class ExercisesDashboardController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function show(): void
    {
        $this->authService->requireLogin();

        $username = $this->authService->username() ?? '';
        $exercises = ExerciseCatalog::all();

        View::render('exercises-dashboard/dashboard', [
            'username' => $username,
            'exercises' => $exercises,
        ]);
    }
}