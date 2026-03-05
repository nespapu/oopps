<?php

namespace App\Controllers\HowMuchDoYouKnow;

use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\Application\Auth\AuthService;;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\Title\TitleEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Title\TitlePayloadBuilder;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\ExerciseStep;

final class TitleController
{
    public function __construct(
        private readonly ExerciseSessionStore $exerciseSessionStore,
        private readonly AuthService $authService,
        private readonly Paths $paths,
        private readonly TitlePayloadBuilder $payloadBuilder,
        private readonly TitleEvaluationService $evaluationService,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function show(): void
    {
        $this->authService->requireLogin();

        $session = $this->exerciseSessionStore->getCurrentSession();

        $payload = $this->payloadBuilder->build($session);
        $evaluation = $session->getStepEvaluation(ExerciseStep::TITLE);

        View::render('exercises/how-much-do-you-know/title', [
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