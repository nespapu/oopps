<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Index;

use App\Application\Exercises\Evaluation\FieldResult;
use App\Application\Exercises\Evaluation\StepEvaluation;
use App\Application\Exercises\Evaluation\StepResult;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;

final class IndexEvaluationService
{
    public function __construct(
        private readonly EqualityEvaluator $equalityEvaluator
    ) {}

    public function evaluate(array $payload, array $stepAnswer): StepEvaluation
    {
        if (($stepAnswer['step'] ?? null) !== ($payload[StepPayloadKeys::STEP] ?? null)) {
            throw new \InvalidArgumentException('Step mismatch.');
        }

        $values = $stepAnswer['values'] ?? null;

        if (!is_array($values)) {
            throw new \InvalidArgumentException('Invalid stepAnswer.values.');
        }

        $items = $payload[StepPayloadKeys::ITEMS] ?? null;
        $expectedItems = $payload[StepPayloadKeys::EXPECTED] ?? null;
        $meta = $payload[StepPayloadKeys::META] ?? null;

        if (!is_array($items) || !is_array($expectedItems) || !is_array($meta)) {
            throw new \InvalidArgumentException('Invalid index payload structure.');
        }

        $evaluable = $meta['evaluable'] ?? null;

        if (!is_array($evaluable)) {
            throw new \InvalidArgumentException('Invalid payload.meta.evaluable.');
        }

        $expectedByItemKey = $this->buildExpectedByItemKey($expectedItems);

        $fieldResults = [];
        $isStepCorrect = true;

        foreach ($items as $item) {
            $itemKey = $item['key'] ?? null;

            if (!is_string($itemKey) || $itemKey === '') {
                throw new \LogicException('Invalid item key.');
            }

            if (!array_key_exists($itemKey, $expectedByItemKey)) {
                throw new \LogicException("Missing expected values for item '{$itemKey}'.");
            }

            foreach ($evaluable as $fieldKey => $isEvaluable) {
                if ($isEvaluable !== true) {
                    continue;
                }

                if (!array_key_exists($fieldKey, $expectedByItemKey[$itemKey])) {
                    throw new \LogicException("Missing expected value for field '{$itemKey}.{$fieldKey}'.");
                }

                $answerKey = $itemKey . '.' . $fieldKey;

                $expected = (string) $expectedByItemKey[$itemKey][$fieldKey];
                $actual = isset($values[$answerKey]) ? (string) $values[$answerKey] : '';

                $isCorrect = $this->equalityEvaluator->evaluate($actual, $expected);

                $fieldResults[$answerKey] = new FieldResult(
                    $fieldKey,
                    $actual,
                    $isCorrect,
                    $item['evaluation']['mode'],
                    null,
                    $isCorrect ? null : 'Answer does not match the expected value.'
                );

                $isStepCorrect = $isStepCorrect && $isCorrect;
            }
        }

        $stepResult = new StepResult(
            step: $payload[StepPayloadKeys::STEP],
            fieldResults: $fieldResults,
            isStepCorrect: $isStepCorrect,
            score: $isStepCorrect ? 1.0 : 0.0
        );

        return StepEvaluation::now($stepResult);
    }

    private function buildExpectedByItemKey(array $expectedItems): array
    {
        $expectedByItemKey = [];

        foreach ($expectedItems as $expectedItem) {
            $itemKey = $expectedItem['key'] ?? null;

            if (!is_string($itemKey) || $itemKey === '') {
                throw new \LogicException('Invalid expected item key.');
            }

            $expectedByItemKey[$itemKey] = $expectedItem;
        }

        return $expectedByItemKey;
    }
}