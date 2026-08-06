<?php

namespace App\Controllers\HowMuchDoYouKnow;

use App\App\Routing\ExercisesDashboardPaths;
use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\Application\Auth\AuthService;
use App\Application\Routing\UrlGenerator;
use App\Core\View;

final class ResultController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly ExercisesDashboardPaths $dashboardPaths,
        private readonly Paths $paths,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function show(): void
    {
        $this->authService->requireOppositionContext();

        View::render('exercises/how-much-do-you-know/result', [
            'url' => $this->urlGenerator,
            'howMuchDoYouKnowPaths' => $this->paths,
            'exercisesDashboardPaths' => $this->dashboardPaths
        ]);
    }
}