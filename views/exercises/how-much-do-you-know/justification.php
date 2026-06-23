<?php

/** @var array<string, mixed> $payload */
/** @var string $sessionId */
/** @var array<string, mixed>|null $stepAnswer */
/** @var array<string, mixed>|null $evaluation */
/** @var \App\Application\Routing\UrlGenerator $url */
/** @var \App\App\Routing\HowMuchDoYouKnow\Paths $howMuchDoYouKnowPaths */

use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;

$title = 'Cuánto sabes del tema';

$items = $payload[StepPayloadKeys::ITEMS] ?? [];
$meta  = $payload[StepPayloadKeys::META] ?? [];

$topicOrder = $meta['topicOrder'] ?? '';
$difficulty = $meta['difficulty'] ?? '';
$flags      = $meta['flags'] ?? [];
$evaluable  = $meta['evaluable'] ?? [];

$isCyclesEvaluable  = (bool)($evaluable['cycles'] ?? false);
$isLawsEvaluable    = (bool)($evaluable['laws'] ?? false);
$isModulesEvaluable = (bool)($evaluable['modules'] ?? false);

$stepAnswerValues = is_array($stepAnswer['values'] ?? null)
    ? $stepAnswer['values']
    : [];

$result = is_array($evaluation['result'] ?? null)
    ? $evaluation['result']
    : [];

$fieldResults = is_array($result['fieldResults'] ?? null)
    ? $result['fieldResults']
    : [];

$isStepCorrect = null;

if (is_array($evaluation)) {
    $isStepCorrect = $evaluation['result']['isStepCorrect'] ?? null;
}

$action = $url->to($howMuchDoYouKnowPaths->justificationEvaluation($sessionId));
$nextUrl = $url->to($howMuchDoYouKnowPaths->quotesStep($sessionId));

$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

$fieldCssClass = function (array $fieldResults, string $key): string {
    $result = $fieldResults[$key] ?? null;

    if (!is_array($result)) {
        return '';
    }

    return ($result['isCorrect'] ?? false) ? 'is-valid' : 'is-invalid';
};
?>

<div class="row justify-content-center">
  <div class="col-12 col-xl-10">

    <div class="d-flex align-items-start justify-content-between mb-3">
      <div>
        <h1 class="h4 mb-1">Cuánto sabes del tema</h1>
        <div class="text-muted">
          Paso: <span class="fw-semibold">Justificación</span>
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
      <?php if (isset($isStepCorrect)): ?>
        <?php if ($isStepCorrect): ?>
          <div class="alert alert-success d-flex align-items-center" role="alert">
            <div class="me-2">✅</div>
            <div><strong>¡Correcto!</strong> Puedes continuar al siguiente paso.</div>
          </div>
        <?php else: ?>
          <div class="alert alert-danger d-flex align-items-center" role="alert">
            <div class="me-2">❌</div>
            <div><strong>No es correcto.</strong> Revisa los ciclos, leyes y módulos.</div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">

        <div class="mb-4">
          <h2 class="h5 mb-1">Completa la justificación del tema</h2>
          <p class="text-muted mb-0">
            Rellena los campos que aparecen vacíos. Los campos ya resueltos forman parte de la configuración elegida.
          </p>
        </div>

        <form method="post" action="<?= $e($action) ?>">

          <?php foreach ($items as $cycle): ?>
            <?php
              $cycleKey = $cycle['key'] ?? '';
              $cycleName = $cycle['name'] ?? '';
              $cycleHint = $cycle['hint'] ?? '';
              $cycleInputId = $cycleKey . '_name';
              $cycleAnswerKey = $cycleKey . '.name';
              $cycleCssClass = $fieldCssClass($fieldResults, $cycleAnswerKey);
            ?>

            <div class="card mb-4 border">
              <div class="card-body">

                <div class="mb-4">
                  <label class="form-label fw-semibold" for="<?= $e($cycleInputId) ?>">
                    Ciclo
                  </label>

                  <?php if ($isCyclesEvaluable): ?>
                    <input
                      type="text"
                      class="form-control <?= $cycleCssClass ?>"
                      id="<?= $e($cycleInputId) ?>"
                      name="<?= $e($cycleKey) ?>[name]"
                      placeholder="<?= $e($cycleHint) ?>"
                      value="<?= $e($stepAnswerValues[$cycleAnswerKey] ?? '') ?>"
                      autocomplete="off"
                      required
                    >
                    <div class="alert alert-info mt-2 mb-0 py-2 <?= $cycleHint === '' ? 'invisible' : '' ?>">
                      <span class="fw-semibold">Pista:</span>
                      <?= $e($cycleHint) ?>
                    </div>
                  <?php else: ?>
                    <input
                      type="text"
                      class="form-control bg-light"
                      value="<?= $e($cycleName) ?>"
                      readonly
                    >
                    <div class="alert alert-info mt-2 mb-0 py-2 invisible">
                      <span class="fw-semibold">Pista:</span>
                      &nbsp;
                    </div>
                  <?php endif; ?>
                </div>

                <div class="mb-4">
                  <h3 class="h6 mb-3">Leyes</h3>

                  <?php foreach (($cycle['laws'] ?? []) as $law): ?>
                    <?php
                      $lawKey = $law['key'] ?? '';
                      $lawName = $law['name'] ?? '';
                      $lawHint = $law['hint'] ?? '';
                      $lawInputId = $cycleKey . '_' . $lawKey . '_name';
                      $lawAnswerKey = $cycleKey . '.' . $lawKey . '.name';
                      $lawCssClass = $fieldCssClass($fieldResults, $lawAnswerKey);
                    ?>

                    <div class="mb-3">
                      <?php if ($isLawsEvaluable): ?>
                        <label class="visually-hidden" for="<?= $e($lawInputId) ?>">
                          Ley
                        </label>

                        <input
                          type="text"
                          class="form-control <?= $lawCssClass ?>"
                          id="<?= $e($lawInputId) ?>"
                          name="<?= $e($cycleKey) ?>[<?= $e($lawKey) ?>][name]"
                          placeholder="<?= $e($lawHint) ?>"
                          value="<?= $e($stepAnswerValues[$lawAnswerKey] ?? '') ?>"
                          autocomplete="off"
                          required
                        >
                        <div class="alert alert-info mt-2 mb-0 py-2 <?= $lawHint === '' ? 'invisible' : '' ?>">
                          <span class="fw-semibold">Pista:</span>
                          <?= $e($lawHint) ?>
                        </div>
                      <?php else: ?>
                        <input
                          type="text"
                          class="form-control bg-light"
                          value="<?= $e($lawName) ?>"
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

                <div>
                  <h3 class="h6 mb-3">Módulos</h3>

                  <?php foreach (($cycle['modules'] ?? []) as $module): ?>
                    <?php
                      $moduleKey = $module['key'] ?? '';
                      $moduleName = $module['name'] ?? '';
                      $moduleHint = $module['hint'] ?? '';
                      $moduleInputId = $cycleKey . '_' . $moduleKey . '_name';
                      $moduleAnswerKey = $cycleKey . '.' . $moduleKey . '.name';
                      $moduleCssClass = $fieldCssClass($fieldResults, $moduleAnswerKey);
                    ?>

                    <div class="mb-3">
                      <?php if ($isModulesEvaluable): ?>
                        <label class="visually-hidden" for="<?= $e($moduleInputId) ?>">
                          Módulo
                        </label>

                        <input
                          type="text"
                          class="form-control <?= $moduleCssClass ?>"
                          id="<?= $e($moduleInputId) ?>"
                          name="<?= $e($cycleKey) ?>[<?= $e($moduleKey) ?>][name]"
                          placeholder="<?= $e($moduleHint) ?>"
                          value="<?= $e($stepAnswerValues[$moduleAnswerKey] ?? '') ?>"
                          autocomplete="off"
                          required
                        >
                        <div class="alert alert-info mt-2 mb-0 py-2 <?= $moduleHint === '' ? 'invisible' : '' ?>">
                          <span class="fw-semibold">Pista:</span>
                          <?= $e($moduleHint) ?>
                        </div>
                      <?php else: ?>
                        <input
                          type="text"
                          class="form-control bg-light"
                          value="<?= $e($moduleName) ?>"
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

              </div>
            </div>
          <?php endforeach; ?>

          <?php if (!empty($flags) && is_array($flags)): ?>
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

            <a class="btn btn-outline-secondary" href="<?= $e($url->to($howMuchDoYouKnowPaths->indexStep($sessionId))) ?>">
              Volver
            </a>

            <?php if (($isStepCorrect ?? null) === true): ?>
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