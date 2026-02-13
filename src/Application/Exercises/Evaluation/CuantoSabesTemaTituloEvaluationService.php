<?php

namespace App\Application\Exercises\Evaluation;

final class CuantoSabesTemaTituloEvaluationService
{
    public function __construct(
        private readonly EqualityEvaluator $equalityEvaluator
    ){}

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
        if (($stepAnswer['step'] ?? null) !== ($payload['step'] ?? null)) {
            throw new \InvalidArgumentException('Step mismatch.');
        }

        $values = $stepAnswer['values'] ?? null;
        if (!is_array($values)) {
            throw new \InvalidArgumentException('Invalid stepAnswer.values.');
        }

        $fieldResults = [];
        $isStepCorrect = true;

        foreach ($payload['items'] as $item) {
            $fieldKey = $item['key'] ?? null;
            if (!is_string($fieldKey) || $fieldKey === '') {
                throw new \LogicException('Invalid item key.');
            }

            if (!array_key_exists($fieldKey, $payload['expected'])) {
                throw new \LogicException("Missing expected value for field '{$fieldKey}'.");
            }

            $expected = $payload['expected'][$fieldKey];
            $actual   = isset($values[$fieldKey]) ? (string) $values[$fieldKey] : '';

            $fieldResult = $this->equalityEvaluator->evaluate($fieldKey, $actual, $expected);

            $fieldResults[$fieldKey] = $fieldResult;
            $isStepCorrect = $isStepCorrect && $fieldResult->isCorrect;
        }

        $stepResult = new StepResult(
            step: $payload['step'],
            fieldResults: $fieldResults,
            isStepCorrect: $isStepCorrect,
            score: $isStepCorrect ? 1.0 : 0.0
        );

        return StepEvaluation::now($stepResult);
    }

}
?>