<?php

namespace App\Application\Exercises\HowMuchDoYouKnow\Tools;

use App\Application\Exercises\Evaluation\EvaluationMode;
use App\Application\Exercises\Evaluation\FieldResult;
use App\Application\Exercises\Evaluation\StepEvaluation;
use App\Application\Exercises\Evaluation\StepResult;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\SimilarityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;

final class ToolsEvaluationService
{
    public function __construct(
        private readonly EqualityEvaluator $equalityEvaluator,
        private readonly SimilarityEvaluator $similarityEvaluator
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

        $fieldConfig = $meta['fieldConfig'] ?? null;

        if (!is_array($fieldConfig)) {
            throw new \InvalidArgumentException('Invalid payload.meta.fieldConfig.');
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

            foreach ($fieldConfig as $fieldKey => $config) {
                if ($config['evaluable'] !== true) {
                    continue;
                }

                if (!array_key_exists($fieldKey, $expectedByItemKey[$itemKey])) {
                    throw new \LogicException("Missing expected value for field '{$itemKey}.{$fieldKey}'.");
                }

                $answerKey = $itemKey . '.' . $fieldKey;

                $expected = (string) $expectedByItemKey[$itemKey][$fieldKey];
                $actual = isset($values[$answerKey]) ? (string) $values[$answerKey] : '';

                $score = null;

                if ($config['evaluationMode'] === EvaluationMode::SIMILARITY) {
                    $score = $this->similarityEvaluator->compare($expected, $actual);
                }

                $isCorrect = match ($config['evaluationMode']) {
                    EvaluationMode::EQUALITY => $this->equalityEvaluator->evaluate($expected, $actual),
                    EvaluationMode::SIMILARITY => $score >= $config['threshold'],
                };

                $fieldResults[$answerKey] = new FieldResult(
                    $fieldKey,
                    $actual,
                    $isCorrect,
                    $config['evaluationMode'],
                    $score,
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