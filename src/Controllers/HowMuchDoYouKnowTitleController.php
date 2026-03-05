<?php

namespace App\Controllers;

use App\App\Routing\HowMuchDoYouKnowPaths;
use App\Application\Auth\AuthService;
use App\Application\Exercises\Evaluation\HowMuchDoYouKnowTitleEvaluationService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\Payload\StepPayloadKeys;
use App\Application\Exercises\StepBuilder\HowMuchDoYouKnowTitlePayloadBuilder;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\ExerciseStep;

final class HowMuchDoYouKnowTitleController
{
    public function __construct(
        private readonly ExerciseSessionStore $exerciseSessionStore,
        private readonly AuthService $authService,
        private readonly HowMuchDoYouKnowPaths $paths,
        private readonly HowMuchDoYouKnowTitlePayloadBuilder $payloadBuilder,
        private readonly HowMuchDoYouKnowTitleEvaluationService $evaluationService,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function show(): void
    {
        $this->authService->requireLogin();

        $session = $this->exerciseSessionStore->getCurrentSession();

        $payload = $this->payloadBuilder->build($session);
        $evaluation = $session->getStepEvaluation(ExerciseStep::TITLE);

        View::render('exercises/HowMuchDoYouKnowTitle', [
            'payload' => $payload,
            'sessionId' => $session->sessionId(),
            'evaluation' => $evaluation,
            'url' => $this->urlGenerator,
            'howMuchDoYouKnowPaths' => $this->paths,
        ]);
    }

    public function evaluate(): void
    {
        $this->authService->requireLogin();

        $session = $this->exerciseSessionStore->getCurrentSession();

        $payload = $this->payloadBuilder->build($session);

        $stepAnswer = $this->buildStepAnswerFromPost($payload, ExerciseStep::TITLE->value);

        $evaluation = $this->evaluationService->evaluate($payload, $stepAnswer);

        $session->setStepEvaluation(ExerciseStep::TITLE, $evaluation);
        $this->exerciseSessionStore->save($session);

        $this->redirector->redirect($this->paths->titleStep($session->sessionId()));
    }

    private function buildStepAnswerFromPost(array $payload, string $step): array
    {
        $values = [];

        foreach ($payload[StepPayloadKeys::ITEMS] as $item) {
            $key = $item['key'];
            $values[$key] = isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
        }

        return [
            'step' => $step,
            'values' => $values,
        ];
    }
}