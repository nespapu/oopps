<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Title;

use App\Application\Exercises\Evaluation\FieldResult;
use App\Application\Exercises\Evaluation\StepEvaluation;
use App\Application\Exercises\Evaluation\StepResult;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;

final class TitleEvaluationService
{
    public function __construct(
        private readonly EqualityEvaluator $equalityEvaluator
    ) {}

    /**
     * @phpstan-type StepPayload array{
     *     step: string,
     *     items: array<int, array{key: string, label?: string, hint?: string|null}>,
     *     expected: array<string, string>
     * }
     *
     * @phpstan-type StepAnswer array{
     *     step: string,
     *     values: array<string, string>
     * }
     *
     * @param StepPayload $payload
     * @param StepAnswer $stepAnswer
     */
    public function evaluate(array $payload, array $stepAnswer): StepEvaluation
    {
        if (($stepAnswer['step'] ?? null) !== ($payload[StepPayloadKeys::STEP] ?? null)) {
            throw new \InvalidArgumentException('Step mismatch.');
        }

        $values = $stepAnswer['values'] ?? null;
        if (!is_array($values)) {
            throw new \InvalidArgumentException('Invalid stepAnswer.values.');
        }

        $fieldResults = [];
        $isStepCorrect = true;

        foreach ($payload[StepPayloadKeys::ITEMS] as $item) {
            $fieldKey = $item['key'] ?? null;
            if (!is_string($fieldKey) || $fieldKey === '') {
                throw new \LogicException('Invalid item key.');
            }

            if (!array_key_exists($fieldKey, $payload[StepPayloadKeys::EXPECTED])) {
                throw new \LogicException("Missing expected value for field '{$fieldKey}'.");
            }

            $expected = $payload[StepPayloadKeys::EXPECTED][$fieldKey];
            $actual = isset($values[$fieldKey]) ? (string) $values[$fieldKey] : '';

            $isCorrect = $this->equalityEvaluator->evaluate($actual, $expected);

            $fieldResult = new FieldResult(
                $fieldKey,
                $actual,
                $isCorrect,
                $item['evaluation']['mode'],
                null,
                $isCorrect ? null : 'Answer does not match the expected value.'
            );

            $fieldResults[$fieldKey] = $fieldResult;
            $isStepCorrect = $isStepCorrect && $fieldResult->isCorrect;
        }

        $stepResult = new StepResult(
            step: $payload[StepPayloadKeys::STEP],
            fieldResults: $fieldResults,
            isStepCorrect: $isStepCorrect,
            score: $isStepCorrect ? 1.0 : 0.0
        );

        return StepEvaluation::now($stepResult);
    }
}