<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Justification;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\Evaluation\FieldResult;
use App\Application\Exercises\Evaluation\StepEvaluation;
use App\Application\Exercises\Evaluation\StepResult;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;

final class JustificationEvaluationService
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
            throw new \InvalidArgumentException('Invalid justification payload structure.');
        }

        $evaluable = $meta['evaluable'] ?? null;

        if (!is_array($evaluable)) {
            throw new \InvalidArgumentException('Invalid payload.meta.evaluable.');
        }

        $expectedByCycleKey = $this->buildExpectedByCycleKey($expectedItems);

        $fieldResults = [];
        $isStepCorrect = true;

        foreach ($items as $cycle) {
            $cycleKey = $this->requireKey($cycle, 'cycle');

            if (!array_key_exists($cycleKey, $expectedByCycleKey)) {
                throw new \LogicException("Missing expected values for cycle '{$cycleKey}'.");
            }

            $expectedCycle = $expectedByCycleKey[$cycleKey];

            if (($evaluable['cycles'] ?? false) === true) {
                $this->evaluateField(
                    $fieldResults,
                    $isStepCorrect,
                    $cycleKey . '.name',
                    $values,
                    (string) ($expectedCycle['name'] ?? ''),
                    $cycle['evaluation']['mode']
                );
            }

            if (($evaluable['laws'] ?? false) === true) {
                $this->evaluateNestedFields(
                    $fieldResults,
                    $isStepCorrect,
                    $cycleKey,
                    $cycle['laws'] ?? [],
                    $expectedCycle['laws'] ?? [],
                    $values,
                    'law'
                );
            }

            if (($evaluable['modules'] ?? false) === true) {
                $this->evaluateNestedFields(
                    $fieldResults,
                    $isStepCorrect,
                    $cycleKey,
                    $cycle['modules'] ?? [],
                    $expectedCycle['modules'] ?? [],
                    $values,
                    'module'
                );
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

    private function evaluateNestedFields(
        array &$fieldResults,
        bool &$isStepCorrect,
        string $cycleKey,
        array $items,
        array $expectedItems,
        array $values,
        string $type
    ): void {
        $expectedByKey = $this->buildExpectedByKey($expectedItems);

        foreach ($items as $item) {
            $itemKey = $this->requireKey($item, $type);

            if (!array_key_exists($itemKey, $expectedByKey)) {
                throw new \LogicException("Missing expected values for '{$cycleKey}.{$itemKey}'.");
            }

            $answerKey = $cycleKey . '.' . $itemKey . '.name';

            $this->evaluateField(
                $fieldResults,
                $isStepCorrect,
                $answerKey,
                $values,
                (string) ($expectedByKey[$itemKey]['name'] ?? ''),
                $item['evaluation']['mode']
            );
        }
    }

    private function evaluateField(
        array &$fieldResults,
        bool &$isStepCorrect,
        string $answerKey,
        array $values,
        string $expected,
        EvaluationMode $mode
    ): void {
        $actual = isset($values[$answerKey]) ? (string) $values[$answerKey] : '';

        $isCorrect = $this->equalityEvaluator->evaluate($actual, $expected);

        $fieldResults[$answerKey] = new FieldResult(
            $answerKey,
            $actual,
            $isCorrect,
            $mode,
            null,
            $isCorrect ? null : 'Answer does not match the expected value.'
        );

        $isStepCorrect = $isStepCorrect && $isCorrect;
    }

    private function buildExpectedByCycleKey(array $expectedItems): array
    {
        return $this->buildExpectedByKey($expectedItems);
    }

    private function buildExpectedByKey(array $expectedItems): array
    {
        $expectedByKey = [];

        foreach ($expectedItems as $expectedItem) {
            $itemKey = $expectedItem['key'] ?? null;

            if (!is_string($itemKey) || $itemKey === '') {
                throw new \LogicException('Invalid expected item key.');
            }

            $expectedByKey[$itemKey] = $expectedItem;
        }

        return $expectedByKey;
    }

    private function requireKey(array $item, string $type): string
    {
        $key = $item['key'] ?? null;

        if (!is_string($key) || $key === '') {
            throw new \LogicException("Invalid {$type} key.");
        }

        return $key;
    }
}