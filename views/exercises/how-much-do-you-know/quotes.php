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

$items = $payload[StepPayloadKeys::ITEMS] ?? [];
$meta = $payload[StepPayloadKeys::META] ?? [];

$topicOrder = $meta['topicOrder'] ?? '';
$difficulty = $meta['difficulty'] ?? '';
$flags = is_array($meta['flags'] ?? null) ? $meta['flags'] : [];
$fieldConfig = is_array($meta['fieldConfig'] ?? null) ? $meta['fieldConfig'] : [];

$isFieldEvaluable = static fn(string $field): bool =>
    (bool)($fieldConfig[$field]['evaluable'] ?? false);

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

$action = $url->to($howMuchDoYouKnowPaths->quotesEvaluation($sessionId));
$nextUrl = $url->to($howMuchDoYouKnowPaths->toolsStep($sessionId));
$previousUrl = $url->to($howMuchDoYouKnowPaths->justificationStep($sessionId));

$e = static fn(mixed $value): string =>
    htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$fieldCssClass = static function (array $fieldResults, string $answerKey): string {
    $fieldResult = $fieldResults[$answerKey] ?? null;

    if (!is_array($fieldResult)) {
        return '';
    }

    return ($fieldResult['isCorrect'] ?? false) ? 'is-valid' : 'is-invalid';
};

$fieldData = static function (array $item, string $field): array {
    $data = $item[$field] ?? [];

    return is_array($data) ? $data : [];
};
?>

<div class="row justify-content-center">
  <div class="col-12 col-xl-11">

    <div class="d-flex align-items-start justify-content-between mb-3">
      <div>
        <h1 class="h4 mb-1">Cuánto sabes del tema</h1>
        <div class="text-muted">
          Paso: <span class="fw-semibold">Citas</span>
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
          <div class="alert alert-success d-flex align-items-center" role="alert">
            <div class="me-2">✅</div>
            <div><strong>¡Correcto!</strong> Puedes continuar al siguiente paso.</div>
          </div>
        <?php else: ?>
          <div class="alert alert-danger d-flex align-items-center" role="alert">
            <div class="me-2">❌</div>
            <div><strong>No es correcto.</strong> Revisa los datos y el contenido de las citas.</div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">

        <div class="mb-4">
          <h2 class="h5 mb-1">Completa las citas del tema</h2>
          <p class="text-muted mb-0">
            Cada tarjeta representa una cita. Completa los campos vacíos; los datos ya resueltos forman parte de la configuración elegida.
          </p>
        </div>

        <form method="post" action="<?= $e($action) ?>">

          <?php foreach ($items as $position => $quote): ?>
            <?php
              $quoteKey = (string)($quote['key'] ?? '');

              $concept = $fieldData($quote, 'quoteConcept');
              $author = $fieldData($quote, 'quoteAuthor');
              $year = $fieldData($quote, 'quoteYear');
              $content = $fieldData($quote, 'quoteContent');
              $sectionOrder = $fieldData($quote, 'quoteSectionOrder');
              $sectionTitle = $fieldData($quote, 'quoteSectionTitle');

              $fields = [
                  'quoteConcept' => ['label' => 'Concepto', 'data' => $concept],
                  'quoteAuthor' => ['label' => 'Autor', 'data' => $author],
                  'quoteYear' => ['label' => 'Año', 'data' => $year],
                  'quoteSectionOrder' => ['label' => 'Numeración', 'data' => $sectionOrder],
                  'quoteSectionTitle' => ['label' => 'Apartado', 'data' => $sectionTitle],
              ];
            ?>

            <section class="card mb-4 border">
              <div class="card-header bg-light d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <span aria-hidden="true">💬</span>
                  <h3 class="h6 mb-0">Cita <?= $e((int)$position + 1) ?></h3>
                </div>
              </div>

              <div class="card-body">
                <div class="row g-3 mb-4">
                  <?php foreach ($fields as $fieldName => $definition): ?>
                    <?php
                      $field = $definition['data'];
                      $label = $definition['label'];
                      $value = $field['value'] ?? '';
                      $hint = $field['hint'] ?? '';
                      $answerKey = $quoteKey . '.' . $fieldName;
                      $inputId = $quoteKey . '_' . $fieldName;
                      $cssClass = $fieldCssClass($fieldResults, $answerKey);
                      $columnClass = $fieldName === 'quoteSectionTitle'
                          ? 'col-12 col-lg-8'
                          : 'col-12 col-md-6 col-lg-4';
                    ?>

                    <div class="<?= $e($columnClass) ?>">
                      <label class="form-label fw-semibold" for="<?= $e($inputId) ?>">
                        <?= $e($label) ?>
                      </label>

                      <?php if ($isFieldEvaluable($fieldName)): ?>
                        <input
                          type="text"
                          class="form-control <?= $e($cssClass) ?>"
                          id="<?= $e($inputId) ?>"
                          name="<?= $e($quoteKey) ?>[<?= $e($fieldName) ?>]"
                          placeholder="<?= $e($hint) ?>"
                          value="<?= $e($stepAnswerValues[$answerKey] ?? '') ?>"
                          autocomplete="off"
                          required
                        >

                        <div class="alert alert-info mt-2 mb-0 py-2 <?= $hint === '' ? 'invisible' : '' ?>">
                          <span class="fw-semibold">Pista:</span>
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

                        <div class="alert alert-info mt-2 mb-0 py-2 invisible">
                          <span class="fw-semibold">Pista:</span>
                          &nbsp;
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>

                <?php
                  $contentValue = $content['value'] ?? '';
                  $contentHint = $content['hint'] ?? '';
                  $contentAnswerKey = $quoteKey . '.quoteContent';
                  $contentInputId = $quoteKey . '_quoteContent';
                  $contentCssClass = $fieldCssClass($fieldResults, $contentAnswerKey);
                ?>

                <div>
                  <label class="form-label fw-semibold" for="<?= $e($contentInputId) ?>">
                    Contenido de la cita
                  </label>

                  <?php if ($isFieldEvaluable('quoteContent')): ?>
                    <textarea
                      class="form-control <?= $e($contentCssClass) ?>"
                      id="<?= $e($contentInputId) ?>"
                      name="<?= $e($quoteKey) ?>[quoteContent]"
                      rows="5"
                      placeholder="<?= $e($contentHint) ?>"
                      required
                    ><?= $e($stepAnswerValues[$contentAnswerKey] ?? '') ?></textarea>

                    <div class="form-text">
                      No es necesario reproducir cada signo de puntuación exactamente: este campo se evalúa por similitud.
                    </div>

                    <div class="alert alert-info mt-2 mb-0 py-2 <?= $contentHint === '' ? 'invisible' : '' ?>">
                      <span class="fw-semibold">Pista:</span>
                      <?= $e($contentHint) ?>
                    </div>
                  <?php else: ?>
                    <textarea
                      class="form-control bg-light"
                      id="<?= $e($contentInputId) ?>"
                      rows="5"
                      readonly
                    ><?= $e($contentValue) ?></textarea>

                    <div class="alert alert-info mt-2 mb-0 py-2 invisible">
                      <span class="fw-semibold">Pista:</span>
                      &nbsp;
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </section>
          <?php endforeach; ?>

          <?php if ($flags !== []): ?>
            <div class="mb-4">
              <div class="small text-muted mb-2">Configuración activa</div>

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
            <a class="btn btn-outline-secondary" href="<?= $e($previousUrl) ?>">
              Volver
            </a>

            <?php if ($isStepCorrect === true): ?>
              <a class="btn btn-success" href="<?= $e($nextUrl) ?>">
                Continuar
              </a>
            <?php else: ?>
              <button type="submit" class="btn btn-primary">
                Comprobar
              </button>
            <?php endif; ?>
          </div>

        </form>
      </div>
    </div>

  </div>
</div>