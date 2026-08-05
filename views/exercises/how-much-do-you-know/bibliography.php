<?php

declare(strict_types=1);

/** @var array<string, mixed> $payload */
/** @var string $sessionId */
/** @var array<string, mixed>|null $stepAnswer */
/** @var array<string, mixed>|null $evaluation */
/** @var \App\Application\Routing\UrlGenerator $url */
/** @var \App\App\Routing\HowMuchDoYouKnow\Paths $howMuchDoYouKnowPaths */

use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;

$title = 'Cuánto sabes del tema';

$items = is_array($payload[StepPayloadKeys::ITEMS] ?? null)
    ? $payload[StepPayloadKeys::ITEMS]
    : [];

$meta = is_array($payload[StepPayloadKeys::META] ?? null)
    ? $payload[StepPayloadKeys::META]
    : [];

$topicOrder = $meta['topicOrder'] ?? '';
$difficulty = $meta['difficulty'] ?? '';

$flags = is_array($meta['flags'] ?? null)
    ? $meta['flags']
    : [];

$fieldConfig = is_array($meta['fieldConfig'] ?? null)
    ? $meta['fieldConfig']
    : [];

$stepAnswerValues = is_array($stepAnswer['values'] ?? null)
    ? $stepAnswer['values']
    : [];

$result = is_array($evaluation['result'] ?? null)
    ? $evaluation['result']
    : [];

$fieldResults = is_array($result['fieldResults'] ?? null)
    ? $result['fieldResults']
    : [];

$isStepCorrect = is_array($evaluation)
    ? ($result['isStepCorrect'] ?? null)
    : null;

$action = $url->to(
    $howMuchDoYouKnowPaths->bibliographyEvaluation($sessionId)
);

$nextUrl = $url->to(
    $howMuchDoYouKnowPaths->webgraphyStep($sessionId)
);

$previousUrl = $url->to(
    $howMuchDoYouKnowPaths->workContextStep($sessionId)
);

$e = static fn(mixed $value): string =>
    htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

$isFieldEvaluable = static fn(string $fieldName): bool =>
    (bool) ($fieldConfig[$fieldName]['evaluable'] ?? false);

$fieldCssClass = static function (
    array $fieldResults,
    string $answerKey
): string {
    $fieldResult = $fieldResults[$answerKey] ?? null;

    if (!is_array($fieldResult)) {
        return '';
    }

    return ($fieldResult['isCorrect'] ?? false)
        ? 'is-valid'
        : 'is-invalid';
};

$fieldDefinitions = [
    'bookAuthor' => [
        'label' => 'Autor',
        'columnClass' => 'col-12 col-md-6',
    ],
    'bookPublicationYear' => [
        'label' => 'Año de publicación',
        'columnClass' => 'col-12 col-md-6',
    ],
    'bookPublisher' => [
        'label' => 'Editorial',
        'columnClass' => 'col-12 col-md-6',
    ],
    'bookTitle' => [
        'label' => 'Título',
        'columnClass' => 'col-12 col-md-6',
    ],
];
?>

<div class="row justify-content-center">
  <div class="col-12 col-xl-11">

    <div class="d-flex align-items-start justify-content-between mb-3">
      <div>
        <h1 class="h4 mb-1">
          Cuánto sabes del tema
        </h1>

        <div class="text-muted">
          Paso:
          <span class="fw-semibold">Bibliografía</span>
        </div>
      </div>

      <div class="text-end">
        <span class="badge text-bg-primary me-1">
          Tema <?= $e($topicOrder) ?>
        </span>

        <?php if ($difficulty !== ''): ?>
          <span class="badge text-bg-secondary">
            Dificultad <?= $e($difficulty) ?>
          </span>
        <?php endif; ?>
      </div>
    </div>

    <div class="mb-3">
      <?php if ($isStepCorrect !== null): ?>

        <?php if ($isStepCorrect): ?>
          <div
            class="alert alert-success d-flex align-items-center"
            role="alert"
          >
            <div class="me-2">
              ✅
            </div>

            <div>
              <strong>¡Correcto!</strong>
              Puedes continuar al siguiente paso.
            </div>
          </div>
        <?php else: ?>
          <div
            class="alert alert-danger d-flex align-items-center"
            role="alert"
          >
            <div class="me-2">
              ❌
            </div>

            <div>
              <strong>No es correcto.</strong>
              Revisa los datos de las referencias bibliográficas.
            </div>
          </div>
        <?php endif; ?>

      <?php endif; ?>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">

        <div class="mb-4">
          <h2 class="h5 mb-1">
            Completa la bibliografía del tema
          </h2>

          <p class="text-muted mb-0">
            Cada tarjeta representa una referencia bibliográfica. Completa
            únicamente los campos que aparecen vacíos; los datos resueltos
            forman parte de la configuración elegida.
          </p>
        </div>

        <?php if ($items === []): ?>
          <div class="alert alert-warning mb-0" role="alert">
            No se han encontrado referencias bibliográficas para este tema.
          </div>
        <?php else: ?>
          <form method="post" action="<?= $e($action) ?>">

            <?php foreach ($items as $position => $book): ?>
              <?php
                if (!is_array($book)) {
                    continue;
                }

                $bookKey = (string) ($book['key'] ?? '');
              ?>

              <section class="card mb-4 border">
                <div
                  class="card-header bg-light d-flex align-items-center justify-content-between"
                >
                  <div class="d-flex align-items-center gap-2">
                    <span aria-hidden="true">
                      📚
                    </span>

                    <h3 class="h6 mb-0">
                      Referencia <?= $e((int) $position + 1) ?>
                    </h3>
                  </div>
                </div>

                <div class="card-body">
                  <div class="row g-4">

                    <?php foreach ($fieldDefinitions as $fieldName => $definition): ?>
                      <?php
                        if (!array_key_exists($fieldName, $book)) {
                            continue;
                        }

                        $field = is_array($book[$fieldName])
                            ? $book[$fieldName]
                            : [];

                        $label = (string) (
                            $definition['label'] ?? $fieldName
                        );

                        $columnClass = (string) (
                            $definition['columnClass'] ?? 'col-12'
                        );

                        $value = (string) (
                            $field['value'] ?? ''
                        );

                        $hint = (string) (
                            $field['hint'] ?? ''
                        );

                        $answerKey = $bookKey
                            . '.'
                            . $fieldName;

                        $inputId = $bookKey
                            . '_'
                            . $fieldName;

                        $cssClass = $fieldCssClass(
                            $fieldResults,
                            $answerKey
                        );

                        $isEvaluable = $isFieldEvaluable(
                            $fieldName
                        );

                        $recoveredValue = (string) (
                            $stepAnswerValues[$answerKey] ?? ''
                        );
                      ?>

                      <div class="<?= $e($columnClass) ?>">
                        <label
                          class="form-label fw-semibold"
                          for="<?= $e($inputId) ?>"
                        >
                          <?= $e($label) ?>
                        </label>

                        <?php if ($isEvaluable): ?>
                          <input
                            type="text"
                            class="form-control <?= $e($cssClass) ?>"
                            id="<?= $e($inputId) ?>"
                            name="<?= $e($bookKey) ?>[<?= $e($fieldName) ?>]"
                            placeholder="<?= $e($hint) ?>"
                            value="<?= $e($recoveredValue) ?>"
                            autocomplete="off"
                            required
                          >

                          <div
                            class="alert alert-info mt-2 mb-0 py-2 <?= $hint === '' ? 'invisible' : '' ?>"
                          >
                            <span class="fw-semibold">
                              Pista:
                            </span>

                            <?= $e($hint) ?>
                          </div>
                        <?php else: ?>
                          <input
                            type="text"
                            class="form-control bg-light"
                            id="<?= $e($inputId) ?>"
                            value="<?= $e($value) ?>"
                            readonly
                          >

                          <div
                            class="alert alert-info mt-2 mb-0 py-2 invisible"
                          >
                            <span class="fw-semibold">
                              Pista:
                            </span>

                            &nbsp;
                          </div>
                        <?php endif; ?>
                      </div>

                    <?php endforeach; ?>

                  </div>
                </div>
              </section>
            <?php endforeach; ?>

            <?php if ($flags !== []): ?>
              <div class="mb-4">
                <div class="small text-muted mb-2">
                  Configuración activa
                </div>

                <div class="d-flex flex-wrap gap-2">
                  <?php foreach ($flags as $key => $enabled): ?>
                    <?php if ($enabled): ?>
                      <span class="badge rounded-pill text-bg-light border">
                        ✅ <?= $e($key) ?>
                      </span>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

            <div class="d-flex justify-content-end gap-2">
              <a
                class="btn btn-outline-secondary"
                href="<?= $e($previousUrl) ?>"
              >
                Volver
              </a>

              <?php if ($isStepCorrect === true): ?>
                <a
                  class="btn btn-success"
                  href="<?= $e($nextUrl) ?>"
                >
                  Continuar
                </a>
              <?php else: ?>
                <button type="submit" class="btn btn-primary">
                  Comprobar
                </button>
              <?php endif; ?>
            </div>

          </form>
        <?php endif; ?>

      </div>
    </div>

  </div>
</div>