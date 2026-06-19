<?php

namespace App\Controllers\HowMuchDoYouKnow;

use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\HowMuchDoYouKnow\Index\IndexEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Index\IndexPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\ExerciseStep;

final class IndexController
{
    public function __construct(
        private readonly ExerciseSessionStore $exerciseSessionStore,
        private readonly AuthService $authService,
        private readonly Paths $paths,
        private readonly IndexPayloadBuilder $payloadBuilder,
        private readonly IndexEvaluationService $evaluationService,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function show(): void
    {
        $this->authService->requireLogin();

        $session = $this->exerciseSessionStore->getCurrentSession();

        $payload = $this->payloadBuilder->build($session);

        $stepAnswer = $session->getStepAnswer(ExerciseStep::INDEX);

        $evaluation = $session->getStepEvaluation(ExerciseStep::INDEX);

        View::render('exercises/how-much-do-you-know/index', [
            'payload' => $payload,
            'sessionId' => $session->sessionId(),
            'stepAnswer' => $stepAnswer,
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

        $stepAnswer = $this->buildStepAnswerFromPost($payload, $_POST, ExerciseStep::INDEX->value);

        $evaluation = $this->evaluationService->evaluate($payload, $stepAnswer);

        $session->setStepAnswer(ExerciseStep::INDEX, $stepAnswer);

        $session->setStepEvaluation(ExerciseStep::INDEX, $evaluation);

        $this->exerciseSessionStore->save($session);
        
        $this->redirector->redirect($this->paths->indexStep($session->sessionId()));
    }

    private function buildStepAnswerFromPost(array $payload, array $postData, string $step): array
    {
        $values = [];

        $evaluable = $payload[StepPayloadKeys::META]['evaluable'] ?? [];
        $items = $payload[StepPayloadKeys::ITEMS] ?? [];

        foreach ($items as $item) {
            $itemKey = $item['key'] ?? null;

            if (!is_string($itemKey) || $itemKey === '') {
                continue;
            }

            $itemPost = $postData[$itemKey] ?? [];

            if (!is_array($itemPost)) {
                continue;
            }

            foreach ($itemPost as $fieldKey => $fieldValue) {
                if (($evaluable[$fieldKey] ?? false) !== true) {
                    continue;
                }

                $values[$itemKey . '.' . $fieldKey] = trim((string) $fieldValue);
            }
        }

        return [
            'step' => $step,
            'values' => $values,
        ];
    }
}