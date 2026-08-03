<?php

namespace App\Controllers\HowMuchDoYouKnow;

use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Exercises\HowMuchDoYouKnow\WorkContext\WorkContextEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\WorkContext\WorkContextPayloadBuilder;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\ExerciseStep;

final class WorkContextController
{
    public function __construct(
        private readonly ExerciseSessionStore $exerciseSessionStore,
        private readonly AuthService $authService,
        private readonly Paths $paths,
        private readonly WorkContextPayloadBuilder $payloadBuilder,
        private readonly WorkContextEvaluationService $evaluationService,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function show(): void
    {
        $this->authService->requireLogin();

        $session = $this->exerciseSessionStore->getCurrentSession();

        $payload = $this->payloadBuilder->build($session);

        $stepAnswer = $session->getStepAnswer(ExerciseStep::WORK_CONTEXT);
        
        $evaluation = $session->getStepEvaluation(ExerciseStep::WORK_CONTEXT);

        View::render('exercises/how-much-do-you-know/work-context', [
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
        
        $stepAnswer = $this->buildStepAnswerFromPost($payload, $_POST, ExerciseStep::WORK_CONTEXT->value);        

        $evaluation = $this->evaluationService->evaluate($payload, $stepAnswer);
        
        $session->setStepAnswer(ExerciseStep::WORK_CONTEXT, $stepAnswer);
        
        $session->setStepEvaluation(ExerciseStep::WORK_CONTEXT, $evaluation);
        
        $this->exerciseSessionStore->save($session);
        
        $this->redirector->redirect($this->paths->workContextStep($session->sessionId()));       
    }


    private function buildStepAnswerFromPost(array $payload, array $postData, string $step): array
    {
        $values = [];

        $fieldConfig = $payload[StepPayloadKeys::META]['fieldConfig'] ?? [];
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
                if (($fieldConfig[$fieldKey]['evaluable'] ?? false) !== true) {
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