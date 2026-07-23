<?php

namespace App\Controllers\HowMuchDoYouKnow;

use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\HowMuchDoYouKnow\Quotes\QuotesPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\ExerciseStep;

final class QuotesController
{
    public function __construct(
        private readonly ExerciseSessionStore $exerciseSessionStore,
        private readonly AuthService $authService,
        private readonly Paths $paths,
        private readonly QuotesPayloadBuilder $payloadBuilder,
        //private readonly JustificationEvaluationService $evaluationService,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function show(): void
    {
        $this->authService->requireLogin();

        $session = $this->exerciseSessionStore->getCurrentSession();

        $payload = $this->payloadBuilder->build($session);

        echo '<pre>';
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo '</pre>';
        die();

    }

    public function evaluate(): void
    {
    }


    private function buildStepAnswerFromPost(array $payload, array $postData, string $step): array
    {
        return [];
    }
}